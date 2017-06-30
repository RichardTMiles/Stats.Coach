<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 6/26/17
 * Time: 8:47 PM
 */

namespace Modules\Helpers;

use Modules\Database;

abstract class QuickFetch  // implements \JsonSerializable
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function __wakeup()
    {
        alert('woke ' . get_called_class());
        self::__construct();
        $object = get_object_vars( $this );
        foreach ($object as $item => $value)    // TODO - were really going to try and objectify everything
            if(is_object( $temp = @unserialize($this->$item)))
                $this->$item = $temp;
    }
    // https://stackoverflow.com/questions/4697656/using-json-encode-on-objects-in-php-regardless-of-scope
    public function jsonSerialize()
    {
        return get_object_vars($this);      // I dont think ill include this in carbon
    }

    public function fetch_into_current_class($array)
    {
        $object = get_object_vars( $this );
        foreach ($array as $key => $value)
            if (array_key_exists( $key, $object ))
                $this->$key = $value;
    }
    public function fetch_as_object($sql,... $execute)
    {
        $stmt = $this->db->prepare( $sql );
        $stmt->setFetchMode( \PDO::FETCH_CLASS, Skeleton::class );
        $stmt->execute($execute);
        $stmt = $stmt->fetchAll();  // user obj
        return (is_array( $stmt ) && count( $stmt ) == 1 ?
             array_pop( $stmt ) : $stmt);

    }
    public function fetch_to_global($sql, $execute )
    {
        $stmt = $this->db->prepare( $sql );
        $stmt->setFetchMode( \PDO::FETCH_CLASS, Carbon::class );
        $stmt->execute($execute);
        return $stmt->fetchAll();  // user obj
    }
}