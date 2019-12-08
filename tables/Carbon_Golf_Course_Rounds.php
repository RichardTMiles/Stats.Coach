<?php
namespace Tables;


use CarbonPHP\Database;
use CarbonPHP\Interfaces\iRest;


class Carbon_Golf_Course_Rounds extends Database implements iRest
{

    public const USER_ID = 'user_id';
    public const ROUND_ID = 'round_id';
    public const COURSE_ID = 'course_id';
    public const ROUND_JSON = 'round_json';
    public const ROUND_PUBLIC = 'round_public';
    public const ROUND_OUT = 'round_out';
    public const ROUND_IN = 'round_in';
    public const ROUND_TOTAL = 'round_total';
    public const ROUND_TOTAL_GNR = 'round_total_gnr';
    public const ROUND_TOTAL_FFS = 'round_total_ffs';
    public const ROUND_TOTAL_PUTTS = 'round_total_putts';
    public const ROUND_DATE = 'round_date';
    public const ROUND_INPUT_COMPLETE = 'round_input_complete';
    public const ROUND_TEE_BOX_COLOR = 'round_tee_box_color';

    public const PRIMARY = [
    'round_id',
    ];

    public const COLUMNS = [
        'user_id' => [ 'binary', '2', '16' ],'round_id' => [ 'binary', '2', '16' ],'course_id' => [ 'binary', '2', '16' ],'round_json' => [ 'json', '2', '' ],'round_public' => [ 'int', '2', '1' ],'round_out' => [ 'int', '2', '2' ],'round_in' => [ 'int', '2', '3' ],'round_total' => [ 'int', '2', '3' ],'round_total_gnr' => [ 'int', '2', '11' ],'round_total_ffs' => [ 'int', '2', '3' ],'round_total_putts' => [ 'int', '2', '11' ],'round_date' => [ 'datetime', '2', '' ],'round_input_complete' => [ 'tinyint', '0', '1' ],'round_tee_box_color' => [ 'varchar', '2', '10' ],
    ];

    public const VALIDATION = [];


    public static $injection = [];


    public static function jsonSQLReporting($argv, $sql) : void {
        global $json;
        if (!\is_array($json)) {
            $json = [];
        }
        if (!isset($json['sql'])) {
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
        $bump = false;
        foreach ($set as $column => $value) {
            if (\is_array($value)) {
                if ($bump) {
                    $sql .= " $join ";
                }
                $bump = true;
                $sql .= self::buildWhere($value, $pdo, $join === 'AND' ? 'OR' : 'AND');
            } else if (array_key_exists($column, self::COLUMNS)) {
                $bump = false;
                if (self::COLUMNS[$column][0] === 'binary') {
                    $sql .= "($column = UNHEX(" . self::addInjection($value, $pdo)  . ")) $join ";
                } else {
                    $sql .= "($column = " . self::addInjection($value, $pdo) . ") $join ";
                }
            } else {
                $bump = false;
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
   
   /*
    $bind = function (array $argv) use (&$bind, &$stmt) {
            foreach ($argv as $key => $value) {
                
                if (is_numeric($key) && is_array($value)) {
                    $bind($value);
                    continue;
                }
                
                   if (array_key_exists('user_id', $argv)) {
            $user_id = $argv['user_id'];
            $stmt->bindParam(':user_id',$user_id, 2, 16);
        }
                   if (array_key_exists('round_id', $argv)) {
            $round_id = $argv['round_id'];
            $stmt->bindParam(':round_id',$round_id, 2, 16);
        }
                   if (array_key_exists('course_id', $argv)) {
            $course_id = $argv['course_id'];
            $stmt->bindParam(':course_id',$course_id, 2, 16);
        }
                   if (array_key_exists('round_json', $argv)) {
            $stmt->bindValue(':round_json',json_encode($argv['round_json']), 2);
        }
                   if (array_key_exists('round_public', $argv)) {
            $round_public = $argv['round_public'];
            $stmt->bindParam(':round_public',$round_public, 2, 1);
        }
                   if (array_key_exists('round_out', $argv)) {
            $round_out = $argv['round_out'];
            $stmt->bindParam(':round_out',$round_out, 2, 2);
        }
                   if (array_key_exists('round_in', $argv)) {
            $round_in = $argv['round_in'];
            $stmt->bindParam(':round_in',$round_in, 2, 3);
        }
                   if (array_key_exists('round_total', $argv)) {
            $round_total = $argv['round_total'];
            $stmt->bindParam(':round_total',$round_total, 2, 3);
        }
                   if (array_key_exists('round_total_gnr', $argv)) {
            $round_total_gnr = $argv['round_total_gnr'];
            $stmt->bindParam(':round_total_gnr',$round_total_gnr, 2, 11);
        }
                   if (array_key_exists('round_total_ffs', $argv)) {
            $round_total_ffs = $argv['round_total_ffs'];
            $stmt->bindParam(':round_total_ffs',$round_total_ffs, 2, 3);
        }
                   if (array_key_exists('round_total_putts', $argv)) {
            $round_total_putts = $argv['round_total_putts'];
            $stmt->bindParam(':round_total_putts',$round_total_putts, 2, 11);
        }
                   if (array_key_exists('round_date', $argv)) {
            $stmt->bindValue(':round_date',$argv['round_date'], 2);
        }
                   if (array_key_exists('round_input_complete', $argv)) {
            $round_input_complete = $argv['round_input_complete'];
            $stmt->bindParam(':round_input_complete',$round_input_complete, 0, 1);
        }
                   if (array_key_exists('round_tee_box_color', $argv)) {
            $round_tee_box_color = $argv['round_tee_box_color'];
            $stmt->bindParam(':round_tee_box_color',$round_tee_box_color, 2, 10);
        }
           
          }
        };
        
        $bind($argv); */

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
    */
    public static function Get(array &$return, string $primary = null, array $argv) : bool
    {
        self::$injection = [];
        $aggregate = false;
        $group = $sql = '';
        $pdo = self::database();

        $get = $argv['select'] ?? array_keys(self::COLUMNS);
        $where = $argv['where'] ?? [];

        if (array_key_exists('pagination',$argv)) {
            if (!empty($argv['pagination']) && !\is_array($argv['pagination'])) {
                $argv['pagination'] = json_decode($argv['pagination'], true);
            }
            if (array_key_exists('limit',$argv['pagination']) && $argv['pagination']['limit'] !== null) {
                $limit = ' LIMIT ' . $argv['pagination']['limit'];
            } else {
                $limit = '';
            }

            $order = '';
            if (!empty($limit)) {

                $order = ' ORDER BY ';

                if (array_key_exists('order',$argv['pagination']) && $argv['pagination']['order'] !== null) {
                    if (\is_array($argv['pagination']['order'])) {
                        foreach ($argv['pagination']['order'] as $item => $sort) {
                            $order .= "$item $sort";
                        }
                    } else {
                        $order .= $argv['pagination']['order'];
                    }
                } else {
                    $order .= 'round_id ASC';
                }
            }
            $limit = "$order $limit";
        } else {
            $limit = ' ORDER BY round_id ASC LIMIT 100';
        }

        foreach($get as $key => $column){
            if (!empty($sql)) {
                $sql .= ', ';
                if (!empty($group)) {
                    $group .= ', ';
                }
            }
            $columnExists = array_key_exists($column, self::COLUMNS);
            if ($columnExists && self::COLUMNS[$column][0] === 'binary') {
                $sql .= "HEX($column) as $column";
                $group .= $column;
            } elseif ($columnExists) {
                $sql .= $column;
                $group .= $column;
            } else {
                if (!preg_match('#(((((hex|argv|count|sum|min|max) *\(+ *)+)|(distinct|\*|\+|\-|\/| |user_id|round_id|course_id|round_json|round_public|round_out|round_in|round_total|round_total_gnr|round_total_ffs|round_total_putts|round_date|round_input_complete|round_tee_box_color))+\)*)+ *(as [a-z]+)?#i', $column)) {
                    return false;
                }
                $sql .= $column;
                $aggregate = true;
            }
        }

        $sql = 'SELECT ' .  $sql . ' FROM StatsCoach.carbon_golf_course_rounds';

        if (null === $primary) {
            /** @noinspection NestedPositiveIfStatementsInspection */
            if (!empty($where)) {
                $sql .= ' WHERE ' . self::buildWhere($where, $pdo);
            }
        } else {
        $sql .= ' WHERE  round_id=UNHEX('.self::addInjection($primary, $pdo).')';
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

        
        if ($primary !== null || (isset($argv['pagination']['limit']) && $argv['pagination']['limit'] === 1 && \count($return) === 1)) {
            $return = isset($return[0]) && \is_array($return[0]) ? $return[0] : $return;
            // promise this is needed and will still return the desired array except for a single record will not be an array
        if (array_key_exists('round_json', $return)) {
                $return['round_json'] = json_decode($return['round_json'], true);
            }
        
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
        $sql = 'INSERT INTO StatsCoach.carbon_golf_course_rounds (user_id, round_id, course_id, round_json, round_public, round_out, round_in, round_total, round_total_gnr, round_total_ffs, round_total_putts, round_input_complete, round_tee_box_color) VALUES ( UNHEX(:user_id), UNHEX(:round_id), UNHEX(:course_id), :round_json, :round_public, :round_out, :round_in, :round_total, :round_total_gnr, :round_total_ffs, :round_total_putts, :round_input_complete, :round_tee_box_color)';

        self::jsonSQLReporting(\func_get_args(), $sql);

        $stmt = self::database()->prepare($sql);

                
                    $user_id = $argv['user_id'];
                    $stmt->bindParam(':user_id',$user_id, 2, 16);
                        $round_id = $id = $argv['round_id'] ?? self::beginTransaction('carbon_golf_course_rounds');
                $stmt->bindParam(':round_id',$round_id, 2, 16);
                
                    $course_id = $argv['course_id'];
                    $stmt->bindParam(':course_id',$course_id, 2, 16);
                        $stmt->bindValue(':round_json',json_encode($argv['round_json']), 2);
                        
                    $round_public =  $argv['round_public'] ?? '1';
                    $stmt->bindParam(':round_public',$round_public, 2, 1);
                        
                    $round_out = $argv['round_out'];
                    $stmt->bindParam(':round_out',$round_out, 2, 2);
                        
                    $round_in = $argv['round_in'];
                    $stmt->bindParam(':round_in',$round_in, 2, 3);
                        
                    $round_total = $argv['round_total'];
                    $stmt->bindParam(':round_total',$round_total, 2, 3);
                        
                    $round_total_gnr =  $argv['round_total_gnr'] ?? '0';
                    $stmt->bindParam(':round_total_gnr',$round_total_gnr, 2, 11);
                        
                    $round_total_ffs =  $argv['round_total_ffs'] ?? '0';
                    $stmt->bindParam(':round_total_ffs',$round_total_ffs, 2, 3);
                        
                    $round_total_putts =  $argv['round_total_putts'] ?? null;
                    $stmt->bindParam(':round_total_putts',$round_total_putts, 2, 11);
                                
                    $round_input_complete =  $argv['round_input_complete'] ?? '0';
                    $stmt->bindParam(':round_input_complete',$round_input_complete, 0, 1);
                        
                    $round_tee_box_color = $argv['round_tee_box_color'];
                    $stmt->bindParam(':round_tee_box_color',$round_tee_box_color, 2, 10);
        


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
            if (!\array_key_exists($key, self::COLUMNS)){
                return false;
            }
        }

        $sql = 'UPDATE StatsCoach.carbon_golf_course_rounds ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

            if (array_key_exists('user_id', $argv)) {
                $set .= 'user_id=UNHEX(:user_id),';
            }
            if (array_key_exists('round_id', $argv)) {
                $set .= 'round_id=UNHEX(:round_id),';
            }
            if (array_key_exists('course_id', $argv)) {
                $set .= 'course_id=UNHEX(:course_id),';
            }
            if (array_key_exists('round_json', $argv)) {
                $set .= 'round_json=:round_json,';
            }
            if (array_key_exists('round_public', $argv)) {
                $set .= 'round_public=:round_public,';
            }
            if (array_key_exists('round_out', $argv)) {
                $set .= 'round_out=:round_out,';
            }
            if (array_key_exists('round_in', $argv)) {
                $set .= 'round_in=:round_in,';
            }
            if (array_key_exists('round_total', $argv)) {
                $set .= 'round_total=:round_total,';
            }
            if (array_key_exists('round_total_gnr', $argv)) {
                $set .= 'round_total_gnr=:round_total_gnr,';
            }
            if (array_key_exists('round_total_ffs', $argv)) {
                $set .= 'round_total_ffs=:round_total_ffs,';
            }
            if (array_key_exists('round_total_putts', $argv)) {
                $set .= 'round_total_putts=:round_total_putts,';
            }
            if (array_key_exists('round_date', $argv)) {
                $set .= 'round_date=:round_date,';
            }
            if (array_key_exists('round_input_complete', $argv)) {
                $set .= 'round_input_complete=:round_input_complete,';
            }
            if (array_key_exists('round_tee_box_color', $argv)) {
                $set .= 'round_tee_box_color=:round_tee_box_color,';
            }

        if (empty($set)){
            return false;
        }

        $sql .= substr($set, 0, -1);

        $pdo = self::database();

        $sql .= ' WHERE  round_id=UNHEX('.self::addInjection($primary, $pdo).')';

        self::jsonSQLReporting(\func_get_args(), $sql);

        $stmt = $pdo->prepare($sql);

                   if (array_key_exists('user_id', $argv)) {
            $user_id = $argv['user_id'];
            $stmt->bindParam(':user_id',$user_id, 2, 16);
        }
                   if (array_key_exists('round_id', $argv)) {
            $round_id = $argv['round_id'];
            $stmt->bindParam(':round_id',$round_id, 2, 16);
        }
                   if (array_key_exists('course_id', $argv)) {
            $course_id = $argv['course_id'];
            $stmt->bindParam(':course_id',$course_id, 2, 16);
        }
                   if (array_key_exists('round_json', $argv)) {
            $stmt->bindValue(':round_json',json_encode($argv['round_json']), 2);
        }
                   if (array_key_exists('round_public', $argv)) {
            $round_public = $argv['round_public'];
            $stmt->bindParam(':round_public',$round_public, 2, 1);
        }
                   if (array_key_exists('round_out', $argv)) {
            $round_out = $argv['round_out'];
            $stmt->bindParam(':round_out',$round_out, 2, 2);
        }
                   if (array_key_exists('round_in', $argv)) {
            $round_in = $argv['round_in'];
            $stmt->bindParam(':round_in',$round_in, 2, 3);
        }
                   if (array_key_exists('round_total', $argv)) {
            $round_total = $argv['round_total'];
            $stmt->bindParam(':round_total',$round_total, 2, 3);
        }
                   if (array_key_exists('round_total_gnr', $argv)) {
            $round_total_gnr = $argv['round_total_gnr'];
            $stmt->bindParam(':round_total_gnr',$round_total_gnr, 2, 11);
        }
                   if (array_key_exists('round_total_ffs', $argv)) {
            $round_total_ffs = $argv['round_total_ffs'];
            $stmt->bindParam(':round_total_ffs',$round_total_ffs, 2, 3);
        }
                   if (array_key_exists('round_total_putts', $argv)) {
            $round_total_putts = $argv['round_total_putts'];
            $stmt->bindParam(':round_total_putts',$round_total_putts, 2, 11);
        }
                   if (array_key_exists('round_date', $argv)) {
            $stmt->bindValue(':round_date',$argv['round_date'], 2);
        }
                   if (array_key_exists('round_input_complete', $argv)) {
            $round_input_complete = $argv['round_input_complete'];
            $stmt->bindParam(':round_input_complete',$round_input_complete, 0, 1);
        }
                   if (array_key_exists('round_tee_box_color', $argv)) {
            $round_tee_box_color = $argv['round_tee_box_color'];
            $stmt->bindParam(':round_tee_box_color',$round_tee_box_color, 2, 10);
        }

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
        if (null !== $primary) {
            return carbons::Delete($remove, $primary, $argv);
        }

        /**
         *   While useful, we've decided to disallow full
         *   table deletions through the rest api. For the
         *   n00bs and future self, "I got chu."
         */
        if (empty($argv)) {
            return false;
        }

        self::$injection = [];
        /** @noinspection SqlResolve */
        $sql = 'DELETE c FROM StatsCoach.carbons c 
                JOIN StatsCoach.carbon_golf_course_rounds on c.entity_pk = follower_table_id';

        $pdo = self::database();

        $sql .= ' WHERE ' . self::buildWhere($argv, $pdo);

        self::jsonSQLReporting(\func_get_args(), $sql);

        $stmt = $pdo->prepare($sql);

        $r = self::bind($stmt, $argv);

        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $r and $remove = null;

        return $r;
    }
}
