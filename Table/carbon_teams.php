<?php

namespace Table;

use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Error\PublicAlert;
use CarbonPHP\Interfaces\iRest;

class carbon_teams extends Entities implements iRest
{
    const COLUMNS = [
            'team_id',
            'team_coach',
            'parent_team',
            'team_code',
            'team_name',
            'team_rank',
            'team_sport',
            'team_division',
            'team_school',
            'team_district',
            'team_membership',
            'team_photo',
    ];

    const PRIMARY = "team_id";

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

        $sql = 'SELECT ' .  $get . ' FROM statscoach.carbon_teams';

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
        $sql = 'INSERT INTO statscoach.carbon_teams (team_id, team_coach, parent_team, team_code, team_name, team_rank, team_sport, team_division, team_school, team_district, team_membership, team_photo) VALUES (:team_id, :team_coach, :parent_team, :team_code, :team_name, :team_rank, :team_sport, :team_division, :team_school, :team_district, :team_membership, :team_photo)';
        $stmt = Database::database()->prepare($sql);
            $stmt->bindValue(':team_id', isset($argv['team_id']) ? $argv['team_id'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':team_coach', isset($argv['team_coach']) ? $argv['team_coach'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':parent_team', isset($argv['parent_team']) ? $argv['parent_team'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':team_code', isset($argv['team_code']) ? $argv['team_code'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':team_name', isset($argv['team_name']) ? $argv['team_name'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':team_rank', isset($argv['team_rank']) ? $argv['team_rank'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':team_sport', isset($argv['team_sport']) ? $argv['team_sport'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':team_division', isset($argv['team_division']) ? $argv['team_division'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':team_school', isset($argv['team_school']) ? $argv['team_school'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':team_district', isset($argv['team_district']) ? $argv['team_district'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':team_membership', isset($argv['team_membership']) ? $argv['team_membership'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':team_photo', isset($argv['team_photo']) ? $argv['team_photo'] : null, \PDO::PARAM_STR);
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

        $sql = 'UPDATE statscoach.carbon_teams ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';
        if (isset($argv['team_id'])) {
            $set .= 'team_id=:team_id,';
        }
        if (isset($argv['team_coach'])) {
            $set .= 'team_coach=:team_coach,';
        }
        if (isset($argv['parent_team'])) {
            $set .= 'parent_team=:parent_team,';
        }
        if (isset($argv['team_code'])) {
            $set .= 'team_code=:team_code,';
        }
        if (isset($argv['team_name'])) {
            $set .= 'team_name=:team_name,';
        }
        if (isset($argv['team_rank'])) {
            $set .= 'team_rank=:team_rank,';
        }
        if (isset($argv['team_sport'])) {
            $set .= 'team_sport=:team_sport,';
        }
        if (isset($argv['team_division'])) {
            $set .= 'team_division=:team_division,';
        }
        if (isset($argv['team_school'])) {
            $set .= 'team_school=:team_school,';
        }
        if (isset($argv['team_district'])) {
            $set .= 'team_district=:team_district,';
        }
        if (isset($argv['team_membership'])) {
            $set .= 'team_membership=:team_membership,';
        }
        if (isset($argv['team_photo'])) {
            $set .= 'team_photo=:team_photo,';
        }

        if (empty($set)){
            return false;
        }

        $set = substr($set, 0, strlen($set)-1);

        $sql .= $set . ' WHERE ' . self::PRIMARY . "='$id'";

        $stmt = Database::database()->prepare($sql);

        if (isset($argv['team_id'])) {
            $stmt->bindValue(':team_id', $argv['team_id'], \PDO::PARAM_STR);
        }
        if (isset($argv['team_coach'])) {
            $stmt->bindValue(':team_coach', $argv['team_coach'], \PDO::PARAM_STR);
        }
        if (isset($argv['parent_team'])) {
            $stmt->bindValue(':parent_team', $argv['parent_team'], \PDO::PARAM_STR);
        }
        if (isset($argv['team_code'])) {
            $stmt->bindValue(':team_code', $argv['team_code'], \PDO::PARAM_STR);
        }
        if (isset($argv['team_name'])) {
            $stmt->bindValue(':team_name', $argv['team_name'], \PDO::PARAM_STR);
        }
        if (isset($argv['team_rank'])) {
            $stmt->bindValue(':team_rank', $argv['team_rank'], \PDO::PARAM_STR);
        }
        if (isset($argv['team_sport'])) {
            $stmt->bindValue(':team_sport', $argv['team_sport'], \PDO::PARAM_STR);
        }
        if (isset($argv['team_division'])) {
            $stmt->bindValue(':team_division', $argv['team_division'], \PDO::PARAM_STR);
        }
        if (isset($argv['team_school'])) {
            $stmt->bindValue(':team_school', $argv['team_school'], \PDO::PARAM_STR);
        }
        if (isset($argv['team_district'])) {
            $stmt->bindValue(':team_district', $argv['team_district'], \PDO::PARAM_STR);
        }
        if (isset($argv['team_membership'])) {
            $stmt->bindValue(':team_membership', $argv['team_membership'], \PDO::PARAM_STR);
        }
        if (isset($argv['team_photo'])) {
            $stmt->bindValue(':team_photo', $argv['team_photo'], \PDO::PARAM_STR);
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
        $sql = 'DELETE FROM statscoach.carbon_teams ';

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