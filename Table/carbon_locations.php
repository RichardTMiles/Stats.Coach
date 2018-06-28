<?php

namespace Table;

use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Error\PublicAlert;
use CarbonPHP\Interfaces\iRest;

class carbon_locations extends Entities implements iRest
{
    const COLUMNS = [
            'entity_id',
            'latitude',
            'longitude',
            'street',
            'city',
            'state',
            'elevation',
    ];

    const PRIMARY = "entity_id";

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

        $sql = 'SELECT ' .  $get . ' FROM statscoach.carbon_locations';

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
        $sql = 'INSERT INTO statscoach.carbon_locations (entity_id, latitude, longitude, street, city, state, elevation) VALUES (:entity_id, :latitude, :longitude, :street, :city, :state, :elevation)';
        $stmt = Database::database()->prepare($sql);
            $stmt->bindValue(':entity_id', isset($argv['entity_id']) ? $argv['entity_id'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':latitude', isset($argv['latitude']) ? $argv['latitude'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':longitude', isset($argv['longitude']) ? $argv['longitude'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':street', isset($argv['street']) ? $argv['street'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':city', isset($argv['city']) ? $argv['city'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':state', isset($argv['state']) ? $argv['state'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':elevation', isset($argv['elevation']) ? $argv['elevation'] : null, \PDO::PARAM_STR);
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

        $sql = 'UPDATE statscoach.carbon_locations ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';
        if (isset($argv['entity_id'])) {
            $set .= 'entity_id=:entity_id,';
        }
        if (isset($argv['latitude'])) {
            $set .= 'latitude=:latitude,';
        }
        if (isset($argv['longitude'])) {
            $set .= 'longitude=:longitude,';
        }
        if (isset($argv['street'])) {
            $set .= 'street=:street,';
        }
        if (isset($argv['city'])) {
            $set .= 'city=:city,';
        }
        if (isset($argv['state'])) {
            $set .= 'state=:state,';
        }
        if (isset($argv['elevation'])) {
            $set .= 'elevation=:elevation,';
        }

        if (empty($set)){
            return false;
        }

        $set = substr($set, 0, strlen($set)-1);

        $sql .= $set . ' WHERE ' . self::PRIMARY . "='$id'";

        $stmt = Database::database()->prepare($sql);

        if (isset($argv['entity_id'])) {
            $stmt->bindValue(':entity_id', $argv['entity_id'], \PDO::PARAM_STR);
        }
        if (isset($argv['latitude'])) {
            $stmt->bindValue(':latitude', $argv['latitude'], \PDO::PARAM_STR);
        }
        if (isset($argv['longitude'])) {
            $stmt->bindValue(':longitude', $argv['longitude'], \PDO::PARAM_STR);
        }
        if (isset($argv['street'])) {
            $stmt->bindValue(':street', $argv['street'], \PDO::PARAM_STR);
        }
        if (isset($argv['city'])) {
            $stmt->bindValue(':city', $argv['city'], \PDO::PARAM_STR);
        }
        if (isset($argv['state'])) {
            $stmt->bindValue(':state', $argv['state'], \PDO::PARAM_STR);
        }
        if (isset($argv['elevation'])) {
            $stmt->bindValue(':elevation', $argv['elevation'], \PDO::PARAM_STR);
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
        $sql = 'DELETE FROM statscoach.carbon_locations ';

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