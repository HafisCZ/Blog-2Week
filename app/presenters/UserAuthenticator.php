<?php

namespace App\Presenters;

use Nette\Security as NS;

class UserAuthenticator extends Nette\Object implements NS\IAuthenticator
{
  public $database;
  
  function __construct(Nette\Database\Context $database)
  {
    $this->database = $database;
  }
  
  function authenticate(array $credentials)
  {
    list($username, $password) = $credentials;
    $row = $this->database->table('users')->where('username', $username)->fetch();
    
    if (!$row) {
      throw new NS\AutheticationException('Uživatel nenalezen.');
    }
    
    if (!NS\Passwords::verify($password, $row->password)) {
      throw new NS\AuthenticationException('Špatné heslo.');
    }
    
    return new NS\Identity($row->id, $row->email);
  }
}