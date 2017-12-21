<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 8/2/17
 * Time: 12:46 PM
 */

namespace Tables;

use Carbon\Database;
use Model\Helpers\GlobalMap;
use Model\User;
use Carbon\Entities;
use Carbon\Interfaces\iEntity;
use Carbon\Helpers\Pipe;

class Messages extends Entities implements iEntity
{
    static function get(&$array, $id)
    {
        $to_user = $array['user_id'] ?? false;
        if (!$to_user) throw new \Exception( 'Cannot get messages from a non-user.' );
        $array['messages'] = self::fetch( 'SELECT * FROM StatsCoach.user_messages INNER JOIN StatsCoach.carbon_tag ON entity_id = message_id WHERE 
                    StatsCoach.user_messages.to_user_id = ? AND StatsCoach.carbon_tag.user_id = ? OR 
                    StatsCoach.user_messages.to_user_id = ? AND StatsCoach.carbon_tag.user_id = ?', $id, $_SESSION['id'], $_SESSION['id'], $id );
        return $array;
    }

    static function all(&$object, $id)   // signed in user
    {
        $stmt = Database::database()->prepare( 'SELECT user_id, to_user_id FROM StatsCoach.user_messages INNER JOIN StatsCoach.carbon_tag ON entity_id = message_id WHERE 
                    StatsCoach.user_messages.to_user_id = ? OR StatsCoach.carbon_tag.user_id = ?');
        $stmt->execute( [$id, $id] );
        $stmt = $stmt->fetchAll();

        $users = array();
        foreach ($stmt as $message => $userId)
            foreach ($userId as $user => $id)
                if (!array_key_exists( $id, $users ))
                    $users[$id] = $id;

        foreach ($users as $key => $val)
            new User( $val );

    }

    static function range(&$object, $id, $argv)
    {
        // TODO: Implement range() method.
    }

    static function add(&$object, $id, $argv)   // id is the user to be sent to
    {
        $message_id = self::beginTransaction( USER_MESSAGES, $_SESSION['id'] );
        $sql = 'INSERT INTO StatsCoach.user_messages (message_id, to_user_id, message) VALUES (:message_id, :user_id, :message)';
        $stmt = Database::database()->prepare( $sql );
        $stmt->bindValue( ':message_id', $message_id );
        $stmt->bindValue( ':user_id', $id );
        $stmt->bindValue( ':message', $argv );
        return $stmt->execute() ? self::commit( function () use ($id) {
            GlobalMap::sendUpdate( $_SESSION['id'], "Messages/\nMessages/$id/\n" );   // Update My View
            GlobalMap::sendUpdate($id, "Messages/\nMessages/{$_SESSION['id']}/\n" );  // Update there browser
        } ) : self::verify( 'Failed to send your message.' );
    }

    static function remove(&$object, $id)
    {
        $sql = 'DELETE * FROM StatsCoach.carbon_location WHERE entity_id = ?';
        if (Database::database()->prepare( $sql )->execute( [$id] )) {
            unset( $object->location );
            return true;
        }
        return false;
    }

}