<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 7/31/17
 * Time: 6:55 PM
 */

namespace Table;


use Carbon\Database;
use Carbon\Entities;
use Carbon\Error\PublicAlert;
use Carbon\Interfaces\iTable;

class Comments extends Entities implements iTable
{

    public static function Get(array &$array, string $id, array $argv): bool
    {
        $sql = 'SELECT * FROM carbon_comments JOIN carbon_tag ON comment_id = entity_id WHERE parent_id = ? LIMIT 10';
        $array['comments'] = static::fetch($sql, $id);
        return true;
    }


    public static function All(array &$object, string $id): bool
    {
        $sql = 'SELECT * FROM carbon_comments JOIN carbon_tag ON comment_id = entity_id WHERE parent_id = ?';
        $object['comments'] = static::fetch_classes($sql, $id);
        return true;
    }

    public static function Put(array &$array, string $id, array $argv) : bool
    {
        // TODO: Implement range() method.
        return true;
    }


    /**
     * @param array $array
     * @return bool
     * @throws PublicAlert
     */
    public static function Post(array $array): bool
    {
        $comment_id = static::beginTransaction('ENTITY_COMMENTS', $array['parent']);
        $sql = 'INSERT INTO carbon_comments (parent_id, comment_id, user_id, comment) VALUES (:parent_id, :comment_id, :user_id, :comment)';
        $stmt = Database::database()->prepare($sql);
        $stmt->bindValue(':parent_id', $array['parent']);
        $stmt->bindValue(':comment_id', $comment_id);
        $stmt->bindValue(':user_id', $_SESSION['id']);
        $stmt->bindValue(':comment', $array['comment']);
        if ($stmt->execute()) {
            throw new PublicAlert('Sorry, we could not process your request.', 'danger');
        }
        return static::commit();
    }

    public static function Delete(array &$array, string $id): bool
    {
        static::remove_entity($id);
        if (array_key_exists($id, $array['comment']))
            unset($array['comment'][$id]);
        return true;
    }
}