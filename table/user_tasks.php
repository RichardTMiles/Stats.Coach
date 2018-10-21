<?php
namespace Table;


use CarbonPHP\Entities;
use CarbonPHP\Interfaces\iRest;
use Psr\Log\InvalidArgumentException;


class user_tasks extends Entities implements iRest
{
    public const PRIMARY = [
    'user_id',
    ];

    public const COLUMNS = [
        'task_id' => [ 'binary', '2', '16' ],'user_id' => [ 'binary', '2', '16' ],'from_id' => [ 'binary', '2', '16' ],'task_name' => [ 'varchar', '2', '40' ],'task_description' => [ 'varchar', '2', '225' ],'percent_complete' => [ 'int', '2', '11' ],'start_date' => [ 'datetime', '2', '' ],'end_date' => [ 'datetime', '2', '' ],
    ];

    public const VALIDATION = [];


    public static $injection = [];


    public static function jsonSQLReporting($argv, $sql) : void {
        global $json;
        if (!\is_array($json)) {
            $json = [];
        } elseif (!isset($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = [
            $argv,
            $sql
        ];
    }

    public static function buildWhere(array $set, \PDO $pdo, $join = 'AND') : string
    {
        $sql = '(';
        foreach ($set as $column => $value) {
            if (\is_array($value)) {
                $sql .= self::buildWhere($value, $pdo, $join === 'AND' ? 'OR' : 'AND');
            } else if (isset(self::COLUMNS[$column])) {
                if (self::COLUMNS[$column][0] === 'binary') {
                    $sql .= "($column = UNHEX(:" . $column . ")) $join ";
                } else {
                    $sql .= "($column = :" . $column . ") $join ";
                }
            } else {
                $sql .= "($column = " . self::addInjection($value, $pdo) . ") $join ";
            }

        }
        return rtrim($sql, " $join") . ')';
    }

    public static function addInjection($value, \PDO $pdo, $quote = false) : string
    {
        $inject = ':injection' . \count(self::$injection) . 'buildWhere';
        self::$injection[$inject] = $quote ? $pdo->quote($value) : $value;
        return $inject;
    }

    public static function bind(\PDOStatement $stmt, array $argv) {
        if (array_key_exists('task_id', $argv)) {
            $task_id = $argv['task_id'];
            $stmt->bindParam(':task_id',$task_id, 2, 16);
        }
        if (array_key_exists('user_id', $argv)) {
            $user_id = $argv['user_id'];
            $stmt->bindParam(':user_id',$user_id, 2, 16);
        }
        if (array_key_exists('from_id', $argv)) {
            $from_id = $argv['from_id'];
            $stmt->bindParam(':from_id',$from_id, 2, 16);
        }
        if (array_key_exists('task_name', $argv)) {
            $task_name = $argv['task_name'];
            $stmt->bindParam(':task_name',$task_name, 2, 40);
        }
        if (array_key_exists('task_description', $argv)) {
            $task_description = $argv['task_description'];
            $stmt->bindParam(':task_description',$task_description, 2, 225);
        }
        if (array_key_exists('percent_complete', $argv)) {
            $percent_complete = $argv['percent_complete'];
            $stmt->bindParam(':percent_complete',$percent_complete, 2, 11);
        }
        if (array_key_exists('start_date', $argv)) {
            $stmt->bindValue(':start_date',$argv['start_date'], 2);
        }
        if (array_key_exists('end_date', $argv)) {
            $stmt->bindValue(':end_date',$argv['end_date'], 2);
        }

        foreach (self::$injection as $key => $value) {
            $stmt->bindValue($key,$value);
        }

        return $stmt->execute();
    }


    /**
    *
    *   $argv = [
    *       'select' => [
    *                          '*column name array*', 'etc..'
    *        ],
    *
    *       'where' => [
    *              'Column Name' => 'Value To Constrain',
    *              'Defaults to AND' => 'Nesting array switches to OR',
    *              [
    *                  'Column Name' => 'Value To Constrain',
    *                  'This array is OR'ed togeather' => 'Another sud array would `AND`'
    *                  [ etc... ]
    *              ]
    *        ],
    *
    *        'pagination' => [
    *              'limit' => (int) 90, // The maximum number of rows to return,
    *                       setting the limit explicitly to 1 will return a key pair array of only the
    *                       singular result. SETTING THE LIMIT TO NULL WILL ALLOW INFINITE RESULTS (NO LIMIT).
    *                       The limit defaults to 100 by design.
    *
    *              'order' => '*column name* [ASC|DESC]',  // i.e.  'username ASC' or 'username, email DESC'
    *
    *
    *         ],
    *
    *   ];
    *
    *
    * @param array $return
    * @param string|null $primary
    * @param array $argv
    * @return bool
    * @throws \Exception
    */
    public static function Get(array &$return, string $primary = null, array $argv) : bool
    {
        self::$injection = [];
        $aggregate = false;
        $group = $sql = '';
        $pdo = self::database();

        $get = $argv['select'] ?? array_keys(self::COLUMNS);
        $where = $argv['where'] ?? [];

        if (isset($argv['pagination'])) {
            if (!empty($argv['pagination']) && !\is_array($argv['pagination'])) {
                $argv['pagination'] = json_decode($argv['pagination'], true);
            }
            if (isset($argv['pagination']['limit']) && $argv['pagination']['limit'] !== null) {
                $limit = ' LIMIT ' . $argv['pagination']['limit'];
            } else {
                $limit = '';
            }

            $order = '';
            if (!empty($limit)) {

                $order = ' ORDER BY ';

                if (isset($argv['pagination']['order']) && $argv['pagination']['order'] !== null) {
                    if (\is_array($argv['pagination']['order'])) {
                        foreach ($argv['pagination']['order'] as $item => $sort) {
                            $order .= "$item $sort";
                        }
                    } else {
                        $order .= $argv['pagination']['order'];
                    }
                } else {
                    $order .= 'user_id ASC';
                }
            }
            $limit = "$order $limit";
        } else {
            $limit = ' ORDER BY user_id ASC LIMIT 100';
        }

        foreach($get as $key => $column){
            if (!empty($sql)) {
                $sql .= ', ';
                if (!empty($group)) {
                    $group .= ', ';
                }
            }
            $columnExists = isset(self::COLUMNS[$column]);
            if ($columnExists && self::COLUMNS[$column][0] === 'binary') {
                $sql .= "HEX($column) as $column";
                $group .= $column;
            } elseif ($columnExists) {
                $sql .= $column;
                $group .= $column;
            } else {
                if (!preg_match('#(((((hex|argv|count|sum|min|max) *\(+ *)+)|(distinct|\*|\+|\-|\/| |task_id|user_id|from_id|task_name|task_description|percent_complete|start_date|end_date))+\)*)+ *(as [a-z]+)?#i', $column)) {
                    /** @noinspection PhpUndefinedClassInspection */
                    throw new InvalidArgumentException('Arguments passed in SELECT failed the REGEX test!');
                }
                $sql .= $column;
                $aggregate = true;
            }
        }

        $sql = 'SELECT ' .  $sql . ' FROM StatsCoach.user_tasks';

        if (null === $primary) {
            /** @noinspection NestedPositiveIfStatementsInspection */
            if (!empty($where)) {
                $sql .= ' WHERE ' . self::buildWhere($where, $pdo);
            }
        } else {
        $sql .= ' WHERE  user_id=UNHEX('.self::addInjection($primary, $pdo).')';
        }

        if ($aggregate  && !empty($group)) {
            $sql .= ' GROUP BY ' . $group . ' ';
        }

        $sql .= $limit;

        self::jsonSQLReporting(\func_get_args(), $sql);

        $stmt = $pdo->prepare($sql);

        if (!self::bind($stmt, $argv['where'] ?? [])) {
            return false;
        }

        $return = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        /**
        *   The next part is so every response from the rest api
        *   formats to a set of rows. Even if only one row is returned.
        *   You must set the third parameter to true, otherwise '0' is
        *   apparently in the self::COLUMNS
        */

        
            if (!empty($primary) || (isset($argv['pagination']['limit']) && $argv['pagination']['limit'] === 1)) {
            $return = (\count($return) === 1 ?
            (\is_array($return['0']) ? $return['0'] : $return) : $return);   // promise this is needed and will still return the desired array except for a single record will not be an array
            }

        return true;
    }

    /**
    * @param array $argv
    * @return bool|mixed
    */
    public static function Post(array $argv)
    {
        self::$injection = [];
    /** @noinspection SqlResolve */
    $sql = 'INSERT INTO StatsCoach.user_tasks (task_id, user_id, from_id, task_name, task_description, percent_complete, start_date, end_date) VALUES ( UNHEX(:task_id), UNHEX(:user_id), UNHEX(:from_id), :task_name, :task_description, :percent_complete, :start_date, :end_date)';

    self::jsonSQLReporting(\func_get_args(), $sql);

    $stmt = self::database()->prepare($sql);

                
                    $task_id = $argv['task_id'];
                    $stmt->bindParam(':task_id',$task_id, 2, 16);
                        $user_id = $id = $argv['user_id'] ?? self::beginTransaction('user_tasks');
                $stmt->bindParam(':user_id',$user_id, 2, 16);
                
                    $from_id =  $argv['from_id'] ?? null;
                    $stmt->bindParam(':from_id',$from_id, 2, 16);
                        
                    $task_name = $argv['task_name'];
                    $stmt->bindParam(':task_name',$task_name, 2, 40);
                        
                    $task_description =  $argv['task_description'] ?? null;
                    $stmt->bindParam(':task_description',$task_description, 2, 225);
                        
                    $percent_complete =  $argv['percent_complete'] ?? '0';
                    $stmt->bindParam(':percent_complete',$percent_complete, 2, 11);
                        $stmt->bindValue(':start_date',isset($argv['start_date']) ? $argv['start_date'] : null, 2);
                        $stmt->bindValue(':end_date',isset($argv['end_date']) ? $argv['end_date'] : null, 2);
        


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
        self::$injection = [];
        if (empty($primary)) {
            return false;
        }

        foreach ($argv as $key => $value) {
            if (!\in_array($key, self::COLUMNS, true)){
                unset($argv[$key]);
            }
        }

        $sql = 'UPDATE StatsCoach.user_tasks ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

            if (!empty($argv['task_id'])) {
                $set .= 'task_id=UNHEX(:task_id),';
            }
            if (!empty($argv['user_id'])) {
                $set .= 'user_id=UNHEX(:user_id),';
            }
            if (!empty($argv['from_id'])) {
                $set .= 'from_id=UNHEX(:from_id),';
            }
            if (!empty($argv['task_name'])) {
                $set .= 'task_name=:task_name,';
            }
            if (!empty($argv['task_description'])) {
                $set .= 'task_description=:task_description,';
            }
            if (!empty($argv['percent_complete'])) {
                $set .= 'percent_complete=:percent_complete,';
            }
            if (!empty($argv['start_date'])) {
                $set .= 'start_date=:start_date,';
            }
            if (!empty($argv['end_date'])) {
                $set .= 'end_date=:end_date,';
            }

        if (empty($set)){
            return false;
        }

        $sql .= substr($set, 0, -1);

        $pdo = self::database();

        $sql .= ' WHERE  user_id=UNHEX('.self::addInjection($primary, $pdo).')';

        self::jsonSQLReporting(\func_get_args(), $sql);

        $stmt = $pdo->prepare($sql);

        if (!self::bind($stmt, $argv)){
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