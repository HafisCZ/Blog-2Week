<?php

namespace App\Models;

use Nette\Security as NS;
use App\Presenters as PS;

class UserAuthenticator extends \Nette\Object implements NS\IAuthenticator
{
  private $database;

  public function __construct(\Nette\Database\Context $database)
  {
    $this->database = $database;
  }

  public function authenticate(array $credentials) 
  {
    list($inUsername, $inPassword) = $credentials;
    unset($credentials);
    
    $rowUser = $this->database->table('users')->where('username', $inUsername)->fetch();    
    
    if (!$rowUser) {
      throw new NS\AuthenticationException("Špatné heslo nebo jméno.");
    }
    
    $rowHash = $rowUser->hash;
    if ($rowHash === crypt($inPassword, PS\SignPresenter::$user_salt)) {
      unset($rowHash);
      unset($inPassword);
      return new NS\Identity($inUsername, $rowUser->rules, $rowUser);
    } else {
      throw new NS\AuthenticationException("Špatné heslo nebo jméno.");  
    }
  }
  
}