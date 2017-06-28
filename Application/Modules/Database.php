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
    private $attempts;

    # Build the connection

    public function __construct($dbName = null)
    {
        $this->attempts = 0;
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
            $this->attempts++;
            if ($this->attempts < 3) {
                sleep( 1 );    // adding one second of runtime??? should I do this, may help clogging the connection for everyone.. do test
                return $this->getConnection();  // Make sure this will work.
            }
            throw new \Exception( "Could not establish database connection. \n" );  // TODO - catch this error with the class error
        }
    }

    protected function setUp()
    {
        if (file_exists( SERVER_ROOT . "Scripts/Build/DBSetup.php" )) {
            require_once SERVER_ROOT . "Scripts/Build/DBSetup.php";
        }
    }

} 

