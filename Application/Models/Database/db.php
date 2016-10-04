<?php // Parent of Model class, can only be called indirectly

namespace App\Models\Database;

use \PDO;

abstract class db
{
    public $isConnected; #Made Public So we can check
    private $database;
    // Because the server will not change, set the variables to private inside the class
    // Currently set to private
    private $username = DB_USER;
    private $password = DB_PASS;
    private $dbName = DB_NAME;
    private $host = DB_HOST;

    #Build the connection
    public function __construct()
    {  // Sets Protected $this->database to our connection
        $this->isConnected = true;
        try {
            $this->database = new \PDO( "mysql:host={$this->host};dbname={$this->dbName}", $this->username, $this->password );
            $this->database->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            $this->database->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
        } catch (\PDOException $e) {
            $this->isConnected = false;      #if error, set $isConnected = false
            throw new \Exception( $e->getMessage() );    #Push error message
        }
    }

    public function Disconnect()
    {
        $this->database = null;
        $this->isConnected = false;
    }

    public function getRow($query, $params = array())
    {
        try {
            $stmt = $this->database->prepare( $query );
            $stmt->execute( $params );
            return $stmt->fetch();
        } catch (\PDOException $e) {
            throw new \Exception( $e->getMessage() );
        }
    }

    public function getAllRows($query, $params = array())
    {
        try {
            $stmt = $this->database->prepare( $query );
            $stmt->execute( $params );
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            throw new \Exception( $e->getMessage() );
        }
    }

    public function checkExist($query, $params = array())
    {
        try {
            $stmt = $this->database->prepare( $query );
            $stmt->execute( $params );
            $rows = $stmt->fetchColumn();

            if ($rows > 0) {
                $return = true;
            } else {
                $return = false;
            }
            return $return;
        } catch (\PDOException $e) {
            throw new \Exception( $e->getMessage() );
        }
    }

    public function insertRow($query, $params)
    {
        try {
            $stmt = $this->database->prepare( $query );
            $stmt->execute( $params );
        } catch (\PDOException $e) {
            // throw new \Exception($e->getMessage());
            return false;
        }
        return true;
    }

    public function updateRow($query, $params)
    {
        return $this->insertRow( $query, $params );
    }    // insertRow();

    public function deleteRow($query, $params)
    {
        return $this->insertRow( $query, $params );
    } // insertRow();

} // end all


//USAGE 
/*      

    For PDO use: when calling the database remember to add the $this-> with in the call
    for Example:
        $getrow = $this->database->getRow(...
        The database must be called with in the function and should be set to private
        Use the __construct() 
        private $database;
    
    Connecting to DataBase
    $database = new db("root", "", "localhost", "database", array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

    Getting row
    $getrow = $database->getRow("SELECT email, username FROM users WHERE username =?", array("yusaf"));

    Getting multiple rows
    $getrows = $database->getAllRows("SELECT id, username FROM users");

    inserting a row
    $insertrow = $database ->insertRow("INSERT INTO users (username, email) VALUES (?, ?)", array("yusaf", "yusaf@email.com"));

    updating existing row           
    $updaterow = $database->updateRow("UPDATE users SET username = ?, email = ? WHERE id = ?", array("yusafk", "yusafk@email.com", "1"));
    
    Check if row exists 
    $rowexist = $database->checkExist("SELECT COUNT(`user_id`) FROM `users` WHERE `user_login`= ?", array($username));

    delete a row
    $deleterow = $database->deleteRow("DELETE FROM users WHERE id = ?", array("1"));
    disconnecting from database
    $database->Disconnect();

    checking if database is connected
    if($database->isConnected){
    echo "you are connected to the database";
    }else{
    echo "you are not connected to the database";
    }

*/ 
