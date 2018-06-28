<?php

namespace Table;

use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Error\PublicAlert;
use CarbonPHP\Interfaces\iRest;

class carbon_users extends Entities implements iRest
{
    const COLUMNS = [
            'user_id',
            'user_type',
            'user_sport',
            'user_session_id',
            'user_facebook_id',
            'user_google_id',
            'user_username',
            'user_first_name',
            'user_last_name',
            'user_profile_pic',
            'user_profile_uri',
            'user_cover_photo',
            'user_birthday',
            'user_gender',
            'user_about_me',
            'user_rank',
            'user_password',
            'user_email',
            'user_email_code',
            'user_email_confirmed',
            'user_generated_string',
            'user_membership',
            'user_deactivated',
            'user_last_login',
            'user_ip',
            'user_education_history',
            'user_location',
            'user_creation_date',
    ];

    const PRIMARY = "user_id";

    /**
     * @param array $return
     * @param string|null $primary
     * @param array $argv
     * @return bool
     */
    public static function Get(array &$return, string $primary = null, array $argv) : bool
    {
        if (isset($argv['limit'])){
            if ($argv['limit'] !== '') {
                $pos = strrpos($argv['limit'], "><");
                if ($pos !== false) { // note: three equal signs
                    substr_replace($argv['limit'],',',$pos, 2);
                }
                $limit = ' LIMIT ' . $argv['limit'];
            } else {
                $limit = '';
            }
        } else {
            $limit = ' LIMIT 100';
        }

        $get = $where = [];
        foreach ($argv as $column => $value) {
            if (!is_int($column) && in_array($column, self::COLUMNS)) {
                if ($value !== '') {
                    $where[$column] = $value;
                } else {
                    $get[] = $column;
                }
            } elseif (in_array($value, self::COLUMNS)) {
                $get[] = $value;
            }
        }

        $get =  !empty($get) ? implode(", ", $get) : ' * ';

        $sql = 'SELECT ' .  $get . ' FROM statscoach.carbon_users';

        if ($primary === null) {
            $sql .= ' WHERE ';
            foreach ($where as $column => $value) {
                $sql .= "($column = " . Database::database()->quote($value) . ') AND ';
            }
            $sql = substr($sql, 0, strlen($sql)-4);
        } else if (!empty(self::PRIMARY)){
            $sql .= ' WHERE ' . self::PRIMARY . '=' . Database::database()->quote($primary);
        }

        $sql .= $limit;

        $return = self::fetch($sql);

        return true;
    }

    /**
    * @param array $argv
    * @return bool|mixed
    */
    public static function Post(array $argv)
    {
        $sql = 'INSERT INTO statscoach.carbon_users (user_id, user_type, user_sport, user_session_id, user_facebook_id, user_google_id, user_username, user_first_name, user_last_name, user_profile_pic, user_profile_uri, user_cover_photo, user_birthday, user_gender, user_about_me, user_rank, user_password, user_email, user_email_code, user_email_confirmed, user_generated_string, user_membership, user_deactivated, user_last_login, user_ip, user_education_history, user_location, user_creation_date) VALUES (:user_id, :user_type, :user_sport, :user_session_id, :user_facebook_id, :user_google_id, :user_username, :user_first_name, :user_last_name, :user_profile_pic, :user_profile_uri, :user_cover_photo, :user_birthday, :user_gender, :user_about_me, :user_rank, :user_password, :user_email, :user_email_code, :user_email_confirmed, :user_generated_string, :user_membership, :user_deactivated, :user_last_login, :user_ip, :user_education_history, :user_location, :user_creation_date)';
        $stmt = Database::database()->prepare($sql);
            $stmt->bindValue(':user_id', isset($argv['user_id']) ? $argv['user_id'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':user_type', isset($argv['user_type']) ? $argv['user_type'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':user_sport', isset($argv['user_sport']) ? $argv['user_sport'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':user_session_id', isset($argv['user_session_id']) ? $argv['user_session_id'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':user_facebook_id', isset($argv['user_facebook_id']) ? $argv['user_facebook_id'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':user_google_id', isset($argv['user_google_id']) ? $argv['user_google_id'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':user_username', isset($argv['user_username']) ? $argv['user_username'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':user_first_name', isset($argv['user_first_name']) ? $argv['user_first_name'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':user_last_name', isset($argv['user_last_name']) ? $argv['user_last_name'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':user_profile_pic', isset($argv['user_profile_pic']) ? $argv['user_profile_pic'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':user_profile_uri', isset($argv['user_profile_uri']) ? $argv['user_profile_uri'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':user_cover_photo', isset($argv['user_cover_photo']) ? $argv['user_cover_photo'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':user_birthday', isset($argv['user_birthday']) ? $argv['user_birthday'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':user_gender', isset($argv['user_gender']) ? $argv['user_gender'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':user_about_me', isset($argv['user_about_me']) ? $argv['user_about_me'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':user_rank', isset($argv['user_rank']) ? $argv['user_rank'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':user_password', isset($argv['user_password']) ? $argv['user_password'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':user_email', isset($argv['user_email']) ? $argv['user_email'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':user_email_code', isset($argv['user_email_code']) ? $argv['user_email_code'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':user_email_confirmed', isset($argv['user_email_confirmed']) ? $argv['user_email_confirmed'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':user_generated_string', isset($argv['user_generated_string']) ? $argv['user_generated_string'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':user_membership', isset($argv['user_membership']) ? $argv['user_membership'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':user_deactivated', isset($argv['user_deactivated']) ? $argv['user_deactivated'] : null, \PDO::PARAM_NULL);
            $stmt->bindValue(':user_last_login', isset($argv['user_last_login']) ? $argv['user_last_login'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':user_ip', isset($argv['user_ip']) ? $argv['user_ip'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':user_education_history', isset($argv['user_education_history']) ? $argv['user_education_history'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':user_location', isset($argv['user_location']) ? $argv['user_location'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':user_creation_date', isset($argv['user_creation_date']) ? $argv['user_creation_date'] : null, \PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
    * @param array $return
    * @param string $id
    * @param array $argv
    * @return bool
    */
    public static function Put(array &$return, string $id, array $argv) : bool
    {
        foreach ($argv as $key => $value) {
            if (!in_array($key, self::COLUMNS)){
                unset($argv[$key]);
            }
        }

        $sql = 'UPDATE statscoach.carbon_users ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';
        if (isset($argv['user_id'])) {
            $set .= 'user_id=:user_id,';
        }
        if (isset($argv['user_type'])) {
            $set .= 'user_type=:user_type,';
        }
        if (isset($argv['user_sport'])) {
            $set .= 'user_sport=:user_sport,';
        }
        if (isset($argv['user_session_id'])) {
            $set .= 'user_session_id=:user_session_id,';
        }
        if (isset($argv['user_facebook_id'])) {
            $set .= 'user_facebook_id=:user_facebook_id,';
        }
        if (isset($argv['user_google_id'])) {
            $set .= 'user_google_id=:user_google_id,';
        }
        if (isset($argv['user_username'])) {
            $set .= 'user_username=:user_username,';
        }
        if (isset($argv['user_first_name'])) {
            $set .= 'user_first_name=:user_first_name,';
        }
        if (isset($argv['user_last_name'])) {
            $set .= 'user_last_name=:user_last_name,';
        }
        if (isset($argv['user_profile_pic'])) {
            $set .= 'user_profile_pic=:user_profile_pic,';
        }
        if (isset($argv['user_profile_uri'])) {
            $set .= 'user_profile_uri=:user_profile_uri,';
        }
        if (isset($argv['user_cover_photo'])) {
            $set .= 'user_cover_photo=:user_cover_photo,';
        }
        if (isset($argv['user_birthday'])) {
            $set .= 'user_birthday=:user_birthday,';
        }
        if (isset($argv['user_gender'])) {
            $set .= 'user_gender=:user_gender,';
        }
        if (isset($argv['user_about_me'])) {
            $set .= 'user_about_me=:user_about_me,';
        }
        if (isset($argv['user_rank'])) {
            $set .= 'user_rank=:user_rank,';
        }
        if (isset($argv['user_password'])) {
            $set .= 'user_password=:user_password,';
        }
        if (isset($argv['user_email'])) {
            $set .= 'user_email=:user_email,';
        }
        if (isset($argv['user_email_code'])) {
            $set .= 'user_email_code=:user_email_code,';
        }
        if (isset($argv['user_email_confirmed'])) {
            $set .= 'user_email_confirmed=:user_email_confirmed,';
        }
        if (isset($argv['user_generated_string'])) {
            $set .= 'user_generated_string=:user_generated_string,';
        }
        if (isset($argv['user_membership'])) {
            $set .= 'user_membership=:user_membership,';
        }
        if (isset($argv['user_deactivated'])) {
            $set .= 'user_deactivated=:user_deactivated,';
        }
        if (isset($argv['user_last_login'])) {
            $set .= 'user_last_login=:user_last_login,';
        }
        if (isset($argv['user_ip'])) {
            $set .= 'user_ip=:user_ip,';
        }
        if (isset($argv['user_education_history'])) {
            $set .= 'user_education_history=:user_education_history,';
        }
        if (isset($argv['user_location'])) {
            $set .= 'user_location=:user_location,';
        }
        if (isset($argv['user_creation_date'])) {
            $set .= 'user_creation_date=:user_creation_date,';
        }

        if (empty($set)){
            return false;
        }

        $set = substr($set, 0, strlen($set)-1);

        $sql .= $set . ' WHERE ' . self::PRIMARY . "='$id'";

        $stmt = Database::database()->prepare($sql);

        if (isset($argv['user_id'])) {
            $stmt->bindValue(':user_id', $argv['user_id'], \PDO::PARAM_STR);
        }
        if (isset($argv['user_type'])) {
            $stmt->bindValue(':user_type', $argv['user_type'], \PDO::PARAM_STR);
        }
        if (isset($argv['user_sport'])) {
            $stmt->bindValue(':user_sport', $argv['user_sport'], \PDO::PARAM_STR);
        }
        if (isset($argv['user_session_id'])) {
            $stmt->bindValue(':user_session_id', $argv['user_session_id'], \PDO::PARAM_STR);
        }
        if (isset($argv['user_facebook_id'])) {
            $stmt->bindValue(':user_facebook_id', $argv['user_facebook_id'], \PDO::PARAM_STR);
        }
        if (isset($argv['user_google_id'])) {
            $stmt->bindValue(':user_google_id', $argv['user_google_id'], \PDO::PARAM_STR);
        }
        if (isset($argv['user_username'])) {
            $stmt->bindValue(':user_username', $argv['user_username'], \PDO::PARAM_STR);
        }
        if (isset($argv['user_first_name'])) {
            $stmt->bindValue(':user_first_name', $argv['user_first_name'], \PDO::PARAM_STR);
        }
        if (isset($argv['user_last_name'])) {
            $stmt->bindValue(':user_last_name', $argv['user_last_name'], \PDO::PARAM_STR);
        }
        if (isset($argv['user_profile_pic'])) {
            $stmt->bindValue(':user_profile_pic', $argv['user_profile_pic'], \PDO::PARAM_STR);
        }
        if (isset($argv['user_profile_uri'])) {
            $stmt->bindValue(':user_profile_uri', $argv['user_profile_uri'], \PDO::PARAM_STR);
        }
        if (isset($argv['user_cover_photo'])) {
            $stmt->bindValue(':user_cover_photo', $argv['user_cover_photo'], \PDO::PARAM_STR);
        }
        if (isset($argv['user_birthday'])) {
            $stmt->bindValue(':user_birthday', $argv['user_birthday'], \PDO::PARAM_STR);
        }
        if (isset($argv['user_gender'])) {
            $stmt->bindValue(':user_gender', $argv['user_gender'], \PDO::PARAM_STR);
        }
        if (isset($argv['user_about_me'])) {
            $stmt->bindValue(':user_about_me', $argv['user_about_me'], \PDO::PARAM_STR);
        }
        if (isset($argv['user_rank'])) {
            $stmt->bindValue(':user_rank', $argv['user_rank'], \PDO::PARAM_STR);
        }
        if (isset($argv['user_password'])) {
            $stmt->bindValue(':user_password', $argv['user_password'], \PDO::PARAM_STR);
        }
        if (isset($argv['user_email'])) {
            $stmt->bindValue(':user_email', $argv['user_email'], \PDO::PARAM_STR);
        }
        if (isset($argv['user_email_code'])) {
            $stmt->bindValue(':user_email_code', $argv['user_email_code'], \PDO::PARAM_STR);
        }
        if (isset($argv['user_email_confirmed'])) {
            $stmt->bindValue(':user_email_confirmed', $argv['user_email_confirmed'], \PDO::PARAM_STR);
        }
        if (isset($argv['user_generated_string'])) {
            $stmt->bindValue(':user_generated_string', $argv['user_generated_string'], \PDO::PARAM_STR);
        }
        if (isset($argv['user_membership'])) {
            $stmt->bindValue(':user_membership', $argv['user_membership'], \PDO::PARAM_STR);
        }
        if (isset($argv['user_deactivated'])) {
            $stmt->bindValue(':user_deactivated', $argv['user_deactivated'], \PDO::PARAM_NULL);
        }
        if (isset($argv['user_last_login'])) {
            $stmt->bindValue(':user_last_login', $argv['user_last_login'], \PDO::PARAM_STR);
        }
        if (isset($argv['user_ip'])) {
            $stmt->bindValue(':user_ip', $argv['user_ip'], \PDO::PARAM_STR);
        }
        if (isset($argv['user_education_history'])) {
            $stmt->bindValue(':user_education_history', $argv['user_education_history'], \PDO::PARAM_STR);
        }
        if (isset($argv['user_location'])) {
            $stmt->bindValue(':user_location', $argv['user_location'], \PDO::PARAM_STR);
        }
        if (isset($argv['user_creation_date'])) {
            $stmt->bindValue(':user_creation_date', $argv['user_creation_date'], \PDO::PARAM_STR);
        }


        if (!$stmt->execute()){
            return false;
        }

        $return = array_merge($return, $argv);

        return true;

    }

    /**
    * @param array $return
    * @param string|null $primary
    * @param array $argv
    * @return bool
    */
    public static function Delete(array &$remove, string $primary = null, array $argv) : bool
    {
        $sql = 'DELETE FROM statscoach.carbon_users ';

        foreach($argv as $column => $constraint){
            if (!in_array($column, self::COLUMNS)){
                unset($argv[$column]);
            }
        }

        if ($primary === null) {
            /**
            *   While useful, we've decided to disallow full
            *   table deletions through the rest api. For the
            *   n00bs and future self, "I got chu."
            */
            if (empty($argv)) {
                return false;
            }
            $sql .= ' WHERE ';
            foreach ($argv as $column => $value) {
                $sql .= " $column =" . Database::database()->quote($value) . ' AND ';
            }
            $sql = substr($sql, 0, strlen($sql)-4);
        } else if (!empty(self::PRIMARY)) {
            $sql .= ' WHERE ' . self::PRIMARY . '=' . Database::database()->quote($primary);
        }

        $remove = null;

        return self::execute($sql);
    }

}