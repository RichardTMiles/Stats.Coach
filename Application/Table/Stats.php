<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 8/4/17
 * Time: 11:16 PM
 */

namespace Table;


use Carbon\Entities;
use Carbon\Interfaces\iTable;

class Stats extends Entities implements iTable
{


    /**
     * @param $array - values received will be placed in this array
     * @param $id - the rows primary key
     * @return bool
     */
    public static function All(array &$array, string $id): bool
    {
        $array['stats'] = self::fetch('SELECT * FROM StatsCoach.golf_stats WHERE stats_id = ?', $id);
        return true;
    }

    /**
     * @param $array - should be set to null on success
     * @param $id - the rows primary key
     * @return bool
     */
    public static function Delete(array &$array, string $id): bool
    {
        // Will be automatically done when foreign keys are removed (innodb)
        return true;
    }

    /**
     * @param $array - values received will be placed in this array
     * @param $id - the rows primary key
     * @param $argv - column names desired to be in our array
     * @return bool
     */
    public static function Get(array &$array, string $id, array $argv): bool
    {
        // TODO: Implement Get() method.

    }

    /**
     * @param $array
     * @return bool
     */
    public static function Post(array $array): bool
    {
        return self::execute('INSERT INTO StatsCoach.golf_stats (stats_id) VALUES (?)', $_SESSION['id']);
    }

    /**
     * @param $array - on success, fields updated will be
     * @param $id - the rows primary key
     * @param $argv - an associative array of Column => Value pairs
     * @return bool  - true on success false on failure
     */
    public static function Put(array &$array, string $id, array $argv): bool
    {
        // TODO: Implement Put() method.
    }
}