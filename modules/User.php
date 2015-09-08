<?php
/**
 * User class example.
 *
 * This class include some examples of how to use
 * the Database class. However, not all the cases
 * are covered here, and each method could be used
 * with more options.
 * For more information, please check the methods
 * documentations.
 */
namespace modules;


/**
 * Class User
 * @package modules
 */
class User
{
  private $id = 0;
  private $name;
  private $email;

  /**
   * Create a dependency of the class Database
   * @param Database $dbh
   */
  function __construct(Database $dbh)
  {
    $this->dbh = $dbh;
  }

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param int $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }


  /**
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }

  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }

  /**
   * Using createQuery method
   * @return bool
   */
  public function createUser()
  {
    $userData = array('name' => $this->getName(), 'email' => $this->getEmail());
    return ($this->dbh->createQuery('user', $userData));
  }

  /**
   * Using readQuery method
   * @return array
   */
  public function readUser()
  {
    $userID = array('id' => $this->getId());
    if (!empty($res = $this->dbh->readQuery('user', $userID))) {
      return $res[0]; //because the readQuery method returns an array of arrays.
    }
    return array();
  }

  /**
   * Another use of readQuery method
   * @param string $limit
   * @return array
   */
  public function readAllUsers($limit = '')
  {
    if (!empty($res = $this->dbh->readQuery('user', array(), $limit))) {
      return $res;
    }
    return array();
  }

  /**
   * Using updateQuery method
   * @return bool
   */
  public function updateUser()
  {
    $userUpdateData = array('name' => $this->getName(), 'email' => $this->getEmail());
    $userID = array('id' => $this->getId());
    return ($this->dbh->updateQuery('user', $userUpdateData, $userID));
  }

  /**
   * Using deleteQuery method
   * @return bool
   */
  public function deleteUser()
  {
    $userID = array('id'=>$this->getId());
    return ($this->dbh->deleteQuery('user', $userID));
  }


  /**
   * Using customQuery method
   * @return array
   */
  public function loggedInDate()
  {
    $query = "SELECT COUNT(*) AS num FROM user WHERE logged_in BETWEEN '2014-03-05 00:00:00' AND '2014-03-05 23:59:59'";
    if (!empty($res = $this->dbh->customQuery($query))) {
      return $res;
    }
    return array();
  }


  /**
   * Using findQuery method
   * @return array
   */
  public function findUsersByName()
  {
    $userByName = array('name' => '%'.$this->getName().'%');
    if (!empty($res = $this->dbh->findQuery('user', $userByName))) {
      return $res;
    }
    return array();
  }


  /**
   * Using countQuery method
   * @return int
   */
  public function countUsers()
  {
    if (!empty($res = $this->dbh->count('user'))) {
      return $res[0][0]; //because the count method returns an array of arrays.
    }
    return 0;
  }
}