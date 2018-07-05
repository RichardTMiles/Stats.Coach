<?php

namespace Table;

use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Interfaces\iRest;

class carbon_photos extends Entities implements iRest
{
    const PRIMARY = "parent_id";

    const COLUMNS = [
    'parent_id','photo_id','user_id','photo_path','photo_description',
    ];

    const BINARY = [
    'parent_id','photo_id','user_id',
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

        $sql = 'SELECT ' .  $sql . ' FROM statscoach.carbon_photos';

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
        $sql = 'INSERT INTO statscoach.carbon_photos (parent_id, photo_id, user_id, photo_path, photo_description) VALUES ( :parent_id, :photo_id, :user_id, :photo_path, :photo_description)';
        $stmt = Database::database()->prepare($sql);
            $parent_id = $id = self::new_entity('carbon_photos');
            $stmt->bindParam(':parent_id',$parent_id, \PDO::PARAM_STR, 16);
            
                $photo_id = isset($argv['photo_id']) ? $argv['photo_id'] : null;
                $stmt->bindParam(':photo_id',$photo_id, \PDO::PARAM_STR, 16);
                    
                $user_id = isset($argv['user_id']) ? $argv['user_id'] : null;
                $stmt->bindParam(':user_id',$user_id, \PDO::PARAM_STR, 16);
                    
                $photo_path = isset($argv['photo_path']) ? $argv['photo_path'] : null;
                $stmt->bindParam(':photo_path',$photo_path, \PDO::PARAM_STR, 225);
                    $stmt->bindValue(':photo_description',isset($argv['photo_description']) ? $argv['photo_description'] : null, \PDO::PARAM_STR);
        
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

        $sql = 'UPDATE statscoach.carbon_photos ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

        if (isset($argv['parent_id'])) {
            $set .= 'parent_id=UNHEX(:parent_id),';
        }
        if (isset($argv['photo_id'])) {
            $set .= 'photo_id=UNHEX(:photo_id),';
        }
        if (isset($argv['user_id'])) {
            $set .= 'user_id=UNHEX(:user_id),';
        }
        if (isset($argv['photo_path'])) {
            $set .= 'photo_path=:photo_path,';
        }
        if (isset($argv['photo_description'])) {
            $set .= 'photo_description=:photo_description,';
        }

        if (empty($set)){
            return false;
        }

        $set = substr($set, 0, strlen($set)-1);

        $sql .= $set . ' WHERE ' . self::PRIMARY . "='$id'";

        $stmt = Database::database()->prepare($sql);

        if (isset($argv['parent_id'])) {
            $parent_id = 'UNHEX('.$argv['parent_id'].')';
            $stmt->bindParam(':parent_id', $parent_id, \PDO::PARAM_STR, 16);
        }
        if (isset($argv['photo_id'])) {
            $photo_id = 'UNHEX('.$argv['photo_id'].')';
            $stmt->bindParam(':photo_id', $photo_id, \PDO::PARAM_STR, 16);
        }
        if (isset($argv['user_id'])) {
            $user_id = 'UNHEX('.$argv['user_id'].')';
            $stmt->bindParam(':user_id', $user_id, \PDO::PARAM_STR, 16);
        }
        if (isset($argv['photo_path'])) {
            $photo_path = $argv['photo_path'];
            $stmt->bindParam(':photo_path',$photo_path, \PDO::PARAM_STR, 225 );
        }
        if (isset($argv['photo_description'])) {
            $stmt->bindValue(':photo_description',$argv['photo_description'], \PDO::PARAM_STR );
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
        $sql = 'DELETE FROM statscoach.carbon_photos ';

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