<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 7/29/17
 * Time: 10:38 PM
 */

namespace Modules\Helpers\Entities;


use Modules\Helpers\Entities;
use Modules\Error\PublicAlert;
use Modules\Interfaces\iEntity;

class Photos extends Entities implements iEntity
{
    static function get($object, $id)
    {
        $sql = 'SELECT * FROM StatsCoach.entity_photos WHERE parent_id = ?';
        $object->photos = static::fetch_classes( $sql, $id );
    }

    static function add($object, $id, $argv)
    {
        $photo_id = static::beginTransaction( Entities::ENTITY_PHOTOS, $id );
        $sql = 'REPLACE INTO StatsCoach.entity_photos (parent_id, photo_id, user_id, photo_path, photo_description) VALUES (:parent_id, :photo_id, :user_id, :photo_path, :photo_description)';
        $stmt = self::database()->prepare( $sql );
        $stmt->bindValue( ':parent_id', $id );
        $stmt->bindValue( ':photo_id', $photo_id );
        $stmt->bindValue( ':user_id', $_SESSION['id'] );
        $stmt->bindValue( ':photo_path', $argv['photo_path'] );
        $stmt->bindValue( ':photo_description', $argv['photo_description'] );
        if (!$stmt->execute())
            throw new PublicAlert('Sorry, we could not process your request.','danger');
        return static::commit();
    }

    static function remove($object, $id)
    {
        $sql = 'DELETE * FROM StatsCoach.entity_photos WHERE photo_id = ?';
        if (array_key_exists( $id, $object->photos ))
            unset($object->photos[$id]);    // I may not need the array_key_exists
        return self::database()->prepare( $sql )->execute([$id]);
    }

}