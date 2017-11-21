<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 9/3/17
 * Time: 11:16 PM
 */

namespace Tables;

use Model\Helpers\Events;
use Model\Helpers\iSport;
use Carbon\Error\PublicAlert;
use Carbon\Helpers\Bcrypt;
use Carbon\Entities;
use Carbon\Interfaces\iEntity;

class Users extends Entities implements iEntity
{

    static function get(&$array, $id)
    {

        $array = static::fetch( 'SELECT * FROM StatsCoach.user WHERE user_id = ?', $id );
        $array['user_profile_picture'] = SITE . ($array['user_profile_pic'] ?? 'Public/img/defaults/photos'.rand(1,3).'.png');
        $array['user_full_name'] = $array['user_first_name'] . ' ' . $array['user_last_name'];
        $array['user_id'] = $id;
        Users::sport( $array, $id );


        return $array;
    }

    static function add(&$object, $id, $argv)        // object and id will be null
    {
        $lambda = function (...$required) use ($argv) {
            foreach ($required as $item => $value) {
                if ($argv[$value] ?? false)
                    $array[] = $argv[$value];
                else throw new \Error();
            }
            return $array ?? [];
        };

        list($username, $password, $email, $userType, $firstName, $lastName, $gender) =
        $lambda('username', 'password', 'email', 'userType', 'fistName', 'lastName', 'gender');

        $email_code = uniqid( 'code_', true ); // Creating a unique string.

        $password = Bcrypt::genHash( $password );

        $key = self::beginTransaction( USER );         // Begin transaction

        $sql = "INSERT INTO StatsCoach.user (user_id, user_profile_uri, user_username, user_password, user_type, user_email, user_ip, user_last_login, user_email_code, user_first_name, user_last_name, user_gender) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        if (!self::database()->prepare( $sql )->execute( array($key, $key, $username, $password, $userType, $email, $_SERVER['REMOTE_ADDR'], time(), $email_code, $firstName, $lastName, $gender) ))
            throw new PublicAlert ( 'Your account could not be created.', 'danger' );

        if (!self::database()->prepare( 'INSERT INTO StatsCoach.golf_stats (stats_id) VALUES (?)' )->execute( [$key] ))
            throw new PublicAlert ( 'Your account could not be created.', 'danger' );;

        if (self::commit()) { $_SESSION['id'] = $key; }

        return $email_code;
    }

    static function remove(&$user, $id)
    {
        // TODO: Implement remove() method.
    }

    static function all(&$user, $id)
    {
        $user = static::fetch( 'SELECT * FROM StatsCoach.user LEFT JOIN StatsCoach.carbon_tag ON entity_id = StatsCoach.user.user_id WHERE StatsCoach.user.user_id = ?', $id );
        if (!is_array( $user )) throw new PublicAlert( 'Could not find user  ' . $id , 'danger');

        $user['user_profile_picture'] = SITE . (!empty( $user['user_profile_pic'] ) ? $user['user_profile_pic'] : 'Data/Uploads/Pictures/Defaults/default_avatar.png');
        $user['user_cover_photo'] = SITE . ($user['user_cover_photo'] ?? 'Data/vendor/almasaeed2010/adminlte/dist/img/photo1.png');
        $user['user_first_last'] = $user['user_full_name'] = $user['user_first_name'] . ' ' . $user['user_last_name'];
        $user['user_id'] = $id;

        Users::sport( $user, $id );

        Events::refresh($user, $_SESSION['id']);

        return $user;
    }

    static function sport(&$user, $id)
    {
        if (!is_array($user)) throw new \InvalidArgumentException('Non Object Passed');

        $sport = $user['user_sport'];
        $sport = "Model\\$sport";

        if (!class_exists($sport)) return null;

        Teams::all( $user, $id );

        $sport = new $sport;
        if ($sport instanceof iSport)                   // load stats
            return $sport->stats( $user, $id );

        throw new PublicAlert('You ran into a big problem. Contact us for support..');
    }


    static function range(&$object, $id, $argv)
    {
        // TODO: Implement range() method.
    }

    static function user_id_from_uri(string $user_uri)
    {
        $stmt = self::database()->prepare( 'SELECT user_id FROM StatsCoach.user WHERE user_profile_uri = ? OR user_id = ?' );
        $stmt->execute( [$user_uri, $user_uri] );
        return $stmt->fetch( \PDO::FETCH_COLUMN );
    }

    static function changeStatus($status = false)
    {
        global $user;
        $sql = 'UPDATE StatsCoach.carbon_session SET user_online_status = ? WHERE user_id = ?';
        $stmt = self::database()->prepare( $sql );
        $stmt->execute( [$status, $_SESSION['id']] );
        return $user[$_SESSION['id']]['online'] = (bool) $stmt->fetchColumn();
    }

    private static function change_password($user_id, $password)
    {   /* Two create a Hash you do */
        $password = Bcrypt::genHash( $password );
        return self::database()->prepare( "UPDATE StatsCoach.user SET user_password = ? WHERE user_id = ?" )->execute( [$password, $user_id] );
    }

    static function onlineStatus($id): bool
    {
        global $user;
        $sql = 'SELECT user_online_status FROM StatsCoach.carbon_session WHERE user_id = ? LIMIT 1';
        self::fetch_into_class( $user[$id], $sql, $id );
        return $user[$id]['user_online_status'];
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