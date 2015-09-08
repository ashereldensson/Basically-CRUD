<?php
/**
 * Basically CRUD.
 *
 * Basically CRUD is a class that helps creating basic
 * CRUD system/application, and making it maintainable
 * and secure against SQL injection.
 * To start using it, create an object passing the path
 * to DB.ini file, and you're done. Now you can execute
 * SQL queries.
 * However, I created it with these two concepts in mind:
 * DI (Dependency Injection) and Law of Demeter (a.k.a
 * tell, don't ask.) So make sure to use it this way:
 * In your class, declare a dependency of this class in
 * the constructor (or using set method...etc) and start
 * using methods of this class in your class.
 * For more information checkout the example.
 *
 * @author Anas Shekhamis <anas.shekhamis@gmail.com>
 * @version 1.0
 */
namespace modules;

/**
 * Class Database.
 *
 * @package modules
 */
class Database
{
  /**
   * Holds the PDO connection object.
   *
   * @var object
   */
  private $connection;
  /**
   * Error messages.
   *
   * @var array
   */
  private $errors = array();

  /**
   * @return array
   */
  public function getErrors()
  {
    return $this->errors;
  }

  /**
   * @param string $error
   */
  private function setErrors($error)
  {
    $this->errors[] = $error;
  }


  /**
   * @param string $dbini is the path to the
   * DB.ini file
   */
  function __construct($dbini)
  {
    $this->openConnection($dbini);
  }


  /**
   * Initiate and open the connection.
   *
   * @param string $dbini (check the constructor)
   * @return boolean that indicates if the connection
   * is established or not
   */
  private function openConnection($dbini)
  {
    if (file_exists($dbini)) {
      $dbconfig = parse_ini_file($dbini);
      if ($dbconfig) {
        $this->connection = new \PDO("mysql:host={$dbconfig['host']};dbname={$dbconfig['name']};charset=utf8", $dbconfig['user'], $dbconfig['password'], array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        return true;
      }
      $this->setErrors('Could not establish the connection!');
      return false;
    }
    $this->setErrors('Database config file does not exist!');
    return false;
  }

  /**
   * Create row in the desired table.
   *
   * @param string $table is the table name
   * @param array $fields is an associated array with
   * field's as key, and field's value as value
   * example array('id'=>1, 'name'=>'john')
   * @return boolean which indicates if the the query
   * has been executed or not
   */
  public function createQuery($table, Array $fields)
  {
    $query = "INSERT INTO {$table}(";
    $queryValues = ' VALUES (';
    try {
      foreach ($fields as $field => $value) {
        $query .= $field . ", ";
        $queryValues .= ":{$field}, ";
      }
      $query = substr($query, 0, -2) . ")";
      $queryValues = substr($queryValues, 0, -2) . ")";
      $query .= $queryValues;
      $stmt = $this->connection->prepare($query);
      if ($stmt->execute($fields)) {
        if ($stmt->rowCount()) {
          return true;
        }
      }
      return false;
    } catch (\PDOException $ex) {
      $this->setErrors($this->connection->errorInfo()[2]);
      return false;
    }
  }


  /**
   * Read row(s) from a desired table.
   *
   * @param string $table is the table name
   * @param array $where is an optional array of
   * parameters to be feed the where statement
   * example: array('id'=>1)
   * @param string $limit is optional, and used
   * to limit the returned rows
   * @param string $orderBy is used with $desc to
   * return results in ascending or descending order
   * @param bool $desc is used with $orderBy to
   * return results in ascending or descending order.
   * Descending by default.
   * @return array of rows or empty one
   */
  public function readQuery($table, Array $where = array(), $limit = '', $orderBy = 'id', $desc = true)
  {
    $query = "SELECT * FROM {$table}";
    try {
      if (!empty($where)) {
        $query .= " WHERE";
        foreach ($where as $field => $value) {
          $query .= " {$field} = :{$field} AND";
        }
        $query = substr($query, 0, -4);
      }
      if ($desc == true) {
        $query .= " ORDER BY {$orderBy} DESC";
      }
      if (!empty($limit)) {
        $query .= " LIMIT {$limit}";
      }
      $stmt = $this->connection->prepare($query);
      if ($stmt->execute($where)) {
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
      }
      return array();
    } catch (\PDOException $ex) {
      $this->setErrors($this->connection->errorInfo()[2]);
      return array();
    }
  }

  /**
   * Update row(s) of a desired table.
   *
   * @param string $table is the table name
   * @param array $fields is an associated
   * array with field's as key, and field's
   * value as value
   * example array('name'=>'John', 'email'=>'john@doe.com')
   * @param array $where is an optional array
   * of parameters to be feed the where statement
   * example: array('id'=>1)
   * @return boolean which indicates if the the
   * query has been executed or not
   */
  public function updateQuery($table, Array $fields, Array $where = array())
  {
    $query = "UPDATE {$table} SET ";
    try {
      foreach ($fields as $field => $value) {
        $query .= "{$field} = :{$field}, ";
      }
      $query = substr($query, 0, -2);
      if (!empty($where)) {
        $query .= " WHERE";
        foreach ($where as $field => $value) {
          $query .= " {$field} = :{$field} AND";
        }
        $query = substr($query, 0, -4);
      }
      $stmt = $this->connection->prepare($query);
      if ($stmt->execute(array_merge($fields, $where))) {
        if ($stmt->rowCount()) {
          return true;
        }
      }
      return false;
    } catch (\PDOException $ex) {
      $this->setErrors($this->connection->errorInfo()[2]);
      return false;
    }
  }

  /**
   * Delete row(s) from a desired table.
   *
   * @param string $table is the table name
   * @param array $where is an optional array of
   * parameters to be feed the where statement
   * example: array('id'=>1)
   * @return boolean which indicates if the query
   * has been executed or not
   */
  public function deleteQuery($table, Array $where = array())
  {
    $query = "DELETE FROM {$table} WHERE ";
    try {
      foreach ($where as $field => $value) {
        $query .= " {$field} = :{$field} AND";
      }
      $query = substr($query, 0, -4);
      $stmt = $this->connection->prepare($query);
      if ($stmt->execute($where)) {
        if ($stmt->rowCount()) {
          return true;
        }
      }
      return false;
    } catch (\PDOException $ex) {
      $this->setErrors($this->connection->errorInfo()[2]);
      return false;
    }
  }

  /**
   * Number of row(s) of a desired table.
   *
   * @param string $table is the table name
   * @param array $where is an optional array of
   * parameters to be feed the where statement
   * example: array('id'=>1)
   * @return array
   */
  public function count($table, Array $where = array())
  {
    $query = "SELECT COUNT(*) FROM {$table}";
    try {
      if (!empty($where)) {
        $query .= " WHERE";
        foreach ($where as $field => $value) {
          $query .= " {$field} = :{$field} AND";
        }
        $query = substr($query, 0, -4);
      }
      $stmt = $this->connection->prepare($query);
      if ($stmt->execute($where)) {
        return $stmt->fetchAll(\PDO::FETCH_BOTH);
      }
      return array();
    } catch (\PDOException $ex) {
      $this->setErrors($this->connection->errorInfo()[2]);
      return array();
    }
  }

  /**
   * Execute a custom query.
   *
   * @param string $query
   * @param array $where $where is an optional array
   * of parameters to be feed the where statement
   * example: array('id'=>1, 'name'=>'John')
   * @return array of wanted results
   */
  public function customQuery($query, Array $where = array())
  {
    try{
      $stmt = $this->connection->prepare($query);
      if ($stmt->execute($where)) {
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
      }
      return array();
    } catch (\PDOException $ex) {
    $this->setErrors($this->connection->errorInfo()[2]);
    return array();
    }
  }

  /**
   * Useful for searching.
   *
   * @param string $table is the table name
   * @param array $where $where is an optional array
   * of parameters to be feed the where statement
   * example: array('name'=>'john')
   * @return array of rows
   */
  public function findQuery($table,Array $where)
  {
    $query = "SELECT * FROM {$table} WHERE ";
    try {
      if (!empty($where)) {
        foreach ($where as $field => $value) {
          $query .= " {$field} LIKE :{$field} OR";
        }
        $query = substr($query, 0, -3);
      }
      $stmt = $this->connection->prepare($query);
      if ($stmt->execute($where)) {
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
      }
      return array();
    } catch (\PDOException $ex) {
      $this->setErrors($this->connection->errorInfo()[2]);
      return array();
    }
  }

  /**
   * The last inserted ID.
   *
   * @return int
   */
  public function lastInsertedID()
  {
    return $this->connection->lastInsertId();
  }

  /**
   * Closing opened connection manually.
   *
   */
  public function closeConnection()
  {
    if (isset($this->connection)) {
      $this->connection = null;
    }
  }
}
