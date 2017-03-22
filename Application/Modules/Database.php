<?php // Parent of Model class, can only be called indirectly

namespace Modules;

use Psr\Singleton;

class Database
{
    use Singleton;

    private $database;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $dbName = DB_NAME;
    private $host = DB_HOST;

    # Build the connection

    public function __construct($dbName = null)
    {
        $this->getConnection( $dbName );
        $this->setUp();             // This can be deleted after the initial run
        return $this->database;
    }

    private function getConnection($dbName = null)
    {
        if (!empty($this->database))
            return $this->database;

        if (!empty($dbName))
            $this->dbName = $dbName;

        try {
            $this->database = new \PDO( "mysql:host={$this->host};dbname={$this->dbName}", $this->username, $this->password );
            $this->database->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
            $this->database->setAttribute( \PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC );
            return $this->database;
        } catch (\PDOException $e) {
            throw new \Exception( "Could not establish database connection. \n" );    #Push error message
        }
    }

    protected function setUp()
    {
        if (file_exists( SERVER_ROOT . "Application/Configs/DBSetup.php" )) {
            require_once SERVER_ROOT . "Application/Configs/DBSetup.php";
        }
    }

} 

