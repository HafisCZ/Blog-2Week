<?php

namespace App\Presenters;

use Nette\Security as NS;

class UserAuthenticator extends \Nette\Object implements NS\IAuthenticator
{
  public $database;

  public function __construct(\Nette\Database\Context $database)
  {
    $this->database = $database;
  }

  public function authenticate(array $credentials) 
  {
    list($inUsername, $inPassword) = $credentials;
    
    $rowUser = $this->database->table('users')->where('username', $inUsername)->fetch();    
    
    if (!$rowUser) {
      throw new NS\AuthenticationException("Špatné heslo nebo jméno.");
    }
    
    $rowHash = $rowUser->hash;
    if ($rowHash === crypt($inPassword, SignPresenter::$user_salt)) {
      unset($rowHash);
      return new NS\Identity($inUsername, $rowUser->rules, $rowUser);
    } else {
      throw new NS\AuthenticationException("Špatné heslo nebo jméno.");  
    }
  }
  
}