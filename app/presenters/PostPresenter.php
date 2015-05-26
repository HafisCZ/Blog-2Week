<?php

namespace App\Presenters;

use Nette, 
  Nette\Application\UI\Form;
    
class PostPresenter extends BasePresenter
{

  private $database;
  
  const _DEFAULT_AVATAR_ = "http://localhost/www/img/default_av.png";
  const _WEB_ = "http://localhost/www"; 
  
  public function __construct(Nette\Database\Context $database)
  {
    $this->database = $database;
  }
  
  public function renderShow($postId) 
  {
    $post = $this->database->table('posts')->get($postId);
    if (!$post) {
      $this->error('Příspěvek nenalezen.');
    }
  
    $this->template->post = $post;
    $this->template->comments = $post->related('comments')->order('created_at');
  }
  
  public function renderAuthor($username)
  {
    $author = $this->database->table('users')->get($username);
    if (!$author) {
      $this->error('Autor nenalezen.');
    }
    
    $this->template->author = $author;
    $this->template->posts = $author->related('posts')->order('created_at');
    $this->template->comments = $author->related('comments')->order('created_at');
  }
  
  public function currentIp()
  {
    return getenv('HTTP_CLIENT_IP')?:
      getenv('HTTP_X_FORWARDED_FOR')?:
      getenv('HTTP_X_FORWARDED')?:
      getenv('HTTP_FORWARDED_FOR')?:
      getenv('HTTP_FORWARDED')?:
      getenv('REMOTE_ADDR');
  }
  
  protected function createComponentCommentForm()
  {
    $form = new Form;
    
    $identity = $this->getUser()->getIdentity();
    $form->addText('name', 'Jméno:')->setRequired()->setValue((($this->getUser()->isLoggedIn()) ? $identity->username : NULL))->setAttribute('readonly');
    $form->addText('email', 'Email:')->setRequired()->setValue((($this->getUser()->isLoggedIn()) ? $identity->email : NULL))->addRule(Form::EMAIL, 'Zadejte platný email.')->setAttribute('readonly');
    $form->addTextArea('content', 'Komentář:')->setRequired();
    $form->addSubmit('send', 'Zveřejnit komentář');
  
    $form->onSuccess[] = array ($this, 'commentFormSucceeded');
  
    return $form;
  }
  
  public function commentFormSucceeded($form)
  {
    $values = $form->values;
    
    if (!$this->getUser()->isLoggedIn()) {
      $this->error('Musíte se přihlásit.');
    }
  
    $postId = $this->getParameter('postId');
    
    $this->database->table('comments')->insert(array(
      'post_id' => $postId,
      'username' => ($this->getUser()->isLoggedIn()) ? $this->getUser()->getIdentity()->username : $values->name,
      'email' => ($this->getUser()->isLoggedIn()) ? $this->getUser()->getIdentity()->email : $values->email,
      'content' => $values->content,
      'ip' => self::currentIp(),
      ));
    
    $this->flashMessage('Komentář přidán.', 'success');
    $this->redirect('this');
    
  }
  
  protected function createComponentPostForm()
  {
    $form = new Form;
    
    $form->addText('title', 'Titulek:')->setRequired();
    $form->addText('subtitle', 'Podtitulek:')->addRule(Form::MAX_LENGTH, 'Podtitulek je příliš dlouhý', 250);
    $form->addText('username', 'Autor')->setRequired()->setValue((($this->getUser()->isLoggedIn()) ? $this->getUser()->getId() : NULL))->setAttribute('readonly');
    $form->addUpload('link', 'Obrázek:');     
    $form->addTextArea('content', 'Obsah:')->setRequired()->addRule(Form::MAX_LENGTH, 'Komentář je příliš dlouhý.', 500);
    $form->addSubmit('send','Publikovat');
    
    $form->onSuccess[] = array($this, 'postFormSucceeded');
    
    return $form;
  }
  
  public function fulltextSearchFormSucceeded($form) {
    $string = $form->values->s;
    
    $relevant = $this->database->table('posts')->where('title LIKE ?', $string);
    $this->flashMessage("Nalezeno ".count($relevant)." příspěvků");  
    $this->template->relevant = $relevant;
  }
  
  public function postFormSucceeded($form, $values)
  {
    if (!$this->getUser()->isLoggedIn()) {
      $this->error('Musíte se přihlásit.');
    }
  
    $postId = $this->getParameter('postId');
    
    $imageLink;
    if ($values['link']->isOk()) {
      $filename = $values->link->getSanitizedName();
      $targetPath = $this->context->parameters['wwwDir'] . '\\images';
      $values['link']->move($targetPath . "\\" . $filename );
      $imageLink = PostPresenter::_WEB_ . "/images/" . $filename;   
    }
    
    $values['ip'] = self::currentIp();
    $values['link'] = ($values['link']->isOk()) ? $imageLink : NULL;
    if ($values['username'] == 'Neznámý uživatel') {
      $values['username'] = NULL;
    } else {                                          
      $values['username'] = $this->getUser()->getIdentity()->username;
    }
    
    if ($postId) {
      if ($values['link'] == NULL) {
        unset($values['link']);
      }
      $post = $this->database->table('posts')->get($postId);
      $post->update($values);
    } else {      
      $post = $this->database->table('posts')->insert($values);
    }
    
    self::makeFeed($this->database);
    self::sendNotifications();
    
    $this->flashMessage("Příspěvek publikován.", 'success');  
    $this->redirect('show', $post->id);
  }
  
  public function actionList()
  {
    $this->template->posts = $this->database->table('posts')->order('created_at DESC');  
  }
  
  public function actionEdit($postId)
  {
    if (!$this->getUser()->isLoggedIn()) {
      $this->redirect('Sign:in');	
    }
  
    $post = $this->database->table('posts')->where('id = ?', $postId)->fetch()->toArray();
    
    if (!$post) {
      $this->error('Příspěvek nebyl nalezen');
    }
    
    if ($post['username'] == NULL) {
      $post['username'] = 'Neznámý uživatel';
    }
    
    $this['postForm']->setDefaults($post);
  }
  
  private function sendNotifications()
  {
    
  }
  
  public function actionRemove($postId)
  {
    if (!$this->getUser()->isLoggedIn()) {
      $this->redirect('Sign:in');
    }
    
    $post = $this->database->table('posts')->where('id = ?', $postId)->fetch();
    $post->delete();
    
    self::makeFeed($this->database);
    
    $this->flashMessage("Příspěvek smazán.", 'success');  
    $this->redirect('Homepage:');
  }
  
  public function actionRemoveComment($commentId)
  {
    if (!$this->getUser()->isLoggedIn()) {
      $this->redirect('Sign:in');	
    }

    $comment = $this->database->table('comments')->where('id = ?', $commentId)->fetch();
    $postId = $comment->post_id;
    $comment->delete();
    
    $this->flashMessage("Komentář smazán", 'success');
    $this->redirect('Post:show', Array('postId' => $postId));
  }
  
  protected function createComponentFulltextSearchForm()
  {
    $form = new Form;
    
    $form->addText('s', 'Výraz:');
    $form->addSubmit('send', 'Hledat');
    
    $form->onSuccess[] = array ($this, 'fulltextSearchFormSucceeded');
    return $form;
  }
  
  public static function makeFeed($database) {
    $posts = $database->table('posts')->order('created_at DESC')->limit(3);
    
    if (count($posts) == 0) {
      echo ("No posts available for Rss feed creation !");
      return;
    }
    
    $feed = fopen("feed.xml", "w");
    fwrite($feed, "<?xml version='1.0' encoding='UTF-8'?>");
    fwrite($feed, "<rss version='2.0'>");
    fwrite($feed, "<channel>");
    fwrite($feed, "<title>Můj blog</title>");
    fwrite($feed, "<link>http://localhost/www/</link>");
    fwrite($feed, "<description>Nové příspěvky</description>");
    foreach ($posts as $post) {
      fwrite($feed, "<item>");
      fwrite($feed, "<title>" . $post->title . "</title>");
      fwrite($feed, "<link>" . self::_WEB_ . "/post/show?postId=" . $post->id . "</link>");
      fwrite($feed, "<description>" . $post->subtitle . "</description>");
      fwrite($feed, "</item>");
    }
    fwrite($feed, "</channel>");
    fwrite($feed, "</rss>");
  }
  
  public static function randomColor()
  {
    $rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
    $color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];
    return $color;
  }
}