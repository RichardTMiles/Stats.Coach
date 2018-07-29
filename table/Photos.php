<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 7/29/17
 * Time: 10:38 PM
 */

namespace Table;


use Carbon\Database;
use Carbon\Entities;
use Carbon\Error\PublicAlert;
use Carbon\Interfaces\iTable;

class Photos extends Entities implements iTable
{
    static function Get(array &$array, string $id, array $arg) : bool
    {
        $array['photo'] = [];

        $sql = 'SELECT photo_id, parent_id, user_id, photo_path, photo_description FROM carbon_photos WHERE parent_id = ? OR photo_id = ? LIMIT 1';
        $stmt = self::fetch( $sql, $id, $id );

        if (array_key_exists('photo_id', $stmt)) {
            $stmt = [$stmt];
        }

        foreach ($stmt as $item => $value) {
            $array['photo'][$value['photo_id']] = $value;
        }

        return true;
    }

    static function Put(array &$array, string $id, array $argv): bool
    {
        return true;
    }

    static function All(array &$object, string $id) : bool
    {
        $sql = 'SELECT photo_id, parent_id, user_id, photo_path, photo_description FROM carbon_photos WHERE parent_id = ?';
        $object['photo'] = static::fetch( $sql, $id );
        return true;
    }

    static function Post(array $argv) : bool
    {
        $photo_id = static::beginTransaction( ENTITY_PHOTOS, $_SESSION['id'] );
        $sql = 'REPLACE INTO carbon_photos (parent_id, photo_id, user_id, photo_path, photo_description) VALUES (:parent_id, :photo_id, :user_id, :photo_path, :photo_description)';
        $stmt = Database::database()->prepare( $sql );
        $stmt->bindValue( ':parent_id', $argv['parent_id'] );
        $stmt->bindValue( ':photo_id', $photo_id );
        $stmt->bindValue( ':user_id', $_SESSION['id'] );
        $stmt->bindValue( ':photo_path', $argv['photo_path'] );
        $stmt->bindValue( ':photo_description', $argv['photo_description'] );
        if (!$stmt->execute())
            throw new PublicAlert( 'Sorry, we could not process your request.', 'danger' );
        return static::commit();
    }

    static function Delete(array &$array, string $id) : bool
    {
        $sql = 'DELETE * FROM carbon_photos WHERE photo_id = ?';
        if (array_key_exists( $id, $array['photos'] ))
            unset( $array['photos'][$id] );    // I may not need the array_key_exists
        return Database::database()->prepare( $sql )->execute( [$id] );
    }

}