<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 8/4/17
 * Time: 10:33 PM
 */

namespace Tables;


use Carbon\Entities;
use Carbon\Interfaces\iEntity;

class Followers extends Entities implements iEntity
{

    static function get(&$array, $id)
    {
        $stmt = self::database()->prepare( 'SELECT COUNT(*) FROM StatsCoach.user_followers WHERE follows_user_id = ?' );
        $stmt->execute( [$id] );
        $array['stats']['followers'] = (int)$stmt->fetchColumn();
        $stmt = self::database()->prepare( 'SELECT COUNT(*) FROM StatsCoach.user_followers WHERE user_id = ?' );
        $stmt->execute( [$id] );
        $array['stats']['following'] = (int)$stmt->fetchColumn();
        return $array;
    }


    static function all(&$object, $id)
    {

    }

    static function range(&$object, $id, $argv)
    {
        // TODO: Implement range() method.
    }

    static function add(&$object, $id, $argv)
    {

    }

    static function remove(&$object, $id)
    {

    }
    
}