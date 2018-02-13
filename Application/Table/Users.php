<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 9/3/17
 * Time: 11:16 PM
 */

namespace Table;

use Carbon\Database;
use Carbon\Error\ErrorCatcher;
use Carbon\Error\PublicAlert;
use Carbon\Helpers\Bcrypt;
use Carbon\Entities;
use Carbon\Interfaces\iTable;

class Users extends Entities implements iTable
{

    public static function Get(array &$array, string $id, array $argv): bool
    {
        $array = static::fetch('SELECT * FROM carbon_users WHERE user_id = ?', $id);
        $array['user_profile_pic'] = SITE . (!empty($user['user_profile_pic']) ? $user['user_profile_pic'] : 'Data/Uploads/Pictures/Defaults/default_avatar.png');
        $array['user_profile_uri'] = $array['user_profile_uri'] ?: $id;
        $array['user_cover_photo'] = SITE . ($array['user_cover_photo'] ?? APP_VIEW . 'Img/defaults/photo' . rand(1, 3) . '.png');
        $array['user_first_last'] = $array['user_full_name'] = $array['user_first_name'] . ' ' . $array['user_last_name'];
        $array['user_id'] = $id;
        return true;
    }

    /**
     * @param array $array
     * @return bool
     * @throws PublicAlert
     */
    public static function Post(array $array): bool      // object and id will be null
    {
        $key = self::beginTransaction(USER);         // Begin transaction

        $sql = 'INSERT INTO carbon_users (user_id, user_type, user_session_id, user_facebook_id, user_username, user_first_name, user_last_name, user_profile_pic, user_profile_uri, user_cover_photo, user_birthday, user_gender, user_about_me, user_password, user_email, user_email_code, user_generated_string, user_last_login, user_ip, user_education_history, user_location, user_creation_date) VALUES 
        (:user_id, :user_type, :user_session_id, :user_facebook_id, :user_username, :user_first_name, :user_last_name, :user_profile_pic, :user_profile_uri, :user_cover_photo, :user_birthday, :user_gender, :user_about_me, :user_password, :user_email, :user_email_code, :user_generated_string, :user_last_login, :user_ip, :user_education_history, :user_location, :user_creation_date)';
        $stmt = Database::database()->prepare($sql);

        $stmt->bindValue(':user_id', $key);
        $stmt->bindValue(':user_type', $array['type'] ?? "Athlete");
        $stmt->bindValue(':user_session_id', session_id());
        $stmt->bindValue(':user_facebook_id', $array['facebook_id'] ?? null);
        $stmt->bindValue(':user_username', $array['username']);
        $stmt->bindValue(':user_first_name', $array['first_name']);
        $stmt->bindValue(':user_last_name', $array['last_name']);
        $stmt->bindValue(':user_profile_pic', $array['profile_pic'] ?? null);
        $stmt->bindValue(':user_profile_uri', $array['profile_uri'] ?? null);
        $stmt->bindValue(':user_cover_photo', $array['cover_photo'] ?? null);
        $stmt->bindValue(':user_birthday', $array['birthday'] ?? null);
        $stmt->bindValue(':user_gender', $array['gender'] ?? null);
        $stmt->bindValue(':user_about_me', $array['about_me'] ?? null);
        $stmt->bindValue(':user_password', Bcrypt::genHash($array['password']));
        $stmt->bindValue(':user_email', $array['email']);
        $stmt->bindValue(':user_email_code', $email_code = uniqid('code_', true));
        $stmt->bindValue(':user_generated_string', null);
        $stmt->bindValue(':user_last_login', time());
        $stmt->bindValue(':user_ip', $_SERVER['REMOTE_ADDR'] ?? null);
        $stmt->bindValue(':user_education_history', $array['education_history'] ?? null);
        $stmt->bindValue(':user_location', $array['location'] ?? null);
        $stmt->bindValue(':user_creation_date', time());

        if (!$stmt->execute()) {
            throw new PublicAlert ('Your account could not be created.', 'danger');
        }

        if (self::commit()) {
            $_SESSION['id'] = $key;
        }

        $subject = 'Your ' . SITE_TITLE . ' Password';
        $headers = 'From: ' . SYSTEM_EMAIL . "\r\n" .
            'Reply-To: ' . REPLY_EMAIL . "\r\n" .
            'X-Mailer: PHP/' . PHP_VERSION;

        $message = "Hello {$array['first_name']},
            \r\nThank you for registering with " . SITE_TITLE .
            "\r\n Username :  {$array['username']} 
            \r\n Password :  {$array['password']}
            \r\n Please visit the link below so we can activate your account:\r\n\r\n
            " . SITE . '/Activate/' . base64_encode($array['email']) . '/' . base64_encode($email_code) . "/ \r\n\r\n Have a good day! \r\n--" . SITE;

        if (!mail($array['email'], $subject, $message, $headers)) {
            ErrorCatcher::generateLog([$array['email'], $subject, $message, $headers]);
            PublicAlert::danger('We failed to send your activation email, this is hella bad. Leave me a messages at 817-7893-294');
        }
        return true;
    }

    public static function Put(array &$array, string $id, array $argv): bool
    {
        // TODO: Implement Put() method.
        return true;
    }

    public static function Delete(array &$user, string $id): bool
    {
        self::remove_entity($id);
        $_SESSION['id'] = false;
        return true;
    }

    public static function All(array &$user, string $id): bool
    {
        $user = static::fetch('SELECT * FROM carbon_users LEFT JOIN carbon_tag ON entity_id = user.user_id WHERE user.user_id = ?', $id);
        if (!\is_array($user)) {
            throw new PublicAlert('Could not find user  ' . $id, 'danger');
        }

        $user['user_profile_pic'] = (!empty($user['user_profile_pic']) ? $user['user_profile_pic'] : SITE . 'Data/Uploads/Pictures/Defaults/default_avatar.png');
        $user['user_profile_uri'] = $user['user_profile_uri'] ?: $id;
        $user['user_cover_photo'] = ($user['user_cover_photo'] ?? SITE . 'Public/Img/defaults/photo' . rand(1, 3) . '.png');
        $user['user_first_last'] = $user['user_full_name'] = $user['user_first_name'] . ' ' . $user['user_last_name'];
        $user['user_id'] = $id;

        return true;
    }

    public static function user_id_from_uri(string $user_uri)
    {
        $stmt = Database::database()->prepare('SELECT user_id FROM carbon_users WHERE user_profile_uri = ? OR user_id = ?');
        $stmt->execute([$user_uri, $user_uri]);
        return $stmt->fetch(\PDO::FETCH_COLUMN);
    }

    public static function changeStatus($status = false)
    {
        global $user;
        $sql = 'UPDATE carbon_sessions SET user_online_status = ? WHERE user_id = ?';
        $stmt = Database::database()->prepare($sql);
        $stmt->execute([$status, $_SESSION['id']]);
        return $user[$_SESSION['id']]['online'] = (bool)$stmt->fetchColumn();
    }

    public static function change_password($password)
    {   /* Two create a Hash you do */
        $password = Bcrypt::genHash($password);
        return Database::database()->prepare('UPDATE carbon_users SET user_password = ? WHERE user_id = ?')->execute([$password, $_SESSION['id']]);
    }

    public static function onlineStatus($id): bool
    {
        global $user;
        $sql = 'SELECT user_online_status FROM carbon_sessions WHERE user_id = ? LIMIT 1';
        self::fetch_into_class($user[$id], $sql, $id);
        return $user[$id]['user_online_status'];
    }

    public static function user_exists($username_or_id): bool
    {
        return self::fetch('SELECT COUNT(*) FROM carbon_users WHERE user_username = ? OR user_id = ? LIMIT 1', $username_or_id, $username_or_id)['COUNT(*)'];
    }

    public static function email_exists($email): bool
    {
        $sql = 'SELECT COUNT(user_id) FROM carbon_users WHERE `user_email`= ? LIMIT 1';
        $stmt = Database::database()->prepare($sql);
        $stmt->execute(array($email));
        return $stmt->fetchColumn();
    }

    public static function email_confirmed($username): bool
    {
        $sql = 'SELECT user_email_confirmed FROM carbon_users WHERE user_username = ? LIMIT 1';
        $stmt = Database::database()->prepare($sql);
        $stmt->execute([$username]);
        return $stmt->fetchColumn();
    }
}