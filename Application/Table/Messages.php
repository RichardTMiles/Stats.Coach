<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 8/2/17
 * Time: 12:46 PM
 */

namespace Table;

use Carbon\Database;
use Carbon\Error\PublicAlert;
use Model\Helpers\GlobalMap;
use Model\User;
use Carbon\Entities;
use Carbon\Interfaces\iTable;
use Carbon\Helpers\Pipe;

class Messages extends Entities implements iTable
{
    public static function Get(array &$array, string $id, array $argv): bool
    {
        $to_user = $array['user_id'] ?? false;
        if (!$to_user) {
            throw new \InvalidArgumentException('Cannot get messages from a non-user.');
        }
        $array['messages'] = self::fetch('SELECT * FROM user_messages INNER JOIN carbon_tag ON entity_id = message_id WHERE 
                    user_messages.to_user_id = ? AND carbon_tag.user_id = ? OR 
                    user_messages.to_user_id = ? AND carbon_tag.user_id = ?', $id, $_SESSION['id'], $_SESSION['id'], $id);
        return true;
    }

    public static function All(array &$array, string $id): bool   // signed in user
    {
        $stmt = Database::database()->prepare('SELECT user_id, to_user_id FROM user_messages INNER JOIN carbon_tag ON entity_id = message_id WHERE 
                    user_messages.to_user_id = ? OR carbon_tag.user_id = ?');
        $stmt->execute([$id, $id]);
        $stmt = $stmt->fetchAll();

        $users = array();
        foreach ($stmt as $message => $userId) {
            foreach ($userId as $user => $uid) {
                if (!array_key_exists($uid, $users)) {
                    $users[$uid] = $uid;
                }
            }
        }
        foreach ($users as $key => $val) {
            new User($val);
        }

        return true;
    }

    public static function Put(array &$array, string $id, array $argv): bool
    {
        // TODO: Implement Put() method.
        return true;
    }

    public static function Post(array $array): bool   // id is the user to be sent to
    {
        $message_id = self::beginTransaction(USER_MESSAGES, $_SESSION['id']);
        $sql = 'INSERT INTO user_messages (message_id, to_user_id, message) VALUES (:message_id, :user_id, :message)';
        $stmt = Database::database()->prepare($sql);
        $stmt->bindValue(':message_id', $message_id);
        $stmt->bindValue(':user_id', $id = $array['to_user']);
        $stmt->bindValue(':message', $array['message']);
        return $stmt->execute() ? self::commit(function () use ($id) {
            GlobalMap::sendUpdate($_SESSION['id'], "Messages/\nMessages/$id/\n");   // Update My View
            GlobalMap::sendUpdate($id, "Messages/\nMessages/{$_SESSION['id']}/\n");  // Update there browser
        }) : self::verify('Failed to send your message.');
    }

    public static function Delete(array &$array, string $id): bool
    {
        if (!static::remove_entity($id)){
            PublicAlert::danger('Failed to delete your message. Please try again later.');
        }
        return self::All($array, $_SESSION['id']);  // TODO -check
    }

}