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

    public function __wakeup()
    {
        self::__construct();
    }

    public function fetch_into_current_class($array)
    {
        $object = get_object_vars( $this );
        foreach ($array as $key => $value)
            if (array_key_exists( $key, $object ))
                $this->$key = $value;
    }
    public function fetch_as_object($sql, $execute)
    {
        $stmt = $this->db->prepare( $sql, $execute );
        $stmt->setFetchMode( \PDO::FETCH_CLASS, Skeleton::class );
        $stmt->execute($execute);
        return $stmt->fetchAll();  // user obj
    }
}