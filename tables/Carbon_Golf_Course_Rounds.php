<?php
namespace Tables;


use PDO;
use PDOStatement;

use function array_key_exists;
use function count;
use function is_array;
use CarbonPHP\Rest;
use CarbonPHP\Interfaces\iRest;
use CarbonPHP\Interfaces\iRestfulReferences;
use CarbonPHP\Error\PublicAlert;


class Carbon_Golf_Course_Rounds extends Rest implements iRest
{
    
    public const TABLE_NAME = 'carbon_golf_course_rounds';
    public const USER_ID = 'carbon_golf_course_rounds.user_id'; 
    public const ROUND_ID = 'carbon_golf_course_rounds.round_id'; 
    public const COURSE_ID = 'carbon_golf_course_rounds.course_id'; 
    public const ROUND_JSON = 'carbon_golf_course_rounds.round_json'; 
    public const ROUND_PUBLIC = 'carbon_golf_course_rounds.round_public'; 
    public const ROUND_OUT = 'carbon_golf_course_rounds.round_out'; 
    public const ROUND_IN = 'carbon_golf_course_rounds.round_in'; 
    public const ROUND_TOTAL = 'carbon_golf_course_rounds.round_total'; 
    public const ROUND_TOTAL_GNR = 'carbon_golf_course_rounds.round_total_gnr'; 
    public const ROUND_TOTAL_FFS = 'carbon_golf_course_rounds.round_total_ffs'; 
    public const ROUND_TOTAL_PUTTS = 'carbon_golf_course_rounds.round_total_putts'; 
    public const ROUND_DATE = 'carbon_golf_course_rounds.round_date'; 
    public const ROUND_INPUT_COMPLETE = 'carbon_golf_course_rounds.round_input_complete'; 
    public const ROUND_TEE_BOX_COLOR = 'carbon_golf_course_rounds.round_tee_box_color'; 

    public const PRIMARY = [
        'carbon_golf_course_rounds.round_id',
    ];

    public const COLUMNS = [
        'carbon_golf_course_rounds.user_id' => 'user_id','carbon_golf_course_rounds.round_id' => 'round_id','carbon_golf_course_rounds.course_id' => 'course_id','carbon_golf_course_rounds.round_json' => 'round_json','carbon_golf_course_rounds.round_public' => 'round_public','carbon_golf_course_rounds.round_out' => 'round_out','carbon_golf_course_rounds.round_in' => 'round_in','carbon_golf_course_rounds.round_total' => 'round_total','carbon_golf_course_rounds.round_total_gnr' => 'round_total_gnr','carbon_golf_course_rounds.round_total_ffs' => 'round_total_ffs','carbon_golf_course_rounds.round_total_putts' => 'round_total_putts','carbon_golf_course_rounds.round_date' => 'round_date','carbon_golf_course_rounds.round_input_complete' => 'round_input_complete','carbon_golf_course_rounds.round_tee_box_color' => 'round_tee_box_color',
    ];

    public const PDO_VALIDATION = [
        'carbon_golf_course_rounds.user_id' => ['binary', '2', '16'],'carbon_golf_course_rounds.round_id' => ['binary', '2', '16'],'carbon_golf_course_rounds.course_id' => ['binary', '2', '16'],'carbon_golf_course_rounds.round_json' => ['json', '2', ''],'carbon_golf_course_rounds.round_public' => ['int', '2', '1'],'carbon_golf_course_rounds.round_out' => ['int', '2', '2'],'carbon_golf_course_rounds.round_in' => ['int', '2', '3'],'carbon_golf_course_rounds.round_total' => ['int', '2', '3'],'carbon_golf_course_rounds.round_total_gnr' => ['int', '2', '11'],'carbon_golf_course_rounds.round_total_ffs' => ['int', '2', '3'],'carbon_golf_course_rounds.round_total_putts' => ['int', '2', '11'],'carbon_golf_course_rounds.round_date' => ['datetime', '2', ''],'carbon_golf_course_rounds.round_input_complete' => ['tinyint', '0', '1'],'carbon_golf_course_rounds.round_tee_box_color' => ['varchar', '2', '10'],
    ];
    
    public const VALIDATION = [];

    public static array $injection = [];

    public static function jsonSQLReporting($argv, $sql) : void {
        global $json;
        if (!is_array($json)) {
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
    
    public static function buildWhere(array $set, PDO $pdo, $join = 'AND') : string
    {
        $sql = '(';
        $bump = false;
        foreach ($set as $column => $value) {
            if (is_array($value)) {
                if ($bump) {
                    $sql .= " $join ";
                }
                $bump = true;
                $sql .= self::buildWhere($value, $pdo, $join === 'AND' ? 'OR' : 'AND');
            } else if (array_key_exists($column, self::PDO_VALIDATION)) {
                $bump = false;
                /** @noinspection SubStrUsedAsStrPosInspection */
                if (substr($value, 0, '8') === 'C6SUB461') {
                    $subQuery = substr($value, '8');
                    $sql .= "($column = $subQuery ) $join ";
                } else if (self::PDO_VALIDATION[$column][0] === 'binary') {
                    $sql .= "($column = UNHEX(" . self::addInjection($value, $pdo) . ")) $join ";
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

    public static function addInjection($value, PDO $pdo, $quote = false): string
    {
        $inject = ':injection' . count(self::$injection) . 'carbon_golf_course_rounds';
        self::$injection[$inject] = $quote ? $pdo->quote($value) : $value;
        return $inject;
    }

    public static function bind(PDOStatement $stmt): void 
    {
        foreach (self::$injection as $key => $value) {
            $stmt->bindValue($key,$value);
        }
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
    public static function Get(array &$return, string $primary = null, array $argv): bool
    {
        $pdo = self::database();

        $sql = self::buildSelectQuery($primary, $argv, $pdo);
        
        $stmt = $pdo->prepare($sql);

        self::bind($stmt);

        if (!$stmt->execute()) {
            return false;
        }

        $return = $stmt->fetchAll(PDO::FETCH_ASSOC);

        /**
        *   The next part is so every response from the rest api
        *   formats to a set of rows. Even if only one row is returned.
        *   You must set the third parameter to true, otherwise '0' is
        *   apparently in the self::PDO_VALIDATION
        */

        
        if ($primary !== null || (isset($argv['pagination']['limit']) && $argv['pagination']['limit'] === 1 && count($return) === 1)) {
            $return = isset($return[0]) && is_array($return[0]) ? $return[0] : $return;
            // promise this is needed and will still return the desired array except for a single record will not be an array
        if (array_key_exists('carbon_golf_course_rounds.round_json', $return)) {
                $return['round_json'] = json_decode($return['round_json'], true);
            }
        
        }

        return true;
    }

    /**
     * @param array $argv
     * @param string|null $dependantEntityId - a C6 Hex entity key 
     * @return bool|string
     * @throws PublicAlert
     */
    public static function Post(array $argv, string $dependantEntityId = null)
    {
        self::$injection = [];
        /** @noinspection SqlResolve */
        $sql = 'INSERT INTO StatsCoach.carbon_golf_course_rounds (user_id, round_id, course_id, round_json, round_public, round_out, round_in, round_total, round_total_gnr, round_total_ffs, round_total_putts, round_input_complete, round_tee_box_color) VALUES ( UNHEX(:user_id), UNHEX(:round_id), UNHEX(:course_id), :round_json, :round_public, :round_out, :round_in, :round_total, :round_total_gnr, :round_total_ffs, :round_total_putts, :round_input_complete, :round_tee_box_color)';

        self::jsonSQLReporting(\func_get_args(), $sql);

        $stmt = self::database()->prepare($sql);

    
        $user_id = $argv['carbon_golf_course_rounds.user_id'];
        $stmt->bindParam(':user_id',$user_id, 2, 16);
    
        $round_id = $id = $argv['carbon_golf_course_rounds.round_id'] ?? self::beginTransaction('carbon_golf_course_rounds', $dependantEntityId);
        $stmt->bindParam(':round_id',$round_id, 2, 16);
    
        $course_id = $argv['carbon_golf_course_rounds.course_id'];
        $stmt->bindParam(':course_id',$course_id, 2, 16);
    
        $stmt->bindValue(':round_json',json_encode($argv['carbon_golf_course_rounds.round_json']), 2);

        $round_public =  $argv['carbon_golf_course_rounds.round_public'] ?? '1';
        $stmt->bindParam(':round_public',$round_public, 2, 1);
    
        $round_out = $argv['carbon_golf_course_rounds.round_out'];
        $stmt->bindParam(':round_out',$round_out, 2, 2);
    
        $round_in = $argv['carbon_golf_course_rounds.round_in'];
        $stmt->bindParam(':round_in',$round_in, 2, 3);
    
        $round_total = $argv['carbon_golf_course_rounds.round_total'];
        $stmt->bindParam(':round_total',$round_total, 2, 3);
    
        $round_total_gnr =  $argv['carbon_golf_course_rounds.round_total_gnr'] ?? '0';
        $stmt->bindParam(':round_total_gnr',$round_total_gnr, 2, 11);
    
        $round_total_ffs =  $argv['carbon_golf_course_rounds.round_total_ffs'] ?? '0';
        $stmt->bindParam(':round_total_ffs',$round_total_ffs, 2, 3);
    
        $round_total_putts =  $argv['carbon_golf_course_rounds.round_total_putts'] ?? null;
        $stmt->bindParam(':round_total_putts',$round_total_putts, 2, 11);
    
        $round_input_complete =  $argv['carbon_golf_course_rounds.round_input_complete'] ?? '0';
        $stmt->bindParam(':round_input_complete',$round_input_complete, 0, 1);
    
        $round_tee_box_color = $argv['carbon_golf_course_rounds.round_tee_box_color'];
        $stmt->bindParam(':round_tee_box_color',$round_tee_box_color, 2, 10);
    


        return $stmt->execute() ? $id : false;
    
    }
     
    public static function subSelect(string $primary = null, array $argv, PDO $pdo = null): string
    {
        return 'C6SUB461' . self::buildSelectQuery($primary, $argv, $pdo, true);
    }
    
    public static function validateSelectColumn($column) : bool {
        return (bool) preg_match('#(((((hex|argv|count|sum|min|max) *\(+ *)+)|(distinct|\*|\+|-|/| |carbon_golf_course_rounds\.user_id|carbon_golf_course_rounds\.round_id|carbon_golf_course_rounds\.course_id|carbon_golf_course_rounds\.round_json|carbon_golf_course_rounds\.round_public|carbon_golf_course_rounds\.round_out|carbon_golf_course_rounds\.round_in|carbon_golf_course_rounds\.round_total|carbon_golf_course_rounds\.round_total_gnr|carbon_golf_course_rounds\.round_total_ffs|carbon_golf_course_rounds\.round_total_putts|carbon_golf_course_rounds\.round_date|carbon_golf_course_rounds\.round_input_complete|carbon_golf_course_rounds\.round_tee_box_color))+\)*)+ *(as [a-z]+)?#i', $column);
    }
    
    public static function buildSelectQuery(string $primary = null, array $argv, PDO $pdo = null, bool $noHEX = false) : string 
    {
        if ($pdo === null) {
            $pdo = self::database();
        }
        self::$injection = [];
        $aggregate = false;
        $group = [];
        $sql = '';
        $get = $argv['select'] ?? array_keys(self::PDO_VALIDATION);
        $where = $argv['where'] ?? [];

        // pagination
        if (array_key_exists('pagination',$argv)) {
            if (!empty($argv['pagination']) && !is_array($argv['pagination'])) {
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
                    if (is_array($argv['pagination']['order'])) {
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

        // join 
        $join = ''; 
        $tableList = [];
        if (array_key_exists('join', $argv)) {
            foreach ($argv['join'] as $by => $tables) {
                $buildJoin = static function ($method) use ($tables, &$join, &$tableList) {
                    foreach ($tables as $table => $stmt) {
                        $tableList[] = $table;
                        switch (count($stmt)) {
                            case 2: 
                                if (is_string($stmt[0]) && is_string($stmt[1])) {
                                    $join .= $method . $table . ' ON ' . $stmt[0] . '=' . $stmt[1];
                                } else {
                                    return false; // todo debugging
                                }
                                break;
                            case 3:
                                if (is_string($stmt[0]) && is_string($stmt[1]) && is_string($stmt[2])) {
                                    $join .= $method . $table . ' ON ' . $stmt[0] . $stmt[1] . $stmt[2]; 
                                } else {
                                    return false; // todo debugging
                                }
                                break;
                            default:
                                return false; // todo debug check, common when joins are not a list of values
                        }
                    }
                    return true;
                };
                switch ($by) {
                    case 'inner':
                        if (!$buildJoin(' INNER JOIN ')) {
                            return false; 
                        }
                        break;
                    case 'left':
                        if (!$buildJoin(' LEFT JOIN ')) {
                            return false; 
                        }
                        break;
                    case 'right':
                        if (!$buildJoin(' RIGHT JOIN ')) {
                            return false; 
                        }
                        break;
                    default:
                        return false; // todo - debugging stmts
                }
            }
        }

        // Select
        foreach($get as $key => $column){
            if (!empty($sql)) {
                $sql .= ', ';
            }
            $columnExists = array_key_exists($column, self::PDO_VALIDATION);
            if ($columnExists) {
                if (!$noHEX && self::PDO_VALIDATION[$column][0] === 'binary') {
                    $asShort = trim($column, self::TABLE_NAME . '.');
                    $prefix = self::TABLE_NAME . '.';
                    if (strpos($column, $prefix) === 0) {
                        $asShort = substr($column, strlen($prefix));
                    }
                    $sql .= "HEX($column) as $asShort";
                    $group[] = $column;
                } elseif ($columnExists) {
                    $sql .= $column;
                    $group[] = $column;  
                }  
            } else if (self::validateSelectColumn($column)) {
                $sql .= $column;
                $group[] = $column;
                $aggregate = true;
            } else {  
                $valid = false;
                $tablesReffrenced = $tableList;
                while (!empty($tablesReffrenced)) {
                     $table = __NAMESPACE__ . '\\' . array_pop($tablesReffrenced);
                     
                     if (!class_exists($table)){
                         continue;
                     }
                     $imp = array_map('strtolower', array_keys(class_implements($table)));
                    
                   
                     /** @noinspection ClassConstantUsageCorrectnessInspection */
                     if (!in_array(strtolower(iRest::class), $imp, true) && 
                         !in_array(strtolower(iRestfulReferences::class), $imp, true)) {
                         continue;
                     }
                     /** @noinspection PhpUndefinedMethodInspection */
                     if ($table::validateSelectColumn($column)) { 
                        $group[] = $column;
                        $valid = true;
                        break; 
                     }
                }
                if (!$valid) {
                    return false;
                }
                $sql .= $column;
                $aggregate = true;
            }
        }

        $sql = 'SELECT ' .  $sql . ' FROM StatsCoach.carbon_golf_course_rounds ' . $join;
       
        if (null === $primary) {
            /** @noinspection NestedPositiveIfStatementsInspection */
            if (!empty($where)) {
                $sql .= ' WHERE ' . self::buildWhere($where, $pdo);
            }
        } else {
            $sql .= ' WHERE  round_id=UNHEX('.self::addInjection($primary, $pdo).')';
        }

        if ($aggregate  && !empty($group)) {
            $sql .= ' GROUP BY ' . implode(', ', $group). ' ';
        }

        $sql .= $limit;

        self::jsonSQLReporting(\func_get_args(), $sql);

        return '(' . $sql . ')';
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
        
        if (array_key_exists(self::UPDATE, $argv)) {
            $argv = $argv[self::UPDATE];
        }
        
        foreach ($argv as $key => $value) {
            if (!array_key_exists($key, self::PDO_VALIDATION)){
                return false;
            }
        }

        $sql = 'UPDATE StatsCoach.carbon_golf_course_rounds ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

        if (array_key_exists('carbon_golf_course_rounds.user_id', $argv)) {
            $set .= 'user_id=UNHEX(:user_id),';
        }
        if (array_key_exists('carbon_golf_course_rounds.round_id', $argv)) {
            $set .= 'round_id=UNHEX(:round_id),';
        }
        if (array_key_exists('carbon_golf_course_rounds.course_id', $argv)) {
            $set .= 'course_id=UNHEX(:course_id),';
        }
        if (array_key_exists('carbon_golf_course_rounds.round_json', $argv)) {
            $set .= 'round_json=:round_json,';
        }
        if (array_key_exists('carbon_golf_course_rounds.round_public', $argv)) {
            $set .= 'round_public=:round_public,';
        }
        if (array_key_exists('carbon_golf_course_rounds.round_out', $argv)) {
            $set .= 'round_out=:round_out,';
        }
        if (array_key_exists('carbon_golf_course_rounds.round_in', $argv)) {
            $set .= 'round_in=:round_in,';
        }
        if (array_key_exists('carbon_golf_course_rounds.round_total', $argv)) {
            $set .= 'round_total=:round_total,';
        }
        if (array_key_exists('carbon_golf_course_rounds.round_total_gnr', $argv)) {
            $set .= 'round_total_gnr=:round_total_gnr,';
        }
        if (array_key_exists('carbon_golf_course_rounds.round_total_ffs', $argv)) {
            $set .= 'round_total_ffs=:round_total_ffs,';
        }
        if (array_key_exists('carbon_golf_course_rounds.round_total_putts', $argv)) {
            $set .= 'round_total_putts=:round_total_putts,';
        }
        if (array_key_exists('carbon_golf_course_rounds.round_date', $argv)) {
            $set .= 'round_date=:round_date,';
        }
        if (array_key_exists('carbon_golf_course_rounds.round_input_complete', $argv)) {
            $set .= 'round_input_complete=:round_input_complete,';
        }
        if (array_key_exists('carbon_golf_course_rounds.round_tee_box_color', $argv)) {
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

        if (array_key_exists('carbon_golf_course_rounds.user_id', $argv)) {
            $user_id = $argv['carbon_golf_course_rounds.user_id'];
            $stmt->bindParam(':user_id',$user_id, 2, 16);
        }
        if (array_key_exists('carbon_golf_course_rounds.round_id', $argv)) {
            $round_id = $argv['carbon_golf_course_rounds.round_id'];
            $stmt->bindParam(':round_id',$round_id, 2, 16);
        }
        if (array_key_exists('carbon_golf_course_rounds.course_id', $argv)) {
            $course_id = $argv['carbon_golf_course_rounds.course_id'];
            $stmt->bindParam(':course_id',$course_id, 2, 16);
        }
        if (array_key_exists('carbon_golf_course_rounds.round_json', $argv)) {
            $stmt->bindValue(':round_json',json_encode($argv['carbon_golf_course_rounds.round_json']), 2);
        }
        if (array_key_exists('carbon_golf_course_rounds.round_public', $argv)) {
            $round_public = $argv['carbon_golf_course_rounds.round_public'];
            $stmt->bindParam(':round_public',$round_public, 2, 1);
        }
        if (array_key_exists('carbon_golf_course_rounds.round_out', $argv)) {
            $round_out = $argv['carbon_golf_course_rounds.round_out'];
            $stmt->bindParam(':round_out',$round_out, 2, 2);
        }
        if (array_key_exists('carbon_golf_course_rounds.round_in', $argv)) {
            $round_in = $argv['carbon_golf_course_rounds.round_in'];
            $stmt->bindParam(':round_in',$round_in, 2, 3);
        }
        if (array_key_exists('carbon_golf_course_rounds.round_total', $argv)) {
            $round_total = $argv['carbon_golf_course_rounds.round_total'];
            $stmt->bindParam(':round_total',$round_total, 2, 3);
        }
        if (array_key_exists('carbon_golf_course_rounds.round_total_gnr', $argv)) {
            $round_total_gnr = $argv['carbon_golf_course_rounds.round_total_gnr'];
            $stmt->bindParam(':round_total_gnr',$round_total_gnr, 2, 11);
        }
        if (array_key_exists('carbon_golf_course_rounds.round_total_ffs', $argv)) {
            $round_total_ffs = $argv['carbon_golf_course_rounds.round_total_ffs'];
            $stmt->bindParam(':round_total_ffs',$round_total_ffs, 2, 3);
        }
        if (array_key_exists('carbon_golf_course_rounds.round_total_putts', $argv)) {
            $round_total_putts = $argv['carbon_golf_course_rounds.round_total_putts'];
            $stmt->bindParam(':round_total_putts',$round_total_putts, 2, 11);
        }
        if (array_key_exists('carbon_golf_course_rounds.round_date', $argv)) {
            $stmt->bindValue(':round_date',$argv['carbon_golf_course_rounds.round_date'], 2);
        }
        if (array_key_exists('carbon_golf_course_rounds.round_input_complete', $argv)) {
            $round_input_complete = $argv['carbon_golf_course_rounds.round_input_complete'];
            $stmt->bindParam(':round_input_complete',$round_input_complete, 0, 1);
        }
        if (array_key_exists('carbon_golf_course_rounds.round_tee_box_color', $argv)) {
            $round_tee_box_color = $argv['carbon_golf_course_rounds.round_tee_box_color'];
            $stmt->bindParam(':round_tee_box_color',$round_tee_box_color, 2, 10);
        }

        self::bind($stmt);

        if (!$stmt->execute()) {
            return false;
        }
        
        $argv = array_combine(
            array_map(
                static function($k) { return str_replace('carbon_golf_course_rounds.', '', $k); },
                array_keys($argv)
            ),
            array_values($argv)
        );

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
            return Carbons::Delete($remove, $primary, $argv);
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
        /** @noinspection SqlWithoutWhere */
        $sql = 'DELETE c FROM StatsCoach.carbons c 
                JOIN StatsCoach.carbon_golf_course_rounds on c.entity_pk = carbon_golf_course_rounds.round_id';

        $pdo = self::database();

        $sql .= ' WHERE ' . self::buildWhere($argv, $pdo);
        
        self::jsonSQLReporting(\func_get_args(), $sql);

        $stmt = $pdo->prepare($sql);

        self::bind($stmt);

        $r = $stmt->execute();

        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $r and $remove = [];

        return $r;
    }
}
