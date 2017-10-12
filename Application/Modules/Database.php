<?php // Parent of Model class, can only be called indirectly

namespace Modules;

use PDO;

class Database
{
    private static $database;
    private static $username = DB_USER;
    private static $password = DB_PASS;
    private static $dbName = DB_NAME;
    private static $host = DB_HOST;

    # Build the connection
    public static function resetConnection()
    {
        self::$database = null;
        self::getConnection();
    }

    public static function getConnection(string $dbName = null): PDO
    {
        if (!empty(self::$database) && self::$database instanceof PDO)
            return static::$database;

        $attempts = 0;
        $host = static::$host;
        if (empty($dbName)) $dbName = static::$dbName;

        do { try {
                $db = new PDO( "mysql:host={$host};dbname={$dbName}", static::$username, static::$password );
                $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
                $db->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC );
                self::$database = $db;
                return self::$database;
            } catch (\PDOException $e) {
                $attempts++;
            }
        } while($attempts < 3);
        throw new \Exception( "Could not establish database connection. \n" );
    }

    protected static function setUp()
    {
        if (file_exists( SERVER_ROOT . "Scripts/Build/DBSetup.php" )) {
            require_once SERVER_ROOT . "Scripts/Build/DBSetup.php";
        }
    }

} 

