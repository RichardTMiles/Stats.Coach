<?php

namespace Table;

use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Error\PublicAlert;
use CarbonPHP\Interfaces\iRest;

class user_tasks extends Entities implements iRest
{
    const COLUMNS = [
            'task_id',
            'user_id',
            'from_id',
            'task_name',
            'task_description',
            'percent_complete',
            'start_date',
            'end_date',
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

        $sql = 'SELECT ' .  $get . ' FROM statscoach.user_tasks';

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
        $sql = 'INSERT INTO statscoach.user_tasks (task_id, user_id, from_id, task_name, task_description, percent_complete, start_date, end_date) VALUES (:task_id, :user_id, :from_id, :task_name, :task_description, :percent_complete, :start_date, :end_date)';
        $stmt = Database::database()->prepare($sql);
            $stmt->bindValue(':task_id', isset($argv['task_id']) ? $argv['task_id'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':user_id', isset($argv['user_id']) ? $argv['user_id'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':from_id', isset($argv['from_id']) ? $argv['from_id'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':task_name', isset($argv['task_name']) ? $argv['task_name'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':task_description', isset($argv['task_description']) ? $argv['task_description'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':percent_complete', isset($argv['percent_complete']) ? $argv['percent_complete'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':start_date', isset($argv['start_date']) ? $argv['start_date'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':end_date', isset($argv['end_date']) ? $argv['end_date'] : null, \PDO::PARAM_STR);
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

        $sql = 'UPDATE statscoach.user_tasks ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';
        if (isset($argv['task_id'])) {
            $set .= 'task_id=:task_id,';
        }
        if (isset($argv['user_id'])) {
            $set .= 'user_id=:user_id,';
        }
        if (isset($argv['from_id'])) {
            $set .= 'from_id=:from_id,';
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
            $stmt->bindValue(':task_id', $argv['task_id'], \PDO::PARAM_STR);
        }
        if (isset($argv['user_id'])) {
            $stmt->bindValue(':user_id', $argv['user_id'], \PDO::PARAM_STR);
        }
        if (isset($argv['from_id'])) {
            $stmt->bindValue(':from_id', $argv['from_id'], \PDO::PARAM_STR);
        }
        if (isset($argv['task_name'])) {
            $stmt->bindValue(':task_name', $argv['task_name'], \PDO::PARAM_STR);
        }
        if (isset($argv['task_description'])) {
            $stmt->bindValue(':task_description', $argv['task_description'], \PDO::PARAM_STR);
        }
        if (isset($argv['percent_complete'])) {
            $stmt->bindValue(':percent_complete', $argv['percent_complete'], \PDO::PARAM_STR);
        }
        if (isset($argv['start_date'])) {
            $stmt->bindValue(':start_date', $argv['start_date'], \PDO::PARAM_STR);
        }
        if (isset($argv['end_date'])) {
            $stmt->bindValue(':end_date', $argv['end_date'], \PDO::PARAM_STR);
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
        $sql = 'DELETE FROM statscoach.user_tasks ';

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