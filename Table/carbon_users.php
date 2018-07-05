<?php

namespace Table;

use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Interfaces\iRest;

class carbon_users extends Entities implements iRest
{
    const PRIMARY = "user_id";

    const COLUMNS = [
    'user_id','user_type','user_sport','user_session_id','user_facebook_id','user_username','user_first_name','user_last_name','user_profile_pic','user_profile_uri','user_cover_photo','user_birthday','user_gender','user_about_me','user_rank','user_password','user_email','user_email_code','user_email_confirmed','user_generated_string','user_membership','user_deactivated','user_ip','user_education_history','user_location','user_creation_date',
    ];

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

        $get = isset($argv['select']) ? $argv['select'] : self::COLUMNS;
        $where = isset($argv['where']) ? $argv['where'] : [];

        $sql = '';
        foreach($get as $key => $column){
            if (!empty($sql)) {
                $sql .= ', ';
            }
            if (in_array($column, self::BINARY)) {
                $sql .= "HEX($column) as $column";
            } else {
                $sql .= $column;
            }
        }

        $sql = 'SELECT ' .  $sql . ' FROM statscoach.carbon_users';

        $pdo = Database::database();

        if ($primary === null) {
            if (!empty($where)) {
                $build_where = function (array $set, $join = 'AND') use (&$pdo, &$build_where) {
                    $sql = '(';
                    foreach ($set as $column => $value) {
                        if (is_array($value)) {
                            $build_where($value, $join === 'AND' ? 'OR' : 'AND');
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
        } else if (!empty(self::PRIMARY)){
            $sql .= ' WHERE ' . self::PRIMARY . '=UNHEX(' . $pdo->quote($primary) . ')';
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
        $sql = 'INSERT INTO statscoach.carbon_users (user_id, user_type, user_sport, user_session_id, user_facebook_id, user_username, user_first_name, user_last_name, user_profile_pic, user_profile_uri, user_cover_photo, user_birthday, user_gender, user_about_me, user_rank, user_password, user_email, user_email_code, user_email_confirmed, user_generated_string, user_membership, user_deactivated, user_ip, user_education_history, user_location, user_creation_date) VALUES ( :user_id, :user_type, :user_sport, :user_session_id, :user_facebook_id, :user_username, :user_first_name, :user_last_name, :user_profile_pic, :user_profile_uri, :user_cover_photo, :user_birthday, :user_gender, :user_about_me, :user_rank, :user_password, :user_email, :user_email_code, :user_email_confirmed, :user_generated_string, :user_membership, :user_deactivated, :user_ip, :user_education_history, :user_location, :user_creation_date)';
        $stmt = Database::database()->prepare($sql);
            $user_id = $id = self::new_entity('carbon_users');

            var_dump($id);
            exit(1);


            $stmt->bindParam(':user_id',$user_id, \PDO::PARAM_STR, 16);
            
                $user_type = isset($argv['user_type']) ? $argv['user_type'] : null;
                $stmt->bindParam(':user_type',$user_type, \PDO::PARAM_STR, 20);
                    
                $user_sport = isset($argv['user_sport']) ? $argv['user_sport'] : null;
                $stmt->bindParam(':user_sport',$user_sport, \PDO::PARAM_STR, 20);
                    
                $user_session_id = isset($argv['user_session_id']) ? $argv['user_session_id'] : null;
                $stmt->bindParam(':user_session_id',$user_session_id, \PDO::PARAM_STR, 225);
                    
                $user_facebook_id = isset($argv['user_facebook_id']) ? $argv['user_facebook_id'] : null;
                $stmt->bindParam(':user_facebook_id',$user_facebook_id, \PDO::PARAM_STR, 225);
                    
                $user_username = isset($argv['user_username']) ? $argv['user_username'] : null;
                $stmt->bindParam(':user_username',$user_username, \PDO::PARAM_STR, 25);
                    
                $user_first_name = isset($argv['user_first_name']) ? $argv['user_first_name'] : null;
                $stmt->bindParam(':user_first_name',$user_first_name, \PDO::PARAM_STR, 25);
                    
                $user_last_name = isset($argv['user_last_name']) ? $argv['user_last_name'] : null;
                $stmt->bindParam(':user_last_name',$user_last_name, \PDO::PARAM_STR, 25);
                    
                $user_profile_pic = isset($argv['user_profile_pic']) ? $argv['user_profile_pic'] : null;
                $stmt->bindParam(':user_profile_pic',$user_profile_pic, \PDO::PARAM_STR, 225);
                    
                $user_profile_uri = isset($argv['user_profile_uri']) ? $argv['user_profile_uri'] : null;
                $stmt->bindParam(':user_profile_uri',$user_profile_uri, \PDO::PARAM_STR, 225);
                    
                $user_cover_photo = isset($argv['user_cover_photo']) ? $argv['user_cover_photo'] : null;
                $stmt->bindParam(':user_cover_photo',$user_cover_photo, \PDO::PARAM_STR, 225);
                    $stmt->bindValue(':user_birthday',isset($argv['user_birthday']) ? $argv['user_birthday'] : null, \PDO::PARAM_STR);
                    
                $user_gender = isset($argv['user_gender']) ? $argv['user_gender'] : null;
                $stmt->bindParam(':user_gender',$user_gender, \PDO::PARAM_STR, 25);
                    $stmt->bindValue(':user_about_me',isset($argv['user_about_me']) ? $argv['user_about_me'] : null, \PDO::PARAM_STR);
                    
                $user_rank = isset($argv['user_rank']) ? $argv['user_rank'] : '0';
                $stmt->bindParam(':user_rank',$user_rank, \PDO::PARAM_STR, 8);
                    
                $user_password = isset($argv['user_password']) ? $argv['user_password'] : null;
                $stmt->bindParam(':user_password',$user_password, \PDO::PARAM_STR, 225);
                    
                $user_email = isset($argv['user_email']) ? $argv['user_email'] : null;
                $stmt->bindParam(':user_email',$user_email, \PDO::PARAM_STR, 50);
                    
                $user_email_code = isset($argv['user_email_code']) ? $argv['user_email_code'] : null;
                $stmt->bindParam(':user_email_code',$user_email_code, \PDO::PARAM_STR, 225);
                    
                $user_email_confirmed = isset($argv['user_email_confirmed']) ? $argv['user_email_confirmed'] : '0';
                $stmt->bindParam(':user_email_confirmed',$user_email_confirmed, \PDO::PARAM_STR, 20);
                    
                $user_generated_string = isset($argv['user_generated_string']) ? $argv['user_generated_string'] : null;
                $stmt->bindParam(':user_generated_string',$user_generated_string, \PDO::PARAM_STR, 200);
                    
                $user_membership = isset($argv['user_membership']) ? $argv['user_membership'] : '0';
                $stmt->bindParam(':user_membership',$user_membership, \PDO::PARAM_STR, 10);
                    
                $user_deactivated = isset($argv['user_deactivated']) ? $argv['user_deactivated'] : '0';
                $stmt->bindParam(':user_deactivated',$user_deactivated, \PDO::PARAM_NULL, 1);
                    
                $user_ip = isset($argv['user_ip']) ? $argv['user_ip'] : '0';
                $stmt->bindParam(':user_ip',$user_ip, \PDO::PARAM_STR, 20);
                    $stmt->bindValue(':user_education_history',isset($argv['user_education_history']) ? $argv['user_education_history'] : '0', \PDO::PARAM_STR);
                    $stmt->bindValue(':user_location',isset($argv['user_location']) ? $argv['user_location'] : '0', \PDO::PARAM_STR);
                    
                $user_creation_date = isset($argv['user_creation_date']) ? $argv['user_creation_date'] : null;
                $stmt->bindParam(':user_creation_date',$user_creation_date, \PDO::PARAM_STR, 14);
        
        return $stmt->execute() ? $id : false;

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
            $user_id = 'UNHEX('.$argv['user_id'].')';
            $stmt->bindParam(':user_id', $user_id, \PDO::PARAM_STR, 16);
        }
        if (isset($argv['user_type'])) {
            $user_type = $argv['user_type'];
            $stmt->bindParam(':user_type',$user_type, \PDO::PARAM_STR, 20 );
        }
        if (isset($argv['user_sport'])) {
            $user_sport = $argv['user_sport'];
            $stmt->bindParam(':user_sport',$user_sport, \PDO::PARAM_STR, 20 );
        }
        if (isset($argv['user_session_id'])) {
            $user_session_id = $argv['user_session_id'];
            $stmt->bindParam(':user_session_id',$user_session_id, \PDO::PARAM_STR, 225 );
        }
        if (isset($argv['user_facebook_id'])) {
            $user_facebook_id = $argv['user_facebook_id'];
            $stmt->bindParam(':user_facebook_id',$user_facebook_id, \PDO::PARAM_STR, 225 );
        }
        if (isset($argv['user_username'])) {
            $user_username = $argv['user_username'];
            $stmt->bindParam(':user_username',$user_username, \PDO::PARAM_STR, 25 );
        }
        if (isset($argv['user_first_name'])) {
            $user_first_name = $argv['user_first_name'];
            $stmt->bindParam(':user_first_name',$user_first_name, \PDO::PARAM_STR, 25 );
        }
        if (isset($argv['user_last_name'])) {
            $user_last_name = $argv['user_last_name'];
            $stmt->bindParam(':user_last_name',$user_last_name, \PDO::PARAM_STR, 25 );
        }
        if (isset($argv['user_profile_pic'])) {
            $user_profile_pic = $argv['user_profile_pic'];
            $stmt->bindParam(':user_profile_pic',$user_profile_pic, \PDO::PARAM_STR, 225 );
        }
        if (isset($argv['user_profile_uri'])) {
            $user_profile_uri = $argv['user_profile_uri'];
            $stmt->bindParam(':user_profile_uri',$user_profile_uri, \PDO::PARAM_STR, 225 );
        }
        if (isset($argv['user_cover_photo'])) {
            $user_cover_photo = $argv['user_cover_photo'];
            $stmt->bindParam(':user_cover_photo',$user_cover_photo, \PDO::PARAM_STR, 225 );
        }
        if (isset($argv['user_birthday'])) {
            $stmt->bindValue(':user_birthday',$argv['user_birthday'], \PDO::PARAM_STR );
        }
        if (isset($argv['user_gender'])) {
            $user_gender = $argv['user_gender'];
            $stmt->bindParam(':user_gender',$user_gender, \PDO::PARAM_STR, 25 );
        }
        if (isset($argv['user_about_me'])) {
            $stmt->bindValue(':user_about_me',$argv['user_about_me'], \PDO::PARAM_STR );
        }
        if (isset($argv['user_rank'])) {
            $user_rank = $argv['user_rank'];
            $stmt->bindParam(':user_rank',$user_rank, \PDO::PARAM_STR, 8 );
        }
        if (isset($argv['user_password'])) {
            $user_password = $argv['user_password'];
            $stmt->bindParam(':user_password',$user_password, \PDO::PARAM_STR, 225 );
        }
        if (isset($argv['user_email'])) {
            $user_email = $argv['user_email'];
            $stmt->bindParam(':user_email',$user_email, \PDO::PARAM_STR, 50 );
        }
        if (isset($argv['user_email_code'])) {
            $user_email_code = $argv['user_email_code'];
            $stmt->bindParam(':user_email_code',$user_email_code, \PDO::PARAM_STR, 225 );
        }
        if (isset($argv['user_email_confirmed'])) {
            $user_email_confirmed = $argv['user_email_confirmed'];
            $stmt->bindParam(':user_email_confirmed',$user_email_confirmed, \PDO::PARAM_STR, 20 );
        }
        if (isset($argv['user_generated_string'])) {
            $user_generated_string = $argv['user_generated_string'];
            $stmt->bindParam(':user_generated_string',$user_generated_string, \PDO::PARAM_STR, 200 );
        }
        if (isset($argv['user_membership'])) {
            $user_membership = $argv['user_membership'];
            $stmt->bindParam(':user_membership',$user_membership, \PDO::PARAM_STR, 10 );
        }
        if (isset($argv['user_deactivated'])) {
            $user_deactivated = $argv['user_deactivated'];
            $stmt->bindParam(':user_deactivated',$user_deactivated, \PDO::PARAM_NULL, 1 );
        }
        if (isset($argv['user_ip'])) {
            $user_ip = $argv['user_ip'];
            $stmt->bindParam(':user_ip',$user_ip, \PDO::PARAM_STR, 20 );
        }
        if (isset($argv['user_education_history'])) {
            $stmt->bindValue(':user_education_history',$argv['user_education_history'], \PDO::PARAM_STR );
        }
        if (isset($argv['user_location'])) {
            $stmt->bindValue(':user_location',$argv['user_location'], \PDO::PARAM_STR );
        }
        if (isset($argv['user_creation_date'])) {
            $user_creation_date = $argv['user_creation_date'];
            $stmt->bindParam(':user_creation_date',$user_creation_date, \PDO::PARAM_STR, 14 );
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
                if (in_array($column, self::BINARY)) {
                    $sql .= " $column =UNHEX(" . Database::database()->quote($value) . ') AND ';
                } else {
                    $sql .= " $column =" . Database::database()->quote($value) . ' AND ';
                }
            }
            $sql = substr($sql, 0, strlen($sql)-4);
        } else if (!empty(self::PRIMARY)) {
            $sql .= ' WHERE ' . self::PRIMARY . '=UNHEX(' . Database::database()->quote($primary) . ')';
        }

        $remove = null;

        return self::execute($sql);
    }
}