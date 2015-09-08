<?php
require_once 'autoload.php';
$dbh = new \modules\Database(DB_INI);
$user = new \modules\User($dbh); //DI
if ($dbh) {
  $user->setName('John Doe');
  $user->setEmail('john@doe.com');
  if ($user->createUser()) {
    $user->setId($dbh->lastInsertedID());
    print_r($user->readUser());
  }
} else {
  var_dump($dbh->getErrors());
}