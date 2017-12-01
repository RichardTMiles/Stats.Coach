<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 7/29/17
 * Time: 10:38 PM
 */

namespace Tables;


use Carbon\Database;
use Carbon\Entities;
use Carbon\Error\PublicAlert;
use Carbon\Interfaces\iEntity;

class Photos extends Entities implements iEntity
{
    static function get(&$array, $id)
    {
        if (!is_array($array))
            throw new \Exception( 'Invalid Object Passed' );
        $array['photo'] = [];

        $sql = 'SELECT photo_id, parent_id, user_id, photo_path, photo_description FROM StatsCoach.carbon_photos WHERE parent_id = ? OR photo_id = ? LIMIT 1';
        $stmt = self::fetch( $sql, $id, $id );

        if (array_key_exists('photo_id', $stmt)) $stmt = [$stmt];

        foreach ($stmt as $item => $value)
             $array['photo'][$value['photo_id']] = $value;

        return $array;
    }

    static function all(&$object, $id)
    {
        $sql = 'SELECT photo_id, parent_id, user_id, photo_path, photo_description FROM StatsCoach.carbon_photos WHERE parent_id = ?';
        $object['photo'] = static::fetch( $sql, $id );
    }

    static function range(&$object, $id, $argv)
    {
        // TODO: Implement range() method.
    }

    static function add(&$object, $id, $argv)
    {
        $photo_id = static::beginTransaction( ENTITY_PHOTOS, $id );
        $sql = 'REPLACE INTO StatsCoach.carbon_photos (parent_id, photo_id, user_id, photo_path, photo_description) VALUES (:parent_id, :photo_id, :user_id, :photo_path, :photo_description)';
        $stmt = Database::database()->prepare( $sql );
        $stmt->bindValue( ':parent_id', $id );
        $stmt->bindValue( ':photo_id', $photo_id );
        $stmt->bindValue( ':user_id', $_SESSION['id'] );
        $stmt->bindValue( ':photo_path', $argv['photo_path'] );
        $stmt->bindValue( ':photo_description', $argv['photo_description'] );
        if (!$stmt->execute())
            throw new PublicAlert( 'Sorry, we could not process your request.', 'danger' );
        return static::commit();
    }

    static function remove(&$object, $id)
    {
        $sql = 'DELETE * FROM StatsCoach.carbon_photos WHERE photo_id = ?';
        if (array_key_exists( $id, $object->photos ))
            unset( $object->photos[$id] );    // I may not need the array_key_exists
        return Database::database()->prepare( $sql )->execute( [$id] );
    }

}