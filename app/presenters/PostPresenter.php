<?php

namespace App\Presenters;

use Nette, 
  Nette\Application\UI\Form,
  Nette\Utils\Strings,
  Nette\Utils\Random;
    
class PostPresenter extends BasePresenter
{

  private $database;
  
  //{if ($user->getIdentity()->avatar != NULL}<img src="{$user->getIdentity()->avatar}"/>{/if}
  
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
    
    $this->template->edited = $this->database->table('posts')->where('editor = ?', $username);
    
    $this->template->author = $author;
    $this->template->posts = $author->related('posts')->order('created_at DESC');
    $this->template->comments = $author->related('comments')->order('created_at DESC');
    
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
    $form->addText('name', 'Jméno:')->setRequired()->setValue((($this->getUser()->isLoggedIn()) ? $identity->username : NULL));
    $form->addText('email', 'Email:')->setRequired()->setValue((($this->getUser()->isLoggedIn()) ? $identity->email : NULL))->addRule(Form::EMAIL, 'Zadejte platný email.');
    $form->addTextArea('content', 'Komentář:')->setRequired();
    $form->addSubmit('send', 'Zveřejnit komentář');
  
    $form->onSuccess[] = array ($this, 'commentFormSucceeded');
  
    return $form;
  }
  
  public function commentFormSucceeded($form)
  {
    $values = $form->values;
    $postId = $this->getParameter('postId');
    
    $comment = array (
      'post_id' => $postId,
      'content' => $values->content,
      'ip' => self::currentIp(),
    );
    if ($this->getUser()->isLoggedIn()) {
      $identity = $this->getUser()->getIdentity();
      $comment['username'] = $identity->username;
      $comment['email'] = $identity->email;
    } else {
      $comment['visitor'] = $values->name;
      $comment['email'] = $values->email;
    }
    
    $this->database->table('comments')->insert($comment);
    
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
    $form->addTextArea('content', 'Obsah:')->setRequired();//->addRule(Form::MAX_LENGTH, 'Obsah je příliš dlouhý.', 1500);
    $form->addSubmit('send','Publikovat');
    
    $form->onSuccess[] = array($this, 'postFormSucceeded');
    
    return $form;
  }
  
  public function fulltextSearchFormSucceeded($form) {
    $string = $form->values->s;
    $depth = $form->values->d;
    
    $relevant = array();
    
    $posts = $this->database->table('posts');
    foreach ($posts as $post) {
      $string1 = $post['title'];      	
      $string2 = $post['subtitle'];
      if (strlen($string) > 0 && (strpos($string1, $string) !== false || ($depth == 1 && $string2 != NULL && strpos($string2, $string) !== false))) {
        $relevant[] = $post;  
      }
    }
    
    $this->flashMessage("Nalezeno ".count($relevant)." příspěvků");  
    $this->template->relevant = $relevant;
  }
  
  public function actionPopulate()
  {
    if (!($this->getUser()->isLoggedIn() && ($this->getUser()->getIdentity()->rules === 1))) {
      $this->error('User doesnt have required permissions for this task to execute'); 	
    }
    $database = $this->database;
    $count = 5;
  
    for ($i = 0; $i <= $count ; $i++) {
      $commentCount = Random::generate('1', '0-5'); 
      $postTitle = Random::generate('15');
      $post = $database->table('posts')->insert(array(
        'title' => $postTitle,
        'username' => NULL,
        'subtitle' => Random::generate('35'),
        'content' => Random::generate('200'),
      ));
      $id = $database->table('posts')->where('title = ?', $postTitle)->fetch()->id;
      for ($y = 0; $y <= $commentCount; $y++) {
        $commentEmail = Random::generate(8, 'a-z') . "@text.tx";  
        $comment = $database->table('comments')->insert(array(
          'post_id' => $id,
          'visitor' => Random::generate(8),
          'email' => Random::generate(5),
          'content' => Random::generate('100'),
        ));	
      }	
    }
    $this->flashMessage('Task completed');    
    $this->redirect('Homepage:');
  }
  
  public function postFormSucceeded($form, $values)
  {
    if (!$this->getUser()->isLoggedIn()) {
      $this->error('Musíte se přihlásit.');
    }
  
    $postId = $this->getParameter('postId');
    
    $imageLink;
    if ($values['link']->isOk()) {
      $rnd = Random::generate(10);
      $filename = $values->link->getSanitizedName();
      $targetPath = $this->context->parameters['wwwDir'] . '\\images';
      $values['link']->move($targetPath . "\\" . $rnd  . "_" . $filename );
      $imageLink = PostPresenter::_WEB_ . "/images/". $rnd  . "_" . $filename;   
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
      unset($values['username']);
      $values['editor'] = $this->getUser()->getIdentity()->username;
      $post = $this->database->table('posts')->get($postId);
      $post->update($values);
    } else {      
      $post = $this->database->table('posts')->insert($values);
    }
    
    self::makeFeed($this->database);
    
    $this->flashMessage("Příspěvek publikován.", 'success');  
    $this->redirect('show', $post->id);
  }
  
  public function actionList()
  {
    $this->template->posts = $this->database->table('posts')->order('created_at DESC');  
  }
  
  public function actionPop()
  {
    self::populate( $this->database, 5);
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
  
  public function actionRemove($postId)
  {
    if (!$this->getUser()->isLoggedIn()) {
      $this->redirect('Sign:in');
    }
    
    $post = $this->database->table('posts')->where('id = ?', $postId)->fetch();
    
    $identity = $this->getUser()->getIdentity();
    if ($identity->username === $post->username || $identity->rules == '1') {
      $post->delete();
    
      self::makeFeed($this->database);
      
      $this->flashMessage("Příspěvek smazán.", 'success');  
      $this->redirect('Homepage:');	
    } else {
      $this->flashMessage('K provedení operace nemáte dostatečná oprávnění !');
      $this->redirect('Homepage:');
    }
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
    $form->addCheckbox('d', 'Hledat v popisech:');
    $form->addSubmit('send', 'Hledat');
    
    $form->onSuccess[] = array ($this, 'fulltextSearchFormSucceeded');
    return $form;
  }
  
  public static function makeFeed($database) {
    $posts = $database->table('posts')->order('created_at DESC')->limit(5);
    
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
    return "#" . Random::generate('6', '0-9a-f');
  }
}