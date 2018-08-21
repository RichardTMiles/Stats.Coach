<?php
namespace Table;


use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Interfaces\iRest;

class golf_tournaments extends Entities implements iRest
{
    const PRIMARY = [
    
    ];

    const COLUMNS = [
    'tournament_id','tournament_name','course_id','host_name','tournament_style','tournament_team_price','tournament_paid','tournament_date',
    ];

    const VALIDATION = [];

    const BINARY = [
    'tournament_id','tournament_name','course_id',
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
                    $order .= self::PRIMARY[0] . ' ASC';
                }
            }
            $limit = $order .' '. $limit;
        } else {
            $limit = ' ORDER BY ' . self::PRIMARY[0] . ' ASC LIMIT 100';
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

        $sql = 'SELECT ' .  $sql . ' FROM StatsCoach.golf_tournaments';

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
                    return rtrim($sql, " $join") . ')';
                };
                $sql .= ' WHERE ' . $build_where($where);
            }
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

        

        return true;
    }

    /**
    * @param array $argv
    * @return bool|mixed
    */
    public static function Post(array $argv)
    {
        $sql = 'INSERT INTO StatsCoach.golf_tournaments (tournament_id, tournament_name, course_id, host_name, tournament_style, tournament_team_price, tournament_paid, tournament_date) VALUES ( UNHEX(:tournament_id), UNHEX(:tournament_name), UNHEX(:course_id), :host_name, :tournament_style, :tournament_team_price, :tournament_paid, :tournament_date)';
        $stmt = Database::database()->prepare($sql);

        global $json;

        if (!isset($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;

            
                $tournament_id = $argv['tournament_id'];
                $stmt->bindParam(':tournament_id',$tournament_id, 2, 16);
                    
                $tournament_name = $argv['tournament_name'];
                $stmt->bindParam(':tournament_name',$tournament_name, 2, 16);
                    
                $course_id = isset($argv['course_id']) ? $argv['course_id'] : null;
                $stmt->bindParam(':course_id',$course_id, 2, 16);
                    
                $host_name = $argv['host_name'];
                $stmt->bindParam(':host_name',$host_name, 2, 225);
                    
                $tournament_style = $argv['tournament_style'];
                $stmt->bindParam(':tournament_style',$tournament_style, 2, 11);
                    
                $tournament_team_price = isset($argv['tournament_team_price']) ? $argv['tournament_team_price'] : null;
                $stmt->bindParam(':tournament_team_price',$tournament_team_price, 2, 11);
                    
                $tournament_paid = isset($argv['tournament_paid']) ? $argv['tournament_paid'] : '1';
                $stmt->bindParam(':tournament_paid',$tournament_paid, 2, 1);
                    $stmt->bindValue(':tournament_date',isset($argv['tournament_date']) ? $argv['tournament_date'] : null, 2);
        

        return $stmt->execute();
    }

    /**
    * @param array $return
    * @param string $primary
    * @param array $argv
    * @return bool
    */
    public static function Put(array &$return, string $primary, array $argv) : bool
    {
        if (empty($primary)) {
            return false;
        }

        foreach ($argv as $key => $value) {
            if (!in_array($key, self::COLUMNS)){
                unset($argv[$key]);
            }
        }

        $sql = 'UPDATE StatsCoach.golf_tournaments ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

        if (!empty($argv['tournament_id'])) {
            $set .= 'tournament_id=UNHEX(:tournament_id),';
        }
        if (!empty($argv['tournament_name'])) {
            $set .= 'tournament_name=UNHEX(:tournament_name),';
        }
        if (!empty($argv['course_id'])) {
            $set .= 'course_id=UNHEX(:course_id),';
        }
        if (!empty($argv['host_name'])) {
            $set .= 'host_name=:host_name,';
        }
        if (!empty($argv['tournament_style'])) {
            $set .= 'tournament_style=:tournament_style,';
        }
        if (!empty($argv['tournament_team_price'])) {
            $set .= 'tournament_team_price=:tournament_team_price,';
        }
        if (!empty($argv['tournament_paid'])) {
            $set .= 'tournament_paid=:tournament_paid,';
        }
        if (!empty($argv['tournament_date'])) {
            $set .= 'tournament_date=:tournament_date,';
        }

        if (empty($set)){
            return false;
        }

        $sql .= substr($set, 0, strlen($set)-1);

        $db = Database::database();

        

        $stmt = $db->prepare($sql);

        global $json;

        if (empty($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;

        if (!empty($argv['tournament_id'])) {
            $tournament_id = $argv['tournament_id'];
            $stmt->bindParam(':tournament_id',$tournament_id, 2, 16);
        }
        if (!empty($argv['tournament_name'])) {
            $tournament_name = $argv['tournament_name'];
            $stmt->bindParam(':tournament_name',$tournament_name, 2, 16);
        }
        if (!empty($argv['course_id'])) {
            $course_id = $argv['course_id'];
            $stmt->bindParam(':course_id',$course_id, 2, 16);
        }
        if (!empty($argv['host_name'])) {
            $host_name = $argv['host_name'];
            $stmt->bindParam(':host_name',$host_name, 2, 225);
        }
        if (!empty($argv['tournament_style'])) {
            $tournament_style = $argv['tournament_style'];
            $stmt->bindParam(':tournament_style',$tournament_style, 2, 11);
        }
        if (!empty($argv['tournament_team_price'])) {
            $tournament_team_price = $argv['tournament_team_price'];
            $stmt->bindParam(':tournament_team_price',$tournament_team_price, 2, 11);
        }
        if (!empty($argv['tournament_paid'])) {
            $tournament_paid = $argv['tournament_paid'];
            $stmt->bindParam(':tournament_paid',$tournament_paid, 2, 1);
        }
        if (!empty($argv['tournament_date'])) {
            $stmt->bindValue(':tournament_date',$argv['tournament_date'], 2);
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
        $sql = 'DELETE FROM StatsCoach.golf_tournaments ';

        foreach($argv as $column => $constraint){
            if (!in_array($column, self::COLUMNS)){
                unset($argv[$column]);
            }
        }

        if (empty($primary)) {
            /**
            *   While useful, we've decided to disallow full
            *   table deletions through the rest api. For the
            *   n00bs and future self, "I got chu."
            */
            if (empty($argv)) {
                return false;
            }
            $pdo = self::database();

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
                return rtrim($sql, " $join") . ')';
            };
            $sql .= ' WHERE ' . $build_where($argv);
        } 

        $remove = null;

        global $json;

        if (!isset($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;

        return self::execute($sql);
    }
}