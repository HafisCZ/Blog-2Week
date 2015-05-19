<?php

namespace App\Presenters;

use Nette, Nette\Application\UI\Form;
    
class PostPresenter extends BasePresenter
{

  private $database;
  
  public function __construct(Nette\Database\Context $database)
  {
    $this->database = $database;
  }
  
  public function renderShow($postId) 
  {
    $post = $this->database->table('posts')->get($postId);
    if (!$post) {
      $this->error('Post not found');
    }
  
    $this->template->post = $post;
    $this->template->comments = $post->related('comments')->order('created_at');
  }
  
  protected function createComponentCommentForm()
  {
    $form = new Form;
    
    $form->addText('name', 'Your name:')->setRequired();
    $form->addText('email', 'Email:')->setRequired();
    $form->addTextArea('content', 'Comment:')->setRequired();
    $form->addSubmit('send', 'Publish comment');
  
    $form->onSuccess[] = array ($this, 'commentFormSucceeded');
  
    return $form;
  }
  
  public function commentFormSucceeded($form, $values)
  {
    $postId = $this->getParameter('postId');
    
    $this->database->table('comments')->insert(array(
      'post_id' => $postId,
      'name' => $values->name,
      'email' => $values->email,
      'content' => $values->content,
      ));
    
    $this->flashMessage('Thank you for your comment', 'success');
    $this->redirect('this');
    
  }
  
  protected function createComponentPostForm()
  {
    $form = new Form;
    
    $form->addText('title', 'Titulek:')->setRequired();
    $form->addText('author', 'Autor')->setRequired()->setValue((($this->getUser()->isLoggedIn()) ? $this->getUser()->getId() : ''));  
    $form->addTextArea('content', 'Obsah:')->setRequired();
    $form->addSubmit('send','Publikovat');
    
    $form->onSuccess[] = array($this, 'postFormSucceeded');
    
    return $form;
  }
  
  public function postFormSucceeded($form, $values)
  {
    if (!$this->getUser()->isLoggedIn()) {
      $this->error('Musíte se přihlásit.');
    }
  
    $postId = $this->getParameter('postId');
    
    if ($postId) {
      $post = $this->database->table('posts')->get($postId);
      $post->update($values);
    } else {
      $post = $this->database->table('posts')->insert($values);
    }
    
    $this->flashMessage("Příspěvek publikován.", 'success');  
    $this->redirect('show', $post->id);
  }
  
  public function actionCreate()
  {
    if (!$this->getUser()->isLoggedIn()) {
      $this->redirect('Sign:in');	
    }
  }
  
  public function actionEdit($postId)
  {
    if (!$this->getUser()->isLoggedIn()) {
      $this->redirect('Sign:in');	
    }
  
    $post = $this->database->table('posts')->get($postId);
    
    if (!$post) {
      $this->error('Příspěvek nebyl nalezen');
    }
    
    $this['postForm']->setDefaults($post->toArray());
  }
  
  public function actionRemove($postId)
  {
    if (!$this->getUser()->isLoggedIn()) {
      $this->redirect('Sign:in');
    }
    
    $postId = $this->getParameter('postId');
    
    #TODO
    
    $this->flashMessage("Příspěvek smazán.", 'success');  
    $this->redirect('Homepage:');
  }
}