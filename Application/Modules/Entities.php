<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 7/28/17
 * Time: 5:48 AM
 */

namespace Modules;

use PDO;
use stdClass;
use Modules\Interfaces\iEntity;
use Modules\Error\PublicAlert;

abstract class Entities
{
    protected $db;
    private static $inTransaction;
    private static $entityTransactionKeys;

    public function __construct( $object = null, $id = null)
    {
        $this->db = Database::getConnection();
        if ($this instanceof iEntity) return $this::get( $object, $id );
        #elseif (is_object($object) && $id) static::getEntities($object, $id);
    }

    static protected function database()
    {
        return Database::getConnection();
    }

    static function verify(string $errorMessage = null): bool
    {
        if (!static::$inTransaction) return true;
        if (!empty(self::$entityTransactionKeys))
            foreach (self::$entityTransactionKeys as $key)
                static::remove_entity( $key );
        if (Database::getConnection()->rollBack() && !empty($errorMessage)) {
            throw new PublicAlert($errorMessage);
        } else throw new \Exception('Failed to remove unused keys');
    }

    static function commit(callable $lambda = null): bool
    {
        if (!self::database()->commit()) return static::verify();
        self::$inTransaction = false;
        self::$entityTransactionKeys = [];
        if (is_callable($lambda)) $lambda();
        return true;
    }

    static function beginTransaction($tag_id, $dependant = null)
    {
        self::$inTransaction = true;
        $key = self::new_entity( $tag_id, $dependant );
        Database::getConnection()->beginTransaction();
        return $key;
    }

    static function new_entity($tag_id, $dependant)
    {
        if (defined( $tag_id ))
            $tag_id = constant( $tag_id );

        $db = self::database();
        do {
            try {
                $stmt = $db->prepare( 'INSERT INTO StatsCoach.entity (entity_pk, entity_fk) VALUE (?,?)' );
                $stmt->execute( [$stmt = Bcrypt::genRandomHex(), $dependant] );
            } catch (\PDOException $e) {
                $stmt = false;
            }
        } while (!$stmt);
        $db->prepare( 'INSERT INTO StatsCoach.entity_tag (entity_id, user_id, tag_id, creation_date) VALUES (?,?,?,?)' )->execute( [$stmt, (!empty($_SESSION['id']) ? $_SESSION['id'] : $stmt), $tag_id, time()] );
        self::$entityTransactionKeys[] = $stmt;
        return $stmt;
    }

    static protected function remove_entity($id)
    {
        if (!self::database()->prepare( 'DELETE FROM StatsCoach.entity WHERE entity_pk = ?' )->execute( [$id] ))
            throw new \Exception( "Bad Entity Delete $id" );
    }

    static function fetch_object(string $sql, ...$execute): stdClass
    {
        $stmt = self::database()->prepare( $sql );
        $stmt->setFetchMode( PDO::FETCH_CLASS, stdClass::class );
        if (!$stmt->execute( $execute ))
            throw new \Exception('Failed to Execute');
        $stmt = $stmt->fetchAll();  // user obj
        return (is_array( $stmt ) && count( $stmt ) == 1 ? $stmt[0] : new stdClass);
    }

    static function fetch_classes(string $sql, ...$execute): array
    {
        $stmt = self::database()->prepare( $sql );
        $stmt->setFetchMode( PDO::FETCH_CLASS, stdClass::class );
        if (!$stmt->execute( $execute )) return [];
        return $stmt->fetchAll();  // user obj
    }

    static function fetch_as_array_object(string $sql, ...$execute): array
    {
        $stmt = self::database()->prepare( $sql );
        $stmt->setFetchMode( PDO::FETCH_CLASS, Skeleton::class );
        if (!$stmt->execute( $execute )) return [];
        return $stmt->fetchAll();  // user obj
    }

    static function fetch_to_global(string $sql, $execute)
    {
        $stmt = self::database()->prepare( $sql );
        $stmt->setFetchMode( PDO::FETCH_CLASS, Globals::class );
        $stmt->execute( $execute );
        $stmt->fetchAll();  // user obj
    }

    static function fetch_into_class($object, $sql, ...$execute)
    {
        $stmt = self::database()->prepare( $sql );
        $stmt->execute( $execute );
        $array = $stmt->fetchAll();
        foreach ($array as $key => $value) $object->$key = $value;
    }


}