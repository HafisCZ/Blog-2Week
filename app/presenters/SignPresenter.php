<?php

namespace App\Presenters;

use Nette,
	Nette\Application\UI\Form;


/**
 * Sign in/out presenters.
 */
class SignPresenter extends BasePresenter
{
	/** @var SignFormFactory @inject */

  private $database;
  
  public function __construct(Nette\Database\Context $database)
  {
    $this->database = $database;  
  } 

	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm()
	{
		$form = new Form;
    
    $form->addText('username', 'Login:')->setRequired('Vyplňte jméno.');
    $form->addPassword('password', 'Heslo:')->setRequired('Vyplňte heslo.');
    $form->addCheckbox('remember','Zůstat přihlášen'); 
    $form->addSubmit('send','Přihlásit');
    
    $form->onSuccess[] = array($this, 'signInFormSucceeded');
    return $form;
	}
  
  protected function createComponentRegistrationForm()
  {
    $form = new Form;
    
    $form->addText('username', 'Login:')->setRequired();
    $form->addText('email', 'Email:')->setRequired();
    $form->addPassword('password', 'Heslo:')->setRequired();
    $form->addSubmit('send', 'Registrovat');
    
    $form->onSuccess[] = array($this, 'registrationFormSucceeded');
    return $form;
  }
  
  public function registrationFormSucceeded($form)
  {
    $values = $form->values;
    
    $userId = $this->getParameter('username');
    
    if ($userId) {
      $this->error('Uživatel již existuje.');
    } else {
      $this->database->table('users')->insert(array(
        'username' => $values->username,
        'email' => $values->email,
        'password' => $values->password,
      ));
    }
    
    $this->flashMessage("Registrace byla úspěšná.", 'success');  
    $this->redirect('Sign:in');
  }

  public function signInFormSucceeded($form)
  {
    $values = $form->values;
    
    try {
      $this->getUser()->login($values->username, $values->password);
      $this->redirect('Homepage:'); 
    } catch (Nette\Security\AuthenticationException $e) {
      $form->addError('Nesprávné jméno či heslo.');
    }
  }

	public function actionOut()
	{
		$this->getUser()->logout();
		$this->flashMessage('Odhlášení bylo úspěšné.');
		$this->redirect('Homepage:');
	}

}
