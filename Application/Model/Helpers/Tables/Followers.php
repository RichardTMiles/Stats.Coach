<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 8/4/17
 * Time: 10:33 PM
 */

namespace Model\Helpers\Tables;


use Modules\Helpers\Entities;
use Modules\Interfaces\iEntity;

class Followers extends Entities implements iEntity
{

    static function get($object, $id)
    {
        $stmt = self::database()->prepare( 'SELECT COUNT(*) FROM StatsCoach.user_followers WHERE follows_user_id = ?' );
        $stmt->execute( [$id] );
        $object->stats->followers = (int)$stmt->fetchColumn();
        $stmt = self::database()->prepare( 'SELECT COUNT(*) FROM StatsCoach.user_followers WHERE user_id = ?' );
        $stmt->execute( [$id] );
        $object->stats->following = (int)$stmt->fetchColumn();
    }

    static function add($object, $id, $argv)
    {

    }

    static function remove($object, $id)
    {

    }
    
}