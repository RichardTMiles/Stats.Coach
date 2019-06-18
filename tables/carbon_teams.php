<?php
namespace Tables;


use CarbonPHP\Database;
use CarbonPHP\Interfaces\iRest;


class carbon_teams extends Database implements iRest
{

    public const TEAM_ID = 'team_id';
    public const TEAM_COACH = 'team_coach';
    public const PARENT_TEAM = 'parent_team';
    public const TEAM_CODE = 'team_code';
    public const TEAM_NAME = 'team_name';
    public const TEAM_RANK = 'team_rank';
    public const TEAM_SPORT = 'team_sport';
    public const TEAM_DIVISION = 'team_division';
    public const TEAM_SCHOOL = 'team_school';
    public const TEAM_DISTRICT = 'team_district';
    public const TEAM_MEMBERSHIP = 'team_membership';
    public const TEAM_PHOTO = 'team_photo';

    public const PRIMARY = [
    'team_id',
    ];

    public const COLUMNS = [
        'team_id' => [ 'binary', '2', '16' ],'team_coach' => [ 'binary', '2', '16' ],'parent_team' => [ 'binary', '2', '16' ],'team_code' => [ 'varchar', '2', '225' ],'team_name' => [ 'varchar', '2', '225' ],'team_rank' => [ 'int', '2', '11' ],'team_sport' => [ 'varchar', '2', '225' ],'team_division' => [ 'varchar', '2', '225' ],'team_school' => [ 'varchar', '2', '225' ],'team_district' => [ 'varchar', '2', '225' ],'team_membership' => [ 'varchar', '2', '225' ],'team_photo' => [ 'binary', '2', '16' ],
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
                
                   if (array_key_exists('team_id', $argv)) {
            $team_id = $argv['team_id'];
            $stmt->bindParam(':team_id',$team_id, 2, 16);
        }
                   if (array_key_exists('team_coach', $argv)) {
            $team_coach = $argv['team_coach'];
            $stmt->bindParam(':team_coach',$team_coach, 2, 16);
        }
                   if (array_key_exists('parent_team', $argv)) {
            $parent_team = $argv['parent_team'];
            $stmt->bindParam(':parent_team',$parent_team, 2, 16);
        }
                   if (array_key_exists('team_code', $argv)) {
            $team_code = $argv['team_code'];
            $stmt->bindParam(':team_code',$team_code, 2, 225);
        }
                   if (array_key_exists('team_name', $argv)) {
            $team_name = $argv['team_name'];
            $stmt->bindParam(':team_name',$team_name, 2, 225);
        }
                   if (array_key_exists('team_rank', $argv)) {
            $team_rank = $argv['team_rank'];
            $stmt->bindParam(':team_rank',$team_rank, 2, 11);
        }
                   if (array_key_exists('team_sport', $argv)) {
            $team_sport = $argv['team_sport'];
            $stmt->bindParam(':team_sport',$team_sport, 2, 225);
        }
                   if (array_key_exists('team_division', $argv)) {
            $team_division = $argv['team_division'];
            $stmt->bindParam(':team_division',$team_division, 2, 225);
        }
                   if (array_key_exists('team_school', $argv)) {
            $team_school = $argv['team_school'];
            $stmt->bindParam(':team_school',$team_school, 2, 225);
        }
                   if (array_key_exists('team_district', $argv)) {
            $team_district = $argv['team_district'];
            $stmt->bindParam(':team_district',$team_district, 2, 225);
        }
                   if (array_key_exists('team_membership', $argv)) {
            $team_membership = $argv['team_membership'];
            $stmt->bindParam(':team_membership',$team_membership, 2, 225);
        }
                   if (array_key_exists('team_photo', $argv)) {
            $team_photo = $argv['team_photo'];
            $stmt->bindParam(':team_photo',$team_photo, 2, 16);
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
                    $order .= 'team_id ASC';
                }
            }
            $limit = "$order $limit";
        } else {
            $limit = ' ORDER BY team_id ASC LIMIT 100';
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
                if (!preg_match('#(((((hex|argv|count|sum|min|max) *\(+ *)+)|(distinct|\*|\+|\-|\/| |team_id|team_coach|parent_team|team_code|team_name|team_rank|team_sport|team_division|team_school|team_district|team_membership|team_photo))+\)*)+ *(as [a-z]+)?#i', $column)) {
                    return false;
                }
                $sql .= $column;
                $aggregate = true;
            }
        }

        $sql = 'SELECT ' .  $sql . ' FROM StatsCoach.carbon_teams';

        if (null === $primary) {
            /** @noinspection NestedPositiveIfStatementsInspection */
            if (!empty($where)) {
                $sql .= ' WHERE ' . self::buildWhere($where, $pdo);
            }
        } else {
        $sql .= ' WHERE  team_id=UNHEX('.self::addInjection($primary, $pdo).')';
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
        $sql = 'INSERT INTO StatsCoach.carbon_teams (team_id, team_coach, parent_team, team_code, team_name, team_rank, team_sport, team_division, team_school, team_district, team_membership, team_photo) VALUES ( UNHEX(:team_id), UNHEX(:team_coach), UNHEX(:parent_team), :team_code, :team_name, :team_rank, :team_sport, :team_division, :team_school, :team_district, :team_membership, UNHEX(:team_photo))';

        self::jsonSQLReporting(\func_get_args(), $sql);

        $stmt = self::database()->prepare($sql);

                $team_id = $id = $argv['team_id'] ?? self::beginTransaction('carbon_teams');
                $stmt->bindParam(':team_id',$team_id, 2, 16);
                
                    $team_coach = $argv['team_coach'];
                    $stmt->bindParam(':team_coach',$team_coach, 2, 16);
                        
                    $parent_team =  $argv['parent_team'] ?? null;
                    $stmt->bindParam(':parent_team',$parent_team, 2, 16);
                        
                    $team_code = $argv['team_code'];
                    $stmt->bindParam(':team_code',$team_code, 2, 225);
                        
                    $team_name = $argv['team_name'];
                    $stmt->bindParam(':team_name',$team_name, 2, 225);
                        
                    $team_rank =  $argv['team_rank'] ?? '0';
                    $stmt->bindParam(':team_rank',$team_rank, 2, 11);
                        
                    $team_sport =  $argv['team_sport'] ?? 'Golf';
                    $stmt->bindParam(':team_sport',$team_sport, 2, 225);
                        
                    $team_division =  $argv['team_division'] ?? null;
                    $stmt->bindParam(':team_division',$team_division, 2, 225);
                        
                    $team_school =  $argv['team_school'] ?? null;
                    $stmt->bindParam(':team_school',$team_school, 2, 225);
                        
                    $team_district =  $argv['team_district'] ?? null;
                    $stmt->bindParam(':team_district',$team_district, 2, 225);
                        
                    $team_membership =  $argv['team_membership'] ?? null;
                    $stmt->bindParam(':team_membership',$team_membership, 2, 225);
                        
                    $team_photo =  $argv['team_photo'] ?? null;
                    $stmt->bindParam(':team_photo',$team_photo, 2, 16);
        


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

        $sql = 'UPDATE StatsCoach.carbon_teams ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

            if (array_key_exists('team_id', $argv)) {
                $set .= 'team_id=UNHEX(:team_id),';
            }
            if (array_key_exists('team_coach', $argv)) {
                $set .= 'team_coach=UNHEX(:team_coach),';
            }
            if (array_key_exists('parent_team', $argv)) {
                $set .= 'parent_team=UNHEX(:parent_team),';
            }
            if (array_key_exists('team_code', $argv)) {
                $set .= 'team_code=:team_code,';
            }
            if (array_key_exists('team_name', $argv)) {
                $set .= 'team_name=:team_name,';
            }
            if (array_key_exists('team_rank', $argv)) {
                $set .= 'team_rank=:team_rank,';
            }
            if (array_key_exists('team_sport', $argv)) {
                $set .= 'team_sport=:team_sport,';
            }
            if (array_key_exists('team_division', $argv)) {
                $set .= 'team_division=:team_division,';
            }
            if (array_key_exists('team_school', $argv)) {
                $set .= 'team_school=:team_school,';
            }
            if (array_key_exists('team_district', $argv)) {
                $set .= 'team_district=:team_district,';
            }
            if (array_key_exists('team_membership', $argv)) {
                $set .= 'team_membership=:team_membership,';
            }
            if (array_key_exists('team_photo', $argv)) {
                $set .= 'team_photo=UNHEX(:team_photo),';
            }

        if (empty($set)){
            return false;
        }

        $sql .= substr($set, 0, -1);

        $pdo = self::database();

        $sql .= ' WHERE  team_id=UNHEX('.self::addInjection($primary, $pdo).')';

        self::jsonSQLReporting(\func_get_args(), $sql);

        $stmt = $pdo->prepare($sql);

                   if (array_key_exists('team_id', $argv)) {
            $team_id = $argv['team_id'];
            $stmt->bindParam(':team_id',$team_id, 2, 16);
        }
                   if (array_key_exists('team_coach', $argv)) {
            $team_coach = $argv['team_coach'];
            $stmt->bindParam(':team_coach',$team_coach, 2, 16);
        }
                   if (array_key_exists('parent_team', $argv)) {
            $parent_team = $argv['parent_team'];
            $stmt->bindParam(':parent_team',$parent_team, 2, 16);
        }
                   if (array_key_exists('team_code', $argv)) {
            $team_code = $argv['team_code'];
            $stmt->bindParam(':team_code',$team_code, 2, 225);
        }
                   if (array_key_exists('team_name', $argv)) {
            $team_name = $argv['team_name'];
            $stmt->bindParam(':team_name',$team_name, 2, 225);
        }
                   if (array_key_exists('team_rank', $argv)) {
            $team_rank = $argv['team_rank'];
            $stmt->bindParam(':team_rank',$team_rank, 2, 11);
        }
                   if (array_key_exists('team_sport', $argv)) {
            $team_sport = $argv['team_sport'];
            $stmt->bindParam(':team_sport',$team_sport, 2, 225);
        }
                   if (array_key_exists('team_division', $argv)) {
            $team_division = $argv['team_division'];
            $stmt->bindParam(':team_division',$team_division, 2, 225);
        }
                   if (array_key_exists('team_school', $argv)) {
            $team_school = $argv['team_school'];
            $stmt->bindParam(':team_school',$team_school, 2, 225);
        }
                   if (array_key_exists('team_district', $argv)) {
            $team_district = $argv['team_district'];
            $stmt->bindParam(':team_district',$team_district, 2, 225);
        }
                   if (array_key_exists('team_membership', $argv)) {
            $team_membership = $argv['team_membership'];
            $stmt->bindParam(':team_membership',$team_membership, 2, 225);
        }
                   if (array_key_exists('team_photo', $argv)) {
            $team_photo = $argv['team_photo'];
            $stmt->bindParam(':team_photo',$team_photo, 2, 16);
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
                JOIN StatsCoach.carbon_teams on c.entity_pk = follower_table_id';

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
