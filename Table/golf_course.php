<?php

namespace Table;

use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Error\PublicAlert;
use CarbonPHP\Interfaces\iRest;

class golf_course extends Entities implements iRest
{
    const COLUMNS = [
            'course_id',
            'course_name',
            'course_holes',
            'course_phone',
            'course_difficulty',
            'course_rank',
            'box_color_1',
            'box_color_2',
            'box_color_3',
            'box_color_4',
            'box_color_5',
            'course_par',
            'course_par_out',
            'course_par_in',
            'par_tot',
            'course_par_hcp',
            'course_type',
            'course_access',
            'course_handicap',
            'pga_professional',
            'website',
    ];

    const PRIMARY = "course_id";

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

        $sql = 'SELECT ' .  $get . ' FROM statscoach.golf_course';

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
        $sql = 'INSERT INTO statscoach.golf_course (course_id, course_name, course_holes, course_phone, course_difficulty, course_rank, box_color_1, box_color_2, box_color_3, box_color_4, box_color_5, course_par, course_par_out, course_par_in, par_tot, course_par_hcp, course_type, course_access, course_handicap, pga_professional, website) VALUES (:course_id, :course_name, :course_holes, :course_phone, :course_difficulty, :course_rank, :box_color_1, :box_color_2, :box_color_3, :box_color_4, :box_color_5, :course_par, :course_par_out, :course_par_in, :par_tot, :course_par_hcp, :course_type, :course_access, :course_handicap, :pga_professional, :website)';
        $stmt = Database::database()->prepare($sql);
            $stmt->bindValue(':course_id', isset($argv['course_id']) ? $argv['course_id'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':course_name', isset($argv['course_name']) ? $argv['course_name'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':course_holes', isset($argv['course_holes']) ? $argv['course_holes'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':course_phone', isset($argv['course_phone']) ? $argv['course_phone'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':course_difficulty', isset($argv['course_difficulty']) ? $argv['course_difficulty'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':course_rank', isset($argv['course_rank']) ? $argv['course_rank'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':box_color_1', isset($argv['box_color_1']) ? $argv['box_color_1'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':box_color_2', isset($argv['box_color_2']) ? $argv['box_color_2'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':box_color_3', isset($argv['box_color_3']) ? $argv['box_color_3'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':box_color_4', isset($argv['box_color_4']) ? $argv['box_color_4'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':box_color_5', isset($argv['box_color_5']) ? $argv['box_color_5'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':course_par', isset($argv['course_par']) ? $argv['course_par'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':course_par_out', isset($argv['course_par_out']) ? $argv['course_par_out'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':course_par_in', isset($argv['course_par_in']) ? $argv['course_par_in'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':par_tot', isset($argv['par_tot']) ? $argv['par_tot'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':course_par_hcp', isset($argv['course_par_hcp']) ? $argv['course_par_hcp'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':course_type', isset($argv['course_type']) ? $argv['course_type'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':course_access', isset($argv['course_access']) ? $argv['course_access'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':course_handicap', isset($argv['course_handicap']) ? $argv['course_handicap'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':pga_professional', isset($argv['pga_professional']) ? $argv['pga_professional'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':website', isset($argv['website']) ? $argv['website'] : null, \PDO::PARAM_STR);
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

        $sql = 'UPDATE statscoach.golf_course ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';
        if (isset($argv['course_id'])) {
            $set .= 'course_id=:course_id,';
        }
        if (isset($argv['course_name'])) {
            $set .= 'course_name=:course_name,';
        }
        if (isset($argv['course_holes'])) {
            $set .= 'course_holes=:course_holes,';
        }
        if (isset($argv['course_phone'])) {
            $set .= 'course_phone=:course_phone,';
        }
        if (isset($argv['course_difficulty'])) {
            $set .= 'course_difficulty=:course_difficulty,';
        }
        if (isset($argv['course_rank'])) {
            $set .= 'course_rank=:course_rank,';
        }
        if (isset($argv['box_color_1'])) {
            $set .= 'box_color_1=:box_color_1,';
        }
        if (isset($argv['box_color_2'])) {
            $set .= 'box_color_2=:box_color_2,';
        }
        if (isset($argv['box_color_3'])) {
            $set .= 'box_color_3=:box_color_3,';
        }
        if (isset($argv['box_color_4'])) {
            $set .= 'box_color_4=:box_color_4,';
        }
        if (isset($argv['box_color_5'])) {
            $set .= 'box_color_5=:box_color_5,';
        }
        if (isset($argv['course_par'])) {
            $set .= 'course_par=:course_par,';
        }
        if (isset($argv['course_par_out'])) {
            $set .= 'course_par_out=:course_par_out,';
        }
        if (isset($argv['course_par_in'])) {
            $set .= 'course_par_in=:course_par_in,';
        }
        if (isset($argv['par_tot'])) {
            $set .= 'par_tot=:par_tot,';
        }
        if (isset($argv['course_par_hcp'])) {
            $set .= 'course_par_hcp=:course_par_hcp,';
        }
        if (isset($argv['course_type'])) {
            $set .= 'course_type=:course_type,';
        }
        if (isset($argv['course_access'])) {
            $set .= 'course_access=:course_access,';
        }
        if (isset($argv['course_handicap'])) {
            $set .= 'course_handicap=:course_handicap,';
        }
        if (isset($argv['pga_professional'])) {
            $set .= 'pga_professional=:pga_professional,';
        }
        if (isset($argv['website'])) {
            $set .= 'website=:website,';
        }

        if (empty($set)){
            return false;
        }

        $set = substr($set, 0, strlen($set)-1);

        $sql .= $set . ' WHERE ' . self::PRIMARY . "='$id'";

        $stmt = Database::database()->prepare($sql);

        if (isset($argv['course_id'])) {
            $stmt->bindValue(':course_id', $argv['course_id'], \PDO::PARAM_STR);
        }
        if (isset($argv['course_name'])) {
            $stmt->bindValue(':course_name', $argv['course_name'], \PDO::PARAM_STR);
        }
        if (isset($argv['course_holes'])) {
            $stmt->bindValue(':course_holes', $argv['course_holes'], \PDO::PARAM_STR);
        }
        if (isset($argv['course_phone'])) {
            $stmt->bindValue(':course_phone', $argv['course_phone'], \PDO::PARAM_STR);
        }
        if (isset($argv['course_difficulty'])) {
            $stmt->bindValue(':course_difficulty', $argv['course_difficulty'], \PDO::PARAM_STR);
        }
        if (isset($argv['course_rank'])) {
            $stmt->bindValue(':course_rank', $argv['course_rank'], \PDO::PARAM_STR);
        }
        if (isset($argv['box_color_1'])) {
            $stmt->bindValue(':box_color_1', $argv['box_color_1'], \PDO::PARAM_STR);
        }
        if (isset($argv['box_color_2'])) {
            $stmt->bindValue(':box_color_2', $argv['box_color_2'], \PDO::PARAM_STR);
        }
        if (isset($argv['box_color_3'])) {
            $stmt->bindValue(':box_color_3', $argv['box_color_3'], \PDO::PARAM_STR);
        }
        if (isset($argv['box_color_4'])) {
            $stmt->bindValue(':box_color_4', $argv['box_color_4'], \PDO::PARAM_STR);
        }
        if (isset($argv['box_color_5'])) {
            $stmt->bindValue(':box_color_5', $argv['box_color_5'], \PDO::PARAM_STR);
        }
        if (isset($argv['course_par'])) {
            $stmt->bindValue(':course_par', $argv['course_par'], \PDO::PARAM_STR);
        }
        if (isset($argv['course_par_out'])) {
            $stmt->bindValue(':course_par_out', $argv['course_par_out'], \PDO::PARAM_STR);
        }
        if (isset($argv['course_par_in'])) {
            $stmt->bindValue(':course_par_in', $argv['course_par_in'], \PDO::PARAM_STR);
        }
        if (isset($argv['par_tot'])) {
            $stmt->bindValue(':par_tot', $argv['par_tot'], \PDO::PARAM_STR);
        }
        if (isset($argv['course_par_hcp'])) {
            $stmt->bindValue(':course_par_hcp', $argv['course_par_hcp'], \PDO::PARAM_STR);
        }
        if (isset($argv['course_type'])) {
            $stmt->bindValue(':course_type', $argv['course_type'], \PDO::PARAM_STR);
        }
        if (isset($argv['course_access'])) {
            $stmt->bindValue(':course_access', $argv['course_access'], \PDO::PARAM_STR);
        }
        if (isset($argv['course_handicap'])) {
            $stmt->bindValue(':course_handicap', $argv['course_handicap'], \PDO::PARAM_STR);
        }
        if (isset($argv['pga_professional'])) {
            $stmt->bindValue(':pga_professional', $argv['pga_professional'], \PDO::PARAM_STR);
        }
        if (isset($argv['website'])) {
            $stmt->bindValue(':website', $argv['website'], \PDO::PARAM_STR);
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
        $sql = 'DELETE FROM statscoach.golf_course ';

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