<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 7/31/17
 * Time: 6:55 PM
 */

namespace Modules\Helpers\Entities;


use Modules\Helpers\Entities;
use Modules\Helpers\Reporting\PublicAlert;

class Comments extends Entities implements iEntity
{
    
    static function get($object, $id)
    {
        $sql = 'SELECT * FROM StatsCoach.entity_comments JOIN StatsCoach.entity_tag ON comment_id = entity_id WHERE parent_id = ?';
        $object->comments = static::fetch_as_array_object( $sql, $id );
        return true;
    }
    
    static function add($object, $id, $argv)
    {
        $comment_id = static::beginTransaction( 'ENTITY_COMMENTS', $id );
        $sql = 'INSERT INTO StatsCoach.entity_comments (parent_id, comment_id, user_id, comment) VALUES (:parent_id, :comment_id, :user_id, :comment)';
        $stmt = self::database()->prepare( $sql );
        $stmt->bindValue( ':parent_id', $id );
        $stmt->bindValue( ':comment_id', $entity );
        $stmt->bindValue( ':user_id', $_SESSION['id'] );
        $stmt->bindValue( ':comment', $argv );
        if ($stmt->execute()) throw new PublicAlert('Sorry, we could not process your request.', 'danger');
        return static::commit();
    }

    static function remove($object, $id)
    {
        static::remove_entity( $id );
        if (array_key_exists( $id, $object->comments ))
            unset($object->comments[$id]);
    }
}