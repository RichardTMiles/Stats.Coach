<?php
namespace Table;


use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Interfaces\iRest;

class golf_rounds extends Entities implements iRest
{
    const PRIMARY = [
    
    ];

    const COLUMNS = [
    'user_id','round_id','course_id','round_public','score','score_gnr','score_ffs','score_putts','score_out','score_in','score_total','score_total_gnr','score_total_ffs','score_total_putts','score_date',
    ];

    const VALIDATION = [];

    const BINARY = [
    'user_id','round_id','course_id',
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

        $sql = 'SELECT ' .  $sql . ' FROM statscoach.golf_rounds';

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
        $sql = 'INSERT INTO statscoach.golf_rounds (user_id, round_id, course_id, round_public, score, score_gnr, score_ffs, score_putts, score_out, score_in, score_total, score_total_gnr, score_total_ffs, score_total_putts, score_date) VALUES ( UNHEX(:user_id), UNHEX(:round_id), UNHEX(:course_id), :round_public, :score, :score_gnr, :score_ffs, :score_putts, :score_out, :score_in, :score_total, :score_total_gnr, :score_total_ffs, :score_total_putts, :score_date)';
        $stmt = Database::database()->prepare($sql);

        global $json;

        if (!isset($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;

            
                $user_id = $argv['user_id'];
                $stmt->bindParam(':user_id',$user_id, 2, 16);
                    
                $round_id = $argv['round_id'];
                $stmt->bindParam(':round_id',$round_id, 2, 16);
                    
                $course_id = $argv['course_id'];
                $stmt->bindParam(':course_id',$course_id, 2, 16);
                    
                $round_public = isset($argv['round_public']) ? $argv['round_public'] : '1';
                $stmt->bindParam(':round_public',$round_public, 2, 1);
                    $stmt->bindValue(':score',$argv['score'], 2);
                    $stmt->bindValue(':score_gnr',$argv['score_gnr'], 2);
                    $stmt->bindValue(':score_ffs',$argv['score_ffs'], 2);
                    $stmt->bindValue(':score_putts',$argv['score_putts'], 2);
                    
                $score_out = $argv['score_out'];
                $stmt->bindParam(':score_out',$score_out, 2, 2);
                    
                $score_in = $argv['score_in'];
                $stmt->bindParam(':score_in',$score_in, 2, 3);
                    
                $score_total = $argv['score_total'];
                $stmt->bindParam(':score_total',$score_total, 2, 3);
                    
                $score_total_gnr = isset($argv['score_total_gnr']) ? $argv['score_total_gnr'] : '0';
                $stmt->bindParam(':score_total_gnr',$score_total_gnr, 2, 11);
                    
                $score_total_ffs = isset($argv['score_total_ffs']) ? $argv['score_total_ffs'] : '0';
                $stmt->bindParam(':score_total_ffs',$score_total_ffs, 2, 3);
                    
                $score_total_putts = isset($argv['score_total_putts']) ? $argv['score_total_putts'] : null;
                $stmt->bindParam(':score_total_putts',$score_total_putts, 2, 11);
                    $stmt->bindValue(':score_date',$argv['score_date'], 2);
        

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
        foreach ($argv as $key => $value) {
            if (!in_array($key, self::COLUMNS)){
                unset($argv[$key]);
            }
        }

        $sql = 'UPDATE statscoach.golf_rounds ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

        if (isset($argv['user_id'])) {
            $set .= 'user_id=UNHEX(:user_id),';
        }
        if (isset($argv['round_id'])) {
            $set .= 'round_id=UNHEX(:round_id),';
        }
        if (isset($argv['course_id'])) {
            $set .= 'course_id=UNHEX(:course_id),';
        }
        if (isset($argv['round_public'])) {
            $set .= 'round_public=:round_public,';
        }
        if (isset($argv['score'])) {
            $set .= 'score=:score,';
        }
        if (isset($argv['score_gnr'])) {
            $set .= 'score_gnr=:score_gnr,';
        }
        if (isset($argv['score_ffs'])) {
            $set .= 'score_ffs=:score_ffs,';
        }
        if (isset($argv['score_putts'])) {
            $set .= 'score_putts=:score_putts,';
        }
        if (isset($argv['score_out'])) {
            $set .= 'score_out=:score_out,';
        }
        if (isset($argv['score_in'])) {
            $set .= 'score_in=:score_in,';
        }
        if (isset($argv['score_total'])) {
            $set .= 'score_total=:score_total,';
        }
        if (isset($argv['score_total_gnr'])) {
            $set .= 'score_total_gnr=:score_total_gnr,';
        }
        if (isset($argv['score_total_ffs'])) {
            $set .= 'score_total_ffs=:score_total_ffs,';
        }
        if (isset($argv['score_total_putts'])) {
            $set .= 'score_total_putts=:score_total_putts,';
        }
        if (isset($argv['score_date'])) {
            $set .= 'score_date=:score_date,';
        }

        if (empty($set)){
            return false;
        }

        $sql .= substr($set, 0, strlen($set)-1);

        $db = Database::database();

        

        $stmt = $db->prepare($sql);

        global $json;

        if (!isset($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;


        if (isset($argv['user_id'])) {
            $user_id = 'UNHEX('.$argv['user_id'].')';
            $stmt->bindParam(':user_id', $user_id, 2, 16);
        }
        if (isset($argv['round_id'])) {
            $round_id = 'UNHEX('.$argv['round_id'].')';
            $stmt->bindParam(':round_id', $round_id, 2, 16);
        }
        if (isset($argv['course_id'])) {
            $course_id = 'UNHEX('.$argv['course_id'].')';
            $stmt->bindParam(':course_id', $course_id, 2, 16);
        }
        if (isset($argv['round_public'])) {
            $round_public = $argv['round_public'];
            $stmt->bindParam(':round_public',$round_public, 2, 1);
        }
        if (isset($argv['score'])) {
            $stmt->bindValue(':score',$argv['score'], 2);
        }
        if (isset($argv['score_gnr'])) {
            $stmt->bindValue(':score_gnr',$argv['score_gnr'], 2);
        }
        if (isset($argv['score_ffs'])) {
            $stmt->bindValue(':score_ffs',$argv['score_ffs'], 2);
        }
        if (isset($argv['score_putts'])) {
            $stmt->bindValue(':score_putts',$argv['score_putts'], 2);
        }
        if (isset($argv['score_out'])) {
            $score_out = $argv['score_out'];
            $stmt->bindParam(':score_out',$score_out, 2, 2);
        }
        if (isset($argv['score_in'])) {
            $score_in = $argv['score_in'];
            $stmt->bindParam(':score_in',$score_in, 2, 3);
        }
        if (isset($argv['score_total'])) {
            $score_total = $argv['score_total'];
            $stmt->bindParam(':score_total',$score_total, 2, 3);
        }
        if (isset($argv['score_total_gnr'])) {
            $score_total_gnr = $argv['score_total_gnr'];
            $stmt->bindParam(':score_total_gnr',$score_total_gnr, 2, 11);
        }
        if (isset($argv['score_total_ffs'])) {
            $score_total_ffs = $argv['score_total_ffs'];
            $stmt->bindParam(':score_total_ffs',$score_total_ffs, 2, 3);
        }
        if (isset($argv['score_total_putts'])) {
            $score_total_putts = $argv['score_total_putts'];
            $stmt->bindParam(':score_total_putts',$score_total_putts, 2, 11);
        }
        if (isset($argv['score_date'])) {
            $stmt->bindValue(':score_date',$argv['score_date'], 2);
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
        $sql = 'DELETE FROM statscoach.golf_rounds ';

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
            $sql .= ' WHERE ';
            foreach ($argv as $column => $value) {
                if (in_array($column, self::BINARY)) {
                    $sql .= " $column =UNHEX(" . Database::database()->quote($value) . ') AND ';
                } else {
                    $sql .= " $column =" . Database::database()->quote($value) . ' AND ';
                }
            }
            $sql = substr($sql, 0, strlen($sql)-4);
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