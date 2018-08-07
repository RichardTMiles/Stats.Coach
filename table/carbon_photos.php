<?php
namespace Table;


use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Interfaces\iRest;

class carbon_photos extends Entities implements iRest
{
    const PRIMARY = [
    'parent_id',
    ];

    const COLUMNS = [
    'parent_id','photo_id','user_id','photo_path','photo_description',
    ];

    const VALIDATION = [];

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
        $get = isset($argv['select']) ? $argv['select'] : self::COLUMNS;
        $where = isset($argv['where']) ? $argv['where'] : [];

        $group = $sql = '';

        if (isset($argv['pagination'])) {
            if (!empty($argv['pagination']) && !is_array($argv['pagination'])) {
                $argv['pagination'] = json_decode($argv['pagination'], true);
            }
            if (isset($argv['pagination']['limit']) && $argv['pagination']['limit'] != null) {
                $limit = ' LIMIT ' . $argv['pagination']['limit'];
            } else {
                $limit = '';
            }

            $order = '';
            if (!empty($limit)) {

                 $order = ' ORDER BY ';

                if (isset($argv['pagination']['order']) && $argv['pagination']['order'] != null) {
                    if (is_array($argv['pagination']['order'])) {
                        foreach ($argv['pagination']['order'] as $item => $sort) {
                            $order .= $item .' '. $sort;
                        }
                    } else {
                        $order .= $argv['pagination']['order'];
                    }
                } else {
                    $order .= self::PRIMARY[0] . ' DESC';
                }
            }
            $limit = $order .' '. $limit;
        } else {
            $limit = ' ORDER BY ' . self::PRIMARY[0] . ' DESC LIMIT 100';
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

        $sql = 'SELECT ' .  $sql . ' FROM statscoach.carbon_photos';

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
            $sql .= ' WHERE  parent_id=UNHEX(' . $primary .')';
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

        
        if (empty($primary) && $argv['pagination']['limit'] !== 1 && count($return) && in_array(array_keys($return)[0], self::COLUMNS, true)) {  // You must set tr
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
        $sql = 'INSERT INTO statscoach.carbon_photos (parent_id, photo_id, user_id, photo_path, photo_description) VALUES ( UNHEX(:parent_id), UNHEX(:photo_id), UNHEX(:user_id), :photo_path, :photo_description)';
        $stmt = Database::database()->prepare($sql);

        global $json;

        if (!isset($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;

            $parent_id = $id = isset($argv['parent_id']) ? $argv['parent_id'] : self::new_entity('carbon_photos');
            $stmt->bindParam(':parent_id',$parent_id, 2, 16);
            
                $photo_id = $argv['photo_id'];
                $stmt->bindParam(':photo_id',$photo_id, 2, 16);
                    
                $user_id = $argv['user_id'];
                $stmt->bindParam(':user_id',$user_id, 2, 16);
                    
                $photo_path = $argv['photo_path'];
                $stmt->bindParam(':photo_path',$photo_path, 2, 225);
                    $stmt->bindValue(':photo_description',$argv['photo_description'], 2);
        
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

        $sql .= substr($set, 0, strlen($set)-1);

        $db = Database::database();

        
        $primary = $db->quote($primary);
        $sql .= ' WHERE  parent_id=UNHEX(' . $primary .')';

        $stmt = $db->prepare($sql);

        global $json;

        if (!isset($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;


        if (isset($argv['parent_id'])) {
            $parent_id = 'UNHEX('.$argv['parent_id'].')';
            $stmt->bindParam(':parent_id', $parent_id, 2, 16);
        }
        if (isset($argv['photo_id'])) {
            $photo_id = 'UNHEX('.$argv['photo_id'].')';
            $stmt->bindParam(':photo_id', $photo_id, 2, 16);
        }
        if (isset($argv['user_id'])) {
            $user_id = 'UNHEX('.$argv['user_id'].')';
            $stmt->bindParam(':user_id', $user_id, 2, 16);
        }
        if (isset($argv['photo_path'])) {
            $photo_path = $argv['photo_path'];
            $stmt->bindParam(':photo_path',$photo_path, 2, 225);
        }
        if (isset($argv['photo_description'])) {
            $stmt->bindValue(':photo_description',$argv['photo_description'], 2);
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