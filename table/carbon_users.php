<?php
namespace Table;


use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Interfaces\iRest;

class carbon_users extends Entities implements iRest
{
    const PRIMARY = [
    'user_id',
    ];

    const COLUMNS = [
    'user_id','user_type','user_sport','user_session_id','user_facebook_id','user_username','user_first_name','user_last_name','user_profile_pic','user_profile_uri','user_cover_photo','user_birthday','user_gender','user_about_me','user_rank','user_password','user_email','user_email_code','user_email_confirmed','user_generated_string','user_membership','user_deactivated','user_last_login','user_ip','user_education_history','user_location','user_creation_date',
    ];

    const VALIDATION = [];

    const BINARY = [
    'user_id',
    ];

    /**
     * @param array $return
     * @param string|null $primary
     * @param array $argv
     * @return bool
     */
    public static function Get(array &$return, string $primary = null, array $argv) : bool
    {
        $get = isset($argv['select']) ? $argv['select'] : self::COLUMNS;
        $where = isset($argv['where']) ? $argv['where'] : [];

        $group = $sql = '';

        if (isset($argv['pagination'])) {
            if (!empty($argv['pagination']) && !is_array($argv['pagination'])) {
                $argv['pagination'] = json_decode($argv['pagination'], true);
            }
            if (isset($argv['pagination']['limit']) && $argv['pagination']['limit'] != null) {
                $pos = strrpos($argv['pagination']['limit'], "><");
                if ($pos !== false) { // note: three equal signs
                    substr_replace($argv['pagination']['limit'],',',$pos, 2);
                }
                $limit = ' LIMIT ' . $argv['pagination']['limit'];
            } else {
                $limit = '';
            }
        } else {
            $limit = ' LIMIT 100';
        }

        foreach($get as $key => $column){
            if (!empty($sql)) {
                $sql .= ', ';
                $group .= ', ';
            }
            if (in_array($column, self::BINARY)) {
                $sql .= "HEX($column) as $column";
                $group .= "$column";
            } else {
                $sql .= $column;
                $group .= $column;
            }
        }

        if (isset($argv['aggregate']) && (is_array($argv['aggregate']) || $argv['aggregate'] = json_decode($argv['aggregate'], true))) {
            foreach($argv['aggregate'] as $key => $value){
                switch ($key){
                    case 'count':
                        if (!empty($sql)) {
                            $sql .= ', ';
                        }
                        $sql .= "COUNT($value) AS count ";
                        break;
                    case 'AVG':
                        if (!empty($sql)) {
                            $sql .= ', ';
                        }
                        $sql .= "AVG($value) AS avg ";
                        break;
                    case 'MIN':
                        if (!empty($sql)) {
                            $sql .= ', ';
                        }
                        $sql .= "MIN($value) AS min ";
                        break;
                    case 'MAX':
                        if (!empty($sql)) {
                            $sql .= ', ';
                        }
                        $sql .= "MAX($value) AS max ";
                        break;
                }
            }
        }

        $sql = 'SELECT ' .  $sql . ' FROM statscoach.carbon_users';

        $pdo = Database::database();

        if (empty($primary)) {
            if (!empty($where)) {
                $build_where = function (array $set, $join = 'AND') use (&$pdo, &$build_where) {
                    $sql = '(';
                    foreach ($set as $column => $value) {
                        if (is_array($value)) {
                            $sql .= $build_where($value, $join === 'AND' ? 'OR' : 'AND');
                        } else {
                            if (in_array($column, self::BINARY)) {
                                $sql .= "($column = UNHEX(" . $pdo->quote($value) . ")) $join ";
                            } else {
                                $sql .= "($column = " . $pdo->quote($value) . ") $join ";
                            }
                        }
                    }
                    return substr($sql, 0, strlen($sql) - (strlen($join) + 1)) . ')';
                };
                $sql .= ' WHERE ' . $build_where($where);
            }
        } else {
            $primary = $pdo->quote($primary);
            $sql .= ' WHERE  user_id=UNHEX(' . $primary .')';
        }

        if (isset($argv['aggregate'])) {
            $sql .= ' GROUP BY ' . $group . ' ';
        }

        $sql .= $limit;

        $return = self::fetch($sql);

        global $json;

        if (!isset($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;

        /**
        *   The next part is so every response from the rest api
        *   formats to a set of rows. Even if only one row is returned.
        *   You must set the third parameter to true, otherwise '0' is
        *   apparently in the self::COLUMNS
        */

        
        if (empty($primary) && count($return) && in_array(array_keys($return)[0], self::COLUMNS, true)) {  // You must set tr
            $return = [$return];
        }

        return true;
    }

    /**
    * @param array $argv
    * @return bool|mixed
    */
    public static function Post(array $argv)
    {
        $sql = 'INSERT INTO statscoach.carbon_users (user_id, user_type, user_sport, user_session_id, user_facebook_id, user_username, user_first_name, user_last_name, user_profile_pic, user_profile_uri, user_cover_photo, user_birthday, user_gender, user_about_me, user_rank, user_password, user_email, user_email_code, user_email_confirmed, user_generated_string, user_membership, user_deactivated, user_last_login, user_ip, user_education_history, user_location, user_creation_date) VALUES ( UNHEX(:user_id), :user_type, :user_sport, :user_session_id, :user_facebook_id, :user_username, :user_first_name, :user_last_name, :user_profile_pic, :user_profile_uri, :user_cover_photo, :user_birthday, :user_gender, :user_about_me, :user_rank, :user_password, :user_email, :user_email_code, :user_email_confirmed, :user_generated_string, :user_membership, :user_deactivated, :user_last_login, :user_ip, :user_education_history, :user_location, :user_creation_date)';
        $stmt = sDatabaseelf::database()->prepare($sql);

        global $json;

        if (!isset($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;

            $user_id = $id = isset($argv['user_id']) ? $argv['user_id'] : self::new_entity('carbon_users');
            $stmt->bindParam(':user_id',$user_id, 2, 16);
            
                $user_type = $argv['user_type'];
                $stmt->bindParam(':user_type',$user_type, 2, 20);
                    
                $user_sport = isset($argv['user_sport']) ? $argv['user_sport'] : null;
                $stmt->bindParam(':user_sport',$user_sport, 2, 20);
                    
                $user_session_id = isset($argv['user_session_id']) ? $argv['user_session_id'] : null;
                $stmt->bindParam(':user_session_id',$user_session_id, 2, 225);
                    
                $user_facebook_id = isset($argv['user_facebook_id']) ? $argv['user_facebook_id'] : null;
                $stmt->bindParam(':user_facebook_id',$user_facebook_id, 2, 225);
                    
                $user_username = $argv['user_username'];
                $stmt->bindParam(':user_username',$user_username, 2, 25);
                    
                $user_first_name = $argv['user_first_name'];
                $stmt->bindParam(':user_first_name',$user_first_name, 2, 25);
                    
                $user_last_name = $argv['user_last_name'];
                $stmt->bindParam(':user_last_name',$user_last_name, 2, 25);
                    
                $user_profile_pic = isset($argv['user_profile_pic']) ? $argv['user_profile_pic'] : null;
                $stmt->bindParam(':user_profile_pic',$user_profile_pic, 2, 225);
                    
                $user_profile_uri = isset($argv['user_profile_uri']) ? $argv['user_profile_uri'] : null;
                $stmt->bindParam(':user_profile_uri',$user_profile_uri, 2, 225);
                    
                $user_cover_photo = isset($argv['user_cover_photo']) ? $argv['user_cover_photo'] : null;
                $stmt->bindParam(':user_cover_photo',$user_cover_photo, 2, 225);
                    $stmt->bindValue(':user_birthday',$argv['user_birthday'], \2);
                    
                $user_gender = isset($argv['user_gender']) ? $argv['user_gender'] : null;
                $stmt->bindParam(':user_gender',$user_gender, 2, 25);
                    $stmt->bindValue(':user_about_me',$argv['user_about_me'], \2);
                    
                $user_rank = isset($argv['user_rank']) ? $argv['user_rank'] : '0';
                $stmt->bindParam(':user_rank',$user_rank, 2, 8);
                    
                $user_password = isset($argv['user_password']) ? $argv['user_password'] : null;
                $stmt->bindParam(':user_password',$user_password, 2, 225);
                    
                $user_email = isset($argv['user_email']) ? $argv['user_email'] : null;
                $stmt->bindParam(':user_email',$user_email, 2, 50);
                    
                $user_email_code = isset($argv['user_email_code']) ? $argv['user_email_code'] : null;
                $stmt->bindParam(':user_email_code',$user_email_code, 2, 225);
                    
                $user_email_confirmed = isset($argv['user_email_confirmed']) ? $argv['user_email_confirmed'] : '0';
                $stmt->bindParam(':user_email_confirmed',$user_email_confirmed, 2, 20);
                    
                $user_generated_string = isset($argv['user_generated_string']) ? $argv['user_generated_string'] : null;
                $stmt->bindParam(':user_generated_string',$user_generated_string, 2, 200);
                    
                $user_membership = isset($argv['user_membership']) ? $argv['user_membership'] : '0';
                $stmt->bindParam(':user_membership',$user_membership, 2, 10);
                    
                $user_deactivated = isset($argv['user_deactivated']) ? $argv['user_deactivated'] : '0';
                $stmt->bindParam(':user_deactivated',$user_deactivated, 0, 1);
                    
                $user_last_login = $argv['user_last_login'];
                $stmt->bindParam(':user_last_login',$user_last_login, 2, 14);
                    
                $user_ip = $argv['user_ip'];
                $stmt->bindParam(':user_ip',$user_ip, 2, 20);
                    $stmt->bindValue(':user_education_history',$argv['user_education_history'], \2);
                    $stmt->bindValue(':user_location',$argv['user_location'], \2);
                    
                $user_creation_date = isset($argv['user_creation_date']) ? $argv['user_creation_date'] : null;
                $stmt->bindParam(':user_creation_date',$user_creation_date, 2, 14);
        
        return $stmt->execute() ? $id : false;

    }

    /**
    * @param array $return
    * @param string $primary
    * @param array $argv
    * @return bool
    */
    public static function Put(array &$return, string $primary, array $argv) : bool
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
            $set .= 'user_id=UNHEX(:user_id),';
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

        $sql .= substr($set, 0, strlen($set)-1);

        $db = Database::database();

        
        $primary = $db->quote($primary);
        $sql .= ' WHERE  user_id=UNHEX(' . $primary .')';

        $stmt = $db->prepare($sql);

        global $json;

        if (!isset($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;


        if (isset($argv['user_id'])) {
            $user_id = 'UNHEX('.$argv['user_id'].')';
            $stmt->bindParam(':user_id', $user_id, 2, 16);
        }
        if (isset($argv['user_type'])) {
            $user_type = $argv['user_type'];
            $stmt->bindParam(':user_type',$user_type, 2, 20);
        }
        if (isset($argv['user_sport'])) {
            $user_sport = $argv['user_sport'];
            $stmt->bindParam(':user_sport',$user_sport, 2, 20);
        }
        if (isset($argv['user_session_id'])) {
            $user_session_id = $argv['user_session_id'];
            $stmt->bindParam(':user_session_id',$user_session_id, 2, 225);
        }
        if (isset($argv['user_facebook_id'])) {
            $user_facebook_id = $argv['user_facebook_id'];
            $stmt->bindParam(':user_facebook_id',$user_facebook_id, 2, 225);
        }
        if (isset($argv['user_username'])) {
            $user_username = $argv['user_username'];
            $stmt->bindParam(':user_username',$user_username, 2, 25);
        }
        if (isset($argv['user_first_name'])) {
            $user_first_name = $argv['user_first_name'];
            $stmt->bindParam(':user_first_name',$user_first_name, 2, 25);
        }
        if (isset($argv['user_last_name'])) {
            $user_last_name = $argv['user_last_name'];
            $stmt->bindParam(':user_last_name',$user_last_name, 2, 25);
        }
        if (isset($argv['user_profile_pic'])) {
            $user_profile_pic = $argv['user_profile_pic'];
            $stmt->bindParam(':user_profile_pic',$user_profile_pic, 2, 225);
        }
        if (isset($argv['user_profile_uri'])) {
            $user_profile_uri = $argv['user_profile_uri'];
            $stmt->bindParam(':user_profile_uri',$user_profile_uri, 2, 225);
        }
        if (isset($argv['user_cover_photo'])) {
            $user_cover_photo = $argv['user_cover_photo'];
            $stmt->bindParam(':user_cover_photo',$user_cover_photo, 2, 225);
        }
        if (isset($argv['user_birthday'])) {
            $stmt->bindValue(':user_birthday',$argv['user_birthday'], 2);
        }
        if (isset($argv['user_gender'])) {
            $user_gender = $argv['user_gender'];
            $stmt->bindParam(':user_gender',$user_gender, 2, 25);
        }
        if (isset($argv['user_about_me'])) {
            $stmt->bindValue(':user_about_me',$argv['user_about_me'], 2);
        }
        if (isset($argv['user_rank'])) {
            $user_rank = $argv['user_rank'];
            $stmt->bindParam(':user_rank',$user_rank, 2, 8);
        }
        if (isset($argv['user_password'])) {
            $user_password = $argv['user_password'];
            $stmt->bindParam(':user_password',$user_password, 2, 225);
        }
        if (isset($argv['user_email'])) {
            $user_email = $argv['user_email'];
            $stmt->bindParam(':user_email',$user_email, 2, 50);
        }
        if (isset($argv['user_email_code'])) {
            $user_email_code = $argv['user_email_code'];
            $stmt->bindParam(':user_email_code',$user_email_code, 2, 225);
        }
        if (isset($argv['user_email_confirmed'])) {
            $user_email_confirmed = $argv['user_email_confirmed'];
            $stmt->bindParam(':user_email_confirmed',$user_email_confirmed, 2, 20);
        }
        if (isset($argv['user_generated_string'])) {
            $user_generated_string = $argv['user_generated_string'];
            $stmt->bindParam(':user_generated_string',$user_generated_string, 2, 200);
        }
        if (isset($argv['user_membership'])) {
            $user_membership = $argv['user_membership'];
            $stmt->bindParam(':user_membership',$user_membership, 2, 10);
        }
        if (isset($argv['user_deactivated'])) {
            $user_deactivated = $argv['user_deactivated'];
            $stmt->bindParam(':user_deactivated',$user_deactivated, 0, 1);
        }
        if (isset($argv['user_last_login'])) {
            $user_last_login = $argv['user_last_login'];
            $stmt->bindParam(':user_last_login',$user_last_login, 2, 14);
        }
        if (isset($argv['user_ip'])) {
            $user_ip = $argv['user_ip'];
            $stmt->bindParam(':user_ip',$user_ip, 2, 20);
        }
        if (isset($argv['user_education_history'])) {
            $stmt->bindValue(':user_education_history',$argv['user_education_history'], 2);
        }
        if (isset($argv['user_location'])) {
            $stmt->bindValue(':user_location',$argv['user_location'], 2);
        }
        if (isset($argv['user_creation_date'])) {
            $user_creation_date = $argv['user_creation_date'];
            $stmt->bindParam(':user_creation_date',$user_creation_date, 2, 14);
        }

        if (!$stmt->execute()){
            return false;
        }

        $return = array_merge($return, $argv);

        return true;

    }

    /**
    * @param array $remove
    * @param string|null $primary
    * @param array $argv
    * @return bool
    */
    public static function Delete(array &$remove, string $primary = null, array $argv) : bool
    {
        return \Table\carbon::Delete($remove, $primary, $argv);
    }
}