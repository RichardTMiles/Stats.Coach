<?php
namespace Table;


use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Interfaces\iRest;

class golf_rounds extends Entities implements iRest
{
    const PRIMARY = "user_id";

    const COLUMNS = [
    'user_id','round_id','course_id','round_public','score','score_gnr','score_ffs','score_putts','score_out','score_in','score_total','score_total_gnr','score_total_ffs','score_total_putts','score_date',
    ];

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

        $sql = 'SELECT ' .  $sql . ' FROM statscoach.golf_rounds';

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
        $sql = 'INSERT INTO statscoach.golf_rounds (user_id, round_id, course_id, round_public, score, score_gnr, score_ffs, score_putts, score_out, score_in, score_total, score_total_gnr, score_total_ffs, score_total_putts, score_date) VALUES ( UNHEX(:user_id), :round_id, :course_id, :round_public, :score, :score_gnr, :score_ffs, :score_putts, :score_out, :score_in, :score_total, :score_total_gnr, :score_total_ffs, :score_total_putts, :score_date)';
        $stmt = Database::database()->prepare($sql);
            $user_id = $id = isset($argv['user_id']) ? $argv['user_id'] : self::new_entity('golf_rounds');
            $stmt->bindParam(':user_id',$user_id, \PDO::PARAM_STR, 16);
            
                $round_id = isset($argv['round_id']) ? $argv['round_id'] : null;
                $stmt->bindParam(':round_id',$round_id, \PDO::PARAM_STR, 16);
                    
                $course_id = isset($argv['course_id']) ? $argv['course_id'] : null;
                $stmt->bindParam(':course_id',$course_id, \PDO::PARAM_STR, 16);
                    
                $round_public = isset($argv['round_public']) ? $argv['round_public'] : null;
                $stmt->bindParam(':round_public',$round_public, \PDO::PARAM_STR, 1);
                    $stmt->bindValue(':score',isset($argv['score']) ? $argv['score'] : null, \PDO::PARAM_STR);
                    $stmt->bindValue(':score_gnr',isset($argv['score_gnr']) ? $argv['score_gnr'] : null, \PDO::PARAM_STR);
                    $stmt->bindValue(':score_ffs',isset($argv['score_ffs']) ? $argv['score_ffs'] : null, \PDO::PARAM_STR);
                    $stmt->bindValue(':score_putts',isset($argv['score_putts']) ? $argv['score_putts'] : null, \PDO::PARAM_STR);
                    
                $score_out = isset($argv['score_out']) ? $argv['score_out'] : null;
                $stmt->bindParam(':score_out',$score_out, \PDO::PARAM_STR, 2);
                    
                $score_in = isset($argv['score_in']) ? $argv['score_in'] : null;
                $stmt->bindParam(':score_in',$score_in, \PDO::PARAM_STR, 3);
                    
                $score_total = isset($argv['score_total']) ? $argv['score_total'] : null;
                $stmt->bindParam(':score_total',$score_total, \PDO::PARAM_STR, 3);
                    
                $score_total_gnr = isset($argv['score_total_gnr']) ? $argv['score_total_gnr'] : '0';
                $stmt->bindParam(':score_total_gnr',$score_total_gnr, \PDO::PARAM_STR, 11);
                    
                $score_total_ffs = isset($argv['score_total_ffs']) ? $argv['score_total_ffs'] : '0';
                $stmt->bindParam(':score_total_ffs',$score_total_ffs, \PDO::PARAM_STR, 3);
                    
                $score_total_putts = isset($argv['score_total_putts']) ? $argv['score_total_putts'] : null;
                $stmt->bindParam(':score_total_putts',$score_total_putts, \PDO::PARAM_STR, 11);
                    $stmt->bindValue(':score_date',isset($argv['score_date']) ? $argv['score_date'] : null, \PDO::PARAM_STR);
        
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

        $set = substr($set, 0, strlen($set)-1);

        $sql .= $set . ' WHERE ' . self::PRIMARY . "='$id'";

        $stmt = Database::database()->prepare($sql);

        if (isset($argv['user_id'])) {
            $user_id = 'UNHEX('.$argv['user_id'].')';
            $stmt->bindParam(':user_id', $user_id, \PDO::PARAM_STR, 16);
        }
        if (isset($argv['round_id'])) {
            $round_id = 'UNHEX('.$argv['round_id'].')';
            $stmt->bindParam(':round_id', $round_id, \PDO::PARAM_STR, 16);
        }
        if (isset($argv['course_id'])) {
            $course_id = 'UNHEX('.$argv['course_id'].')';
            $stmt->bindParam(':course_id', $course_id, \PDO::PARAM_STR, 16);
        }
        if (isset($argv['round_public'])) {
            $round_public = $argv['round_public'];
            $stmt->bindParam(':round_public',$round_public, \PDO::PARAM_STR, 1 );
        }
        if (isset($argv['score'])) {
            $stmt->bindValue(':score',$argv['score'], \PDO::PARAM_STR );
        }
        if (isset($argv['score_gnr'])) {
            $stmt->bindValue(':score_gnr',$argv['score_gnr'], \PDO::PARAM_STR );
        }
        if (isset($argv['score_ffs'])) {
            $stmt->bindValue(':score_ffs',$argv['score_ffs'], \PDO::PARAM_STR );
        }
        if (isset($argv['score_putts'])) {
            $stmt->bindValue(':score_putts',$argv['score_putts'], \PDO::PARAM_STR );
        }
        if (isset($argv['score_out'])) {
            $score_out = $argv['score_out'];
            $stmt->bindParam(':score_out',$score_out, \PDO::PARAM_STR, 2 );
        }
        if (isset($argv['score_in'])) {
            $score_in = $argv['score_in'];
            $stmt->bindParam(':score_in',$score_in, \PDO::PARAM_STR, 3 );
        }
        if (isset($argv['score_total'])) {
            $score_total = $argv['score_total'];
            $stmt->bindParam(':score_total',$score_total, \PDO::PARAM_STR, 3 );
        }
        if (isset($argv['score_total_gnr'])) {
            $score_total_gnr = $argv['score_total_gnr'];
            $stmt->bindParam(':score_total_gnr',$score_total_gnr, \PDO::PARAM_STR, 11 );
        }
        if (isset($argv['score_total_ffs'])) {
            $score_total_ffs = $argv['score_total_ffs'];
            $stmt->bindParam(':score_total_ffs',$score_total_ffs, \PDO::PARAM_STR, 3 );
        }
        if (isset($argv['score_total_putts'])) {
            $score_total_putts = $argv['score_total_putts'];
            $stmt->bindParam(':score_total_putts',$score_total_putts, \PDO::PARAM_STR, 11 );
        }
        if (isset($argv['score_date'])) {
            $stmt->bindValue(':score_date',$argv['score_date'], \PDO::PARAM_STR );
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