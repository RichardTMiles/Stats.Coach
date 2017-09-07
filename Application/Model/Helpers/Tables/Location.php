<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 7/31/17
 * Time: 7:53 PM
 */

namespace Tables;


use Modules\Helpers\Entities;
use Modules\Interfaces\iEntity;

class Location extends Entities implements iEntity
{
    static function get($object, $id)
    {
        $sql = 'SELECT * FROM StatsCoach.entity_location WHERE entity_id = ?';
        $object->location = self::fetch_object( $sql, $id );
        return true;
    }

    static function add($object, $id, $argv)
    {
        $sql = "REPLACE INTO StatsCoach.entity_location (entity_id, latitude, longitude, street, city, state, elevation) VALUES (:entity_id, :latitude, :longitude, :street, :city, :state, :elevation)";
        $stmt = self::database()->prepare( $sql );
        $stmt->bindValue( ':entity_id', $id );
        $stmt->bindValue( ':latitude',  $argv['latitude']  ?? null );
        $stmt->bindValue( ':longitude', $argv['longitude']  ?? null  );
        $stmt->bindValue( ':elevation', $argv['elevation']  ?? null  );
        $stmt->bindValue( ':street',    $argv['street']  ?? null  );
        $stmt->bindValue( ':city',      $argv['city']  ?? null  );
        $stmt->bindValue( ':state',     $argv['state']  ?? null  );
        return $stmt->execute();
    }

    static function all($object, $id)
    {

    }

    static function range($object, $id, $argv)
    {
        // TODO: Implement range() method.
    }

    static function remove($object, $id)
    {
        $sql = 'DELETE * FROM StatsCoach.entity_location WHERE entity_id = ?';
        if (self::database()->prepare( $sql )->execute([$id])) {
            unset($object->location);
            return true;
        }
        return false;
    }

}