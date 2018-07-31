<?php
namespace Table;


use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Interfaces\iRest;

class user_tasks extends Entities implements iRest
{
    const PRIMARY = [
    'user_id',
    ];

    const COLUMNS = [
    'task_id','user_id','from_id','task_name','task_description','percent_complete','start_date','end_date',
    ];

    const VALIDATION = [];

    const BINARY = [
    'task_id','user_id','from_id',
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

        $sql = 'SELECT ' .  $sql . ' FROM statscoach.user_tasks';

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
        $sql = 'INSERT INTO statscoach.user_tasks (task_id, user_id, from_id, task_name, task_description, percent_complete, start_date, end_date) VALUES ( UNHEX(:task_id), UNHEX(:user_id), UNHEX(:from_id), :task_name, :task_description, :percent_complete, :start_date, :end_date)';
        $stmt = sDatabaseelf::database()->prepare($sql);

        global $json;

        if (!isset($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;

            
                $task_id = $argv['task_id'];
                $stmt->bindParam(':task_id',$task_id, 2, 16);
                    $user_id = $id = isset($argv['user_id']) ? $argv['user_id'] : self::new_entity('user_tasks');
            $stmt->bindParam(':user_id',$user_id, 2, 16);
            
                $from_id = isset($argv['from_id']) ? $argv['from_id'] : null;
                $stmt->bindParam(':from_id',$from_id, 2, 16);
                    
                $task_name = $argv['task_name'];
                $stmt->bindParam(':task_name',$task_name, 2, 40);
                    
                $task_description = isset($argv['task_description']) ? $argv['task_description'] : null;
                $stmt->bindParam(':task_description',$task_description, 2, 225);
                    
                $percent_complete = isset($argv['percent_complete']) ? $argv['percent_complete'] : '0';
                $stmt->bindParam(':percent_complete',$percent_complete, 2, 11);
                    $stmt->bindValue(':start_date',isset($argv['start_date']) ? $argv['start_date'] : null, \2);
                    $stmt->bindValue(':end_date',isset($argv['end_date']) ? $argv['end_date'] : null, \2);
        
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

        $sql = 'UPDATE statscoach.user_tasks ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

        if (isset($argv['task_id'])) {
            $set .= 'task_id=UNHEX(:task_id),';
        }
        if (isset($argv['user_id'])) {
            $set .= 'user_id=UNHEX(:user_id),';
        }
        if (isset($argv['from_id'])) {
            $set .= 'from_id=UNHEX(:from_id),';
        }
        if (isset($argv['task_name'])) {
            $set .= 'task_name=:task_name,';
        }
        if (isset($argv['task_description'])) {
            $set .= 'task_description=:task_description,';
        }
        if (isset($argv['percent_complete'])) {
            $set .= 'percent_complete=:percent_complete,';
        }
        if (isset($argv['start_date'])) {
            $set .= 'start_date=:start_date,';
        }
        if (isset($argv['end_date'])) {
            $set .= 'end_date=:end_date,';
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


        if (isset($argv['task_id'])) {
            $task_id = 'UNHEX('.$argv['task_id'].')';
            $stmt->bindParam(':task_id', $task_id, 2, 16);
        }
        if (isset($argv['user_id'])) {
            $user_id = 'UNHEX('.$argv['user_id'].')';
            $stmt->bindParam(':user_id', $user_id, 2, 16);
        }
        if (isset($argv['from_id'])) {
            $from_id = 'UNHEX('.$argv['from_id'].')';
            $stmt->bindParam(':from_id', $from_id, 2, 16);
        }
        if (isset($argv['task_name'])) {
            $task_name = $argv['task_name'];
            $stmt->bindParam(':task_name',$task_name, 2, 40);
        }
        if (isset($argv['task_description'])) {
            $task_description = $argv['task_description'];
            $stmt->bindParam(':task_description',$task_description, 2, 225);
        }
        if (isset($argv['percent_complete'])) {
            $percent_complete = $argv['percent_complete'];
            $stmt->bindParam(':percent_complete',$percent_complete, 2, 11);
        }
        if (isset($argv['start_date'])) {
            $stmt->bindValue(':start_date',$argv['start_date'], 2);
        }
        if (isset($argv['end_date'])) {
            $stmt->bindValue(':end_date',$argv['end_date'], 2);
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