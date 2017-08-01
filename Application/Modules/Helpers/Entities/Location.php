<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 7/31/17
 * Time: 7:53 PM
 */

namespace Modules\Helpers\Entities;


use Modules\Helpers\Entities;

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
        $sql = "INSERT INTO StatsCoach.entity_location (entity_id, latitude, longitude, street, city, state, elevation) VALUES (:entity_id, :latitude, :longitude, :street, :city, :state, :elevation)";
        $stmt = self::database()->prepare( $sql );
        $stmt->bindValue( ':entity_id', $id );
        $stmt->bindValue( ':latitude',  isset($argv['latitude']) ? $argv['latitude']  : null );
        $stmt->bindValue( ':longitude', isset($argv['longitude']) ? $argv['longitude']  : null  );
        $stmt->bindValue( ':elevation', isset($argv['elevation']) ? $argv['elevation']  : null  );
        $stmt->bindValue( ':street',    isset($argv['street']) ? $argv['street']  : null  );
        $stmt->bindValue( ':city',      isset($argv['city']) ? $argv['city']  : null  );
        $stmt->bindValue( ':state',     isset($argv['state']) ? $argv['state']  : null  );
        if (!$stmt->execute()) throw new \Exception( "Failed inserting courses" );
        return true;
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