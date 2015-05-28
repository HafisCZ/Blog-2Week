<?php

namespace App\Presenters;

use Nette,
	Nette\Application\UI\Form,
  Nette\Utils\Random,
  Nette\Mail\Message,
  Nette\Mail\SendmailMailer;

class SignPresenter extends BasePresenter
{

  private $database;
  public static $user_salt = '$6$rounds=5000$AEcx199opQjk82jkdab84jhk24hkrh0ADK3kCB684n$';
  
  public function __construct(Nette\Database\Context $database)
  {
    $this->database = $database;  
  } 

	protected function createComponentSignInForm()
	{
		$form = new Form;
    
    $form->addText('username', 'Jméno:')->setRequired('Vyplňte jméno.');
    $form->addPassword('password', 'Heslo:')->setRequired('Vyplňte heslo.');
    $form->addCheckbox('remember','Zůstat přihlášen'); 
    $form->addSubmit('send','Přihlásit');
    
    $form->onSuccess[] = array($this, 'signInFormSucceeded');
    return $form;
	}
  
  protected function createComponentForgottenForm()
  {
    $form = new Form;
    
    $form->addText('username', 'Jméno:')->setRequired();
    $form->addText('email', 'Email:')->setRequired()->addRule(Form::EMAIL, 'Zadejte platný email.');
    $form->addSubmit('send', 'Poslat heslo');
    
    $form->onSuccess[] = array($this, 'forgottenFormSucceeded');
    return $form;
  }
  
  protected function createComponentRegistrationForm()
  {
    $form = new Form;
    
    $form->addText('username', 'Jméno:')->setRequired();
    $form->addText('email', 'Email:')->setRequired()->addRule(Form::EMAIL, 'Zadejte platný email.');
    $form->addPassword('password','Heslo:')->setRequired()->addRule(Form::MIN_LENGTH, 'Heslo musí mít nějméně %d znaků.', 6);
    $form->addPassword('checkPassword', 'Heslo znovu:')->setRequired()->addRule(Form::EQUAL, 'Hesla musí být stejná', $form['password']);
    $form->addCheckBox('forward','Notifikace');
    $form->addSubmit('send', 'Registrovat');
    
    $form->onSuccess[] = array($this, 'registrationFormSucceeded');
    return $form;
  }
  
  protected function createComponentDeleteAccountForm() 
  {
    $form = new Form;
    
    $form->addText('username', 'Jméno:')->setRequired();
    $form->addPassword('password', 'Heslo:')->setRequired();
    $form->addPassword('checkPassword', 'Heslo znovu:')->setRequired()->addRule(Form::EQUAL, 'Hesla se neshodují', $form['password']);
    $form->addSubmit('send', 'Odstranit');
    
    $form->onSuccess[] = array($this, 'deleteAccountFormSucceeded');
    return $form;
  }
  
  protected function createComponentSettingsForm()
  {
    if (!$this->getUser()->isLoggedIn()) {
      $this->error('Musíte se přihlásit.');	
    }
    
    $form = new Form;
    
    // $identity = $this->getUser()->getIdentity();
    $identity = $this->database->table('users')->where('username', $this->getUser()->getId())->fetch();
    
    // $form->addText('username', 'Jméno:')->setValue($this->getUser()->getId())->addRule(Form::MIN_LENGTH, 'Jméno musí mít alespoň %d znak.', 1);
    $form->addText('displayname', 'Zobrazené jméno:')->setValue($identity->displayname);
    $form->addText('email', 'Email:')->setValue($identity->email)->addRule(Form::EMAIL, 'Zadejte platný email.');
    $form->addUpload('avatar', 'Avatar:');
    $form->addTextArea('description', 'Popisek:')->setValue($identity->description)->addRule(Form::MAX_LENGTH, 'Popisek je příliš dlouhý', 500);
    $form->addSubmit('send', 'Uložit');
    
    $form->onSuccess[] = array($this, 'settingsFormSucceeded');
    return $form;
  }
  
  public function settingsFormSucceeded($form)
  {
    $values = $form->values;
    if (!$this->getUser()->getId()) {
      $this->error('Musíte se přihlásit');  
    }
    
    $imageLink;
    if ($values['avatar']->isOk()) {
      $rnd = Random::generate(10);
      $filename = $values->avatar->getSanitizedName();
      $targetPath = $this->context->parameters['wwwDir'] . '\\images';
      $values['avatar']->move($targetPath . "\\" . $rnd . "_" . $filename);
      $imageLink = PostPresenter::_WEB_ . "/images/" . $rnd  . "_" . $filename;  
    }
    
    $identity = $this->getUser()->getIdentity();
    $values['avatar'] = ($values['avatar']->isOk()) ? $imageLink : NULL;
    $sessionTable = $this->database->table('users')->where('username', $identity->getId());
    
    if (!($values['avatar'] == NULL)) {
      $sessionTable->update(Array('avatar' => $values->avatar));  
      $identity->avatar = $values->avatar;
    }
    if ($values->displayname !== $identity->displayname) {
      $sessionTable->update(Array('displayname' => $values->displayname));
      $identity->displayname = $values->displayname;
    }
    if ($values->email !== $identity->email) {
      $sessionTable->update(Array('email' => $values->email));
      $identity->email = $values->email;
    }
    if ($values->description !== $identity->description) {
      $sessionTable->update(Array('description' => $values->description));
      $identity->description = $values->description;
    }
    
    $this->flashMessage("Nastavení účtu bylo uloženo.", 'success');
  }
  
  public function forgottenFormSucceeded($form)
  {
    $values = $form->values;
    
    $row = $this->database->table('users')->where('username', $values->username)->fetch();
    if ($row && $row->email === $values->email) {
      $newPassword = substr(md5(rand()), 0, 15);
      $row->update(Array('hash' => crypt($newPassword, SignPresenter::$user_salt)));
      $this->flashMessage('Heslo: '.$newPassword);
      //self::sendMessage($row->email, 'Password Recovery Code', $newPassword);
      unset($newPassword);
      //$this->flashMessage('Heslo odesláno.');
      $this->redirect('Sign:in');
    } else {
      $form->addError("Uživatel nenalezen v databázi");
    }
  }
  
  protected function createComponentChangePasswordForm()
  {
    if (!$this->getUser()->isLoggedIn()) {
      $this->error('Musíte se přihlásit');
    }
    
    $form = new Form;
    
    $identity = $this->getUser()->getIdentity();
    
    $form->addPassword('oldPassword', 'Stávající heslo:')->setRequired();
    $form->addPassword('newPassword', 'Nové heslo:')->setRequired()->addRule(Form::MIN_LENGTH, 'Heslo musí mít nějméně %d znaků.', 6);
    $form->addPassword('verPassword', 'Zopakujte heslo:')->setRequired()->addRule(Form::EQUAL, 'Hesla se neshodují', $form['newPassword']);
    $form->addSubmit('send', 'Změnit');
    
    $form->onSuccess[] = array($this, 'changePasswordFormSucceeded');
    return $form;
  }
  
  public function changePasswordFormSucceeded($form)
  {
    $values = $form->values;
    
    $currentUser = $this->getUser()->getId();
    $userHash = $this->database->table('users')->where('username = ?', $currentUser)->fetch()->hash;
    if($userHash === crypt($values->oldPassword, SignPresenter::$user_salt)) {
      unset($userHash);
      $newHash = crypt($values->newPassword, SignPresenter::$user_salt);      
      $this->database->table('users')->where('username', $currentUser)->update(Array('hash' => $newHash));
      unset($newHash);
      $this->flashMessage("Heslo bylo změněno, znovu se přihlašte", 'success');
      $this->redirect('Sign:out');
    } else {
      $form->addError("Heslo nebylo možné změnit");
    }
  }
  
  public function deleteAccountFormSucceeded($form)
  {
    $values = $form->values;
    
    $currentUser = $this->getUser()->getId();
    $userHash = $this->database->table('users')->where('username = ?', $currentUser)->fetch()->hash;
    if ($userHash === crypt($values->password, SignPresenter::$user_salt)) {
      unset($userHash);
      $acc = $this->database->table('users')->where('username = ?', $currentUser)->fetch();
      $acc->delete();
      self::actionOut();  	
      $this->flashMessage("Účet byl smazán.", 'success');
      $this->redirect('Homepage:');
    } else {
      $form->addError("Účet nebylo možno smazat.");
    }
  }
  
  public function sendMessage($to, $title, $message)
  {
    $mail = new Message;
    $mail->setFrom('pasw@localblog.net', 'Password Recovery')->addTo($to)->setSubject($title)->setBody($message);
    $mailer = new SendmailMailer;
    $mailer->send($mail);
  }
  
  public function registrationFormSucceeded($form)
  {
    $values = $form->values;
    
    if ($this->database->table('users')->where('username = ?', $values->username)->fetch()) {
      $this->flashMessage("Uživatel se zadaným jménem již existuje !", 'error'); 
      $this->redirect('Sign:in');
    } else {
      $this->database->table('users')->insert(array(
        'username' => $values->username,
        'email' => $values->email,
        'hash' => crypt($values->password, self::$user_salt),
        'rules' => 0,
        'forward' => $values->forward,
      ));
      unset($values['password']);
    }
    
    $regMessage = "Registrace byla úspěšná";
    
    mail($values->email,"Registrace uživatele ".$values->username, $regMessage);
    
    $this->flashMessage("Registrace byla úspěšná.", 'success');  
    $this->redirect('Sign:in');
  }

  public function signInFormSucceeded($form)
  {
    $values = $form->values;
    
    try {
      $this->getUser()->login($values->username, $values->password);
      ini_set('session.gc_maxlifetime', 43201*60);
      $this->getUser()->setExpiration((($values->remember) ? '43200 minutes' : 0));
      $this->redirect('Homepage:'); 
    } catch (Nette\Security\AuthenticationException $e) {
      $form->addError('Nesprávné jméno či heslo.');
    }
    unset($values['password']);
  }

	public function actionOut()
	{
		$this->getUser()->logout(TRUE);
    $this->getSession()->destroy();
		$this->flashMessage('Odhlášení bylo úspěšné.');
		$this->redirect('Homepage:');
	}

}
