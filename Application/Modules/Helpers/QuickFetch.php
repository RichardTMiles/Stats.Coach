<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 6/26/17
 * Time: 8:47 PM
 */

namespace Modules\Helpers;

use Modules\Database;

abstract class QuickFetch
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }


    // https://stackoverflow.com/questions/4697656/using-json-encode-on-objects-in-php-regardless-of-scope
    public function jsonSerialize()
    {
        return get_object_vars( $this );      // I dont think ill include this in carbon
    }

    public function fetch_into_current_class($array)
    {
        $object = get_object_vars( $this );
        foreach ($array as $key => $value)
            if (array_key_exists( $key, $object ))
                $this->$key = $value;
    }

    public function fetch_as_object($sql, ... $execute)
    {
        $stmt = $this->db->prepare( $sql );
        $stmt->setFetchMode( \PDO::FETCH_CLASS, \stdClass::class );
        $stmt->execute( $execute );
        $stmt = $stmt->fetchAll();  // user obj
        return clone (is_array( $stmt ) && count( $stmt ) == 1 ? $stmt[0] : $stmt);
    }

    public function fetch_to_global($sql, $execute)
    {
        try {
            $stmt = $this->db->prepare( $sql );
            $stmt->setFetchMode( \PDO::FETCH_CLASS, Carbon::class );
            $stmt->execute( $execute );
            return $stmt->fetchAll();  // user obj
        } catch (\Exception $e) {
            sortDump( $e );
        }
    }
}