<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 9/3/17
 * Time: 11:16 PM
 */

namespace Tables;


use Model\Helpers\iSport;
use Modules\Error\PublicAlert;
use Modules\Helpers\Bcrypt;
use Modules\Helpers\Entities;
use Modules\Interfaces\iEntity;
use Psr\Log\InvalidArgumentException;

class Users extends Entities implements iEntity
{

    static function get($object, $id)
    {
        if (!is_object($object))
            throw new InvalidArgumentException();

        $object = static::fetch_object( 'SELECT user_profile_uri, user_profile_pic, user_sport, user_first_name, user_last_name FROM StatsCoach.user WHERE user_id = ?', $id );
        $object->user_profile_picture = SITE . $object->user_profile_pic ?? 'Data/Uploads/Pictures/Defaults/default_avatar.png';
        $object->user_full_name = $object->user_first_name . ' ' . $object->user_last_name;

        $sport = $object->user_sport;
        $sport = "Model\\$sport";
        $sport = new $sport;
        if ($sport instanceof iSport)                   // load stats
            $sport->stats( $id );

        return $object;
    }

    static function add($object, $id, $argv)        // object and id will be null
    {
        $lambda = function (...$required) use ($argv) {
            foreach ($required as $item => $value)
                 $array = $argv[$item] ?? false or die(1);
            return $array;
        };

        list($username, $password, $email, $userType, $fistName, $lastName, $gender) =
        $lambda('username', 'password', 'email', 'userType', 'fistName', 'lastName', 'gender');

        $email_code = uniqid( 'code_', true ); // Creating a unique string.

        $password = Bcrypt::genHash( $password );

        // Begin transaction
        $_SESSION['id'] = self::beginTransaction( Entities::USER );

        $sql = "INSERT INTO StatsCoach.user (user_id, user_profile_uri, user_username, user_password, user_type, user_email, user_ip, user_last_login, user_email_code, user_first_name, user_last_name, user_gender) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        if (!self::database()->prepare( $sql )->execute( array($_SESSION['id'], $_SESSION['id'], $username, $password, $userType, $email, $_SERVER['REMOTE_ADDR'], time(), $email_code, $firstName, $lastName, $gender) ))
            throw new PublicAlert ( 'Your account could not be created.', 'danger' );

        if (!self::database()->prepare( 'INSERT INTO StatsCoach.golf_stats (stats_id) VALUES (?)' )->execute( [$_SESSION['id']] ))
            throw new PublicAlert ( 'Your account could not be created.', 'danger' );;

        self::commit();
    }

    static function remove($user, $id)
    {
        // TODO: Implement remove() method.
    }

    static function all($user, $id)
    {
        $user = static::fetch_object( 'SELECT * FROM StatsCoach.user LEFT JOIN StatsCoach.entity_tag ON entity_id = StatsCoach.user.user_id WHERE StatsCoach.user.user_id = ?', $id );
        if (!is_object( $user )) throw new \Exception( 'Could not find user  ' . $id );
        $user->user_profile_picture = SITE . (!empty( $user->user_profile_pic ) ? $user->user_profile_pic : 'Data/Uploads/Pictures/Defaults/default_avatar.png');
        $user->user_cover_photo = SITE . $user->user_cover_photo;
        $user->user_full_name = $user->user_first_name . ' ' . $user->user_last_name;
        return $user;
    }

    static function sport($user, $id)
    {
        if (!is_object($user))
            throw new InvalidArgumentException('Non Object Passed');

        $sport = $user->user_sport;
        $sport = "Model\\$sport";
        $sport = new $sport;
        if ($sport instanceof iSport)                   // load stats
            return $sport->stats( $user, $id );         // B/C we don't not work?

    }


    static function range($object, $id, $argv)
    {
        // TODO: Implement range() method.
    }

    static function user_id_from_uri(string $user_uri)
    {
        $stmt = self::database()->prepare( 'SELECT user_id FROM StatsCoach.user WHERE user_profile_uri = ?' );
        $stmt->execute( [$user_uri] );
        return $stmt->fetch( \PDO::FETCH_COLUMN );
    }

    static function changeStatus($status = false)
    {
        global $user;
        $sql = 'UPDATE StatsCoach.user_session SET user_online_status = ? WHERE user_id = ?';
        $stmt = self::database()->prepare( $sql );
        $stmt->execute( [$status, $_SESSION['id']] );
        return $user[$id]->online = (bool) $stmt->fetchColumn();
    }

    private static function change_password($user_id, $password)
    {   /* Two create a Hash you do */
        $password = Bcrypt::genHash( $password );
        return self::database()->prepare( "UPDATE StatsCoach.user SET user_password = ? WHERE user_id = ?" )->execute( [$password, $user_id] );
    }

    static function onlineStatus($id): bool
    {
        global $user;
        $sql = 'SELECT user_online_status FROM StatsCoach.user_session WHERE user_id = ? LIMIT 1';
        self::fetch_into_class( $user[$id], $sql, $id );
        return $user[$id]->user_online_status;
    }

    static function user_exists($username): bool
    {
        $sql = 'SELECT COUNT(user_id) FROM StatsCoach.user WHERE user_username = ? LIMIT 1';
        $stmt = self::database()->prepare( $sql );
        $stmt->execute( [$username] );
        return $stmt->fetchColumn();
    }

    static function email_exists($email): bool
    {
        $sql = "SELECT COUNT(user_id) FROM StatsCoach.user WHERE `user_email`= ? LIMIT 1";
        $stmt = self::database()->prepare( $sql );
        $stmt->execute( array($email) );
        return $stmt->fetchColumn();
    }

    static function email_confirmed($username): bool
    {
        $sql = "SELECT user_email_confirmed FROM StatsCoach.user WHERE user_username = ? LIMIT 1";
        $stmt = self::database()->prepare( $sql );
        $stmt->execute( [$username] );
        return $stmt->fetchColumn();
    }
}