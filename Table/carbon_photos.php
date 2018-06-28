<?php

namespace Table;

use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Error\PublicAlert;
use CarbonPHP\Interfaces\iRest;

class carbon_photos extends Entities implements iRest
{
    const COLUMNS = [
            'parent_id',
            'photo_id',
            'user_id',
            'photo_path',
            'photo_description',
    ];

    const PRIMARY = "parent_id";

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

        $sql = 'SELECT ' .  $get . ' FROM statscoach.carbon_photos';

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
        $sql = 'INSERT INTO statscoach.carbon_photos (parent_id, photo_id, user_id, photo_path, photo_description) VALUES (:parent_id, :photo_id, :user_id, :photo_path, :photo_description)';
        $stmt = Database::database()->prepare($sql);
            $stmt->bindValue(':parent_id', isset($argv['parent_id']) ? $argv['parent_id'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':photo_id', isset($argv['photo_id']) ? $argv['photo_id'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':user_id', isset($argv['user_id']) ? $argv['user_id'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':photo_path', isset($argv['photo_path']) ? $argv['photo_path'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':photo_description', isset($argv['photo_description']) ? $argv['photo_description'] : null, \PDO::PARAM_STR);
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

        $sql = 'UPDATE statscoach.carbon_photos ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';
        if (isset($argv['parent_id'])) {
            $set .= 'parent_id=:parent_id,';
        }
        if (isset($argv['photo_id'])) {
            $set .= 'photo_id=:photo_id,';
        }
        if (isset($argv['user_id'])) {
            $set .= 'user_id=:user_id,';
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
            $stmt->bindValue(':parent_id', $argv['parent_id'], \PDO::PARAM_STR);
        }
        if (isset($argv['photo_id'])) {
            $stmt->bindValue(':photo_id', $argv['photo_id'], \PDO::PARAM_STR);
        }
        if (isset($argv['user_id'])) {
            $stmt->bindValue(':user_id', $argv['user_id'], \PDO::PARAM_STR);
        }
        if (isset($argv['photo_path'])) {
            $stmt->bindValue(':photo_path', $argv['photo_path'], \PDO::PARAM_STR);
        }
        if (isset($argv['photo_description'])) {
            $stmt->bindValue(':photo_description', $argv['photo_description'], \PDO::PARAM_STR);
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