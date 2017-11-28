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

        $array = static::fetch('SELECT * FROM StatsCoach.user WHERE user_id = ?', $id);

        $array['user_profile_picture'] = SITE . (!empty($user['user_profile_pic']) ? $user['user_profile_pic'] : 'Data/Uploads/Pictures/Defaults/default_avatar.png');
        $array['user_cover_photo'] = SITE . ($array['user_profile_pic'] ?? 'Public/img/defaults/photo' . rand(1, 3) . '.png');
        $array['user_full_name'] = $array['user_first_name'] . ' ' . $array['user_last_name'];
        $array['user_id'] = $id;
        Users::sport($array, $id);

        return $array;
    }

    static function add(&$object, $id, $argv)        // object and id will be null
    {
        $key = self::beginTransaction(USER);         // Begin transaction

        $sql = "INSERT INTO StatsCoach.user (user_id, user_type, user_session_id, user_facebook_id, user_username, user_first_name, user_last_name, user_profile_pic, user_profile_uri, user_cover_photo, user_birthday, user_gender, user_about_me, user_password, user_email, user_email_code, user_generated_string, user_last_login, user_ip, user_education_history, user_location, user_creation_date) VALUES 
        (:user_id, :user_type, :user_session_id, :user_facebook_id, :user_username, :user_first_name, :user_last_name, :user_profile_pic, :user_profile_uri, :user_cover_photo, :user_birthday, :user_gender, :user_about_me, :user_password, :user_email, :user_email_code, :user_generated_string, :user_last_login, :user_ip, :user_education_history, :user_location, :user_creation_date)";
        $stmt = self::database()->prepare( $sql );

        $stmt->bindValue( ':user_id', $key);
        $stmt->bindValue( ':user_type', $argv['type'] ?? "Athlete");
        $stmt->bindValue( ':user_session_id', session_id());
        $stmt->bindValue( ':user_facebook_id', $argv['facebook_id'] ?? null);
        $stmt->bindValue( ':user_username', $argv['username']);
        $stmt->bindValue( ':user_first_name', $argv['first_name']);
        $stmt->bindValue( ':user_last_name', $argv['last_name']);
        $stmt->bindValue( ':user_profile_pic', $argv['profile_pic'] ?? null);
        $stmt->bindValue( ':user_profile_uri', $argv['profile_uri'] ?? null);
        $stmt->bindValue( ':user_cover_photo', $argv['cover_photo'] ?? null);
        $stmt->bindValue( ':user_birthday', $argv['birthday'] ?? null);
        $stmt->bindValue( ':user_gender', $argv['gender'] ?? null);
        $stmt->bindValue( ':user_about_me', $argv['about_me'] ?? null);
        $stmt->bindValue( ':user_password', Bcrypt::genHash($argv['password']));
        $stmt->bindValue( ':user_email', $argv['email']);
        $stmt->bindValue( ':user_email_code', $email_code = uniqid('code_', true));
        $stmt->bindValue( ':user_generated_string', null);
        $stmt->bindValue( ':user_last_login', time());
        $stmt->bindValue( ':user_ip', $_SERVER['REMOTE_ADDR'] ?? null);
        $stmt->bindValue( ':user_education_history', $argv['education_history'] ?? null);
        $stmt->bindValue( ':user_location', $argv['location'] ?? null);
        $stmt->bindValue( ':user_creation_date', time());

        if (!$stmt->execute()) throw new PublicAlert ('Your account could not be created.', 'danger');

        if (!self::database()->prepare('INSERT INTO StatsCoach.golf_stats (stats_id) VALUES (?)')->execute([$key]))
            throw new PublicAlert ('Your account could not be created.', 'danger');;

        if (self::commit()) $_SESSION['id'] = $key;

        return $email_code;
    }

    static function remove(&$user, $id)
    {
        // TODO: Implement remove() method.
    }

    static function all(&$user, $id)
    {
        $user = static::fetch('SELECT * FROM StatsCoach.user LEFT JOIN StatsCoach.carbon_tag ON entity_id = StatsCoach.user.user_id WHERE StatsCoach.user.user_id = ?', $id);
        if (!is_array($user)) throw new PublicAlert('Could not find user  ' . $id, 'danger');

        $user['user_profile_picture'] =  (!empty($user['user_profile_pic']) ? $user['user_profile_pic'] : SITE . 'Data/Uploads/Pictures/Defaults/default_avatar.png');
        $user['user_cover_photo'] =  ($user['user_cover_photo'] ??  SITE . 'Public/img/defaults/photo' . rand(1, 3) . '.png');
        $user['user_first_last'] = $user['user_full_name'] = $user['user_first_name'] . ' ' . $user['user_last_name'];
        $user['user_id'] = $id;

        Users::sport($user, $id);

        Events::refresh($user, $_SESSION['id']);

        return $user;
    }

    static function sport(&$user, $id)
    {
        if (!is_array($user)) throw new \InvalidArgumentException('Non Object Passed');

        $sport = $user['user_sport'];
        $sport = "Model\\$sport";

        if (!class_exists($sport)) return null;

        Teams::all($user, $id);

        $sport = new $sport;
        if ($sport instanceof iSport)                   // load stats
            return $sport->stats($user, $id);

        throw new PublicAlert('You ran into a big problem. Contact us for support.');
    }


    static function range(&$object, $id, $argv)
    {
        // TODO: Implement range() method.
    }

    static function user_id_from_uri(string $user_uri)
    {
        $stmt = self::database()->prepare('SELECT user_id FROM StatsCoach.user WHERE user_profile_uri = ? OR user_id = ?');
        $stmt->execute([$user_uri, $user_uri]);
        return $stmt->fetch(\PDO::FETCH_COLUMN);
    }

    static function changeStatus($status = false)
    {
        global $user;
        $sql = 'UPDATE StatsCoach.carbon_session SET user_online_status = ? WHERE user_id = ?';
        $stmt = self::database()->prepare($sql);
        $stmt->execute([$status, $_SESSION['id']]);
        return $user[$_SESSION['id']]['online'] = (bool)$stmt->fetchColumn();
    }

    private static function change_password($user_id, $password)
    {   /* Two create a Hash you do */
        $password = Bcrypt::genHash($password);
        return self::database()->prepare("UPDATE StatsCoach.user SET user_password = ? WHERE user_id = ?")->execute([$password, $user_id]);
    }

    static function onlineStatus($id): bool
    {
        global $user;
        $sql = 'SELECT user_online_status FROM StatsCoach.carbon_session WHERE user_id = ? LIMIT 1';
        self::fetch_into_class($user[$id], $sql, $id);
        return $user[$id]['user_online_status'];
    }

    static function user_exists($username): bool
    {
        $sql = 'SELECT COUNT(user_id) FROM StatsCoach.user WHERE user_username = ? LIMIT 1';
        $stmt = self::database()->prepare($sql);
        $stmt->execute([$username]);
        return $stmt->fetchColumn();
    }

    static function email_exists($email): bool
    {
        $sql = "SELECT COUNT(user_id) FROM StatsCoach.user WHERE `user_email`= ? LIMIT 1";
        $stmt = self::database()->prepare($sql);
        $stmt->execute(array($email));
        return $stmt->fetchColumn();
    }

    static function email_confirmed($username): bool
    {
        $sql = "SELECT user_email_confirmed FROM StatsCoach.user WHERE user_username = ? LIMIT 1";
        $stmt = self::database()->prepare($sql);
        $stmt->execute([$username]);
        return $stmt->fetchColumn();
    }
}