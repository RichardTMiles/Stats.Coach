<?php
namespace Table;


use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Interfaces\iRest;

class user_tasks extends Entities implements iRest
{
    const PRIMARY = "user_id";

    const COLUMNS = [
    'task_id','user_id','from_id','task_name','task_description','percent_complete','start_date','end_date',
    ];

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

        $sql = 'SELECT ' .  $sql . ' FROM statscoach.user_tasks';

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
        $sql = 'INSERT INTO statscoach.user_tasks (task_id, user_id, from_id, task_name, task_description, percent_complete, start_date, end_date) VALUES ( :task_id, UNHEX(:user_id), :from_id, :task_name, :task_description, :percent_complete, :start_date, :end_date)';
        $stmt = Database::database()->prepare($sql);
            
                $task_id = isset($argv['task_id']) ? $argv['task_id'] : null;
                $stmt->bindParam(':task_id',$task_id, \PDO::PARAM_STR, 16);
                    $user_id = $id = isset($argv['user_id']) ? $argv['user_id'] : self::new_entity('user_tasks');
            $stmt->bindParam(':user_id',$user_id, \PDO::PARAM_STR, 16);
            
                $from_id = isset($argv['from_id']) ? $argv['from_id'] : null;
                $stmt->bindParam(':from_id',$from_id, \PDO::PARAM_STR, 16);
                    
                $task_name = isset($argv['task_name']) ? $argv['task_name'] : '0';
                $stmt->bindParam(':task_name',$task_name, \PDO::PARAM_STR, 40);
                    
                $task_description = isset($argv['task_description']) ? $argv['task_description'] : null;
                $stmt->bindParam(':task_description',$task_description, \PDO::PARAM_STR, 225);
                    
                $percent_complete = isset($argv['percent_complete']) ? $argv['percent_complete'] : '0';
                $stmt->bindParam(':percent_complete',$percent_complete, \PDO::PARAM_STR, 11);
                    $stmt->bindValue(':start_date',isset($argv['start_date']) ? $argv['start_date'] : null, \PDO::PARAM_STR);
                    $stmt->bindValue(':end_date',isset($argv['end_date']) ? $argv['end_date'] : null, \PDO::PARAM_STR);
        
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

        $set = substr($set, 0, strlen($set)-1);

        $sql .= $set . ' WHERE ' . self::PRIMARY . "='$id'";

        $stmt = Database::database()->prepare($sql);

        if (isset($argv['task_id'])) {
            $task_id = 'UNHEX('.$argv['task_id'].')';
            $stmt->bindParam(':task_id', $task_id, \PDO::PARAM_STR, 16);
        }
        if (isset($argv['user_id'])) {
            $user_id = 'UNHEX('.$argv['user_id'].')';
            $stmt->bindParam(':user_id', $user_id, \PDO::PARAM_STR, 16);
        }
        if (isset($argv['from_id'])) {
            $from_id = 'UNHEX('.$argv['from_id'].')';
            $stmt->bindParam(':from_id', $from_id, \PDO::PARAM_STR, 16);
        }
        if (isset($argv['task_name'])) {
            $task_name = $argv['task_name'];
            $stmt->bindParam(':task_name',$task_name, \PDO::PARAM_STR, 40 );
        }
        if (isset($argv['task_description'])) {
            $task_description = $argv['task_description'];
            $stmt->bindParam(':task_description',$task_description, \PDO::PARAM_STR, 225 );
        }
        if (isset($argv['percent_complete'])) {
            $percent_complete = $argv['percent_complete'];
            $stmt->bindParam(':percent_complete',$percent_complete, \PDO::PARAM_STR, 11 );
        }
        if (isset($argv['start_date'])) {
            $stmt->bindValue(':start_date',$argv['start_date'], \PDO::PARAM_STR );
        }
        if (isset($argv['end_date'])) {
            $stmt->bindValue(':end_date',$argv['end_date'], \PDO::PARAM_STR );
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