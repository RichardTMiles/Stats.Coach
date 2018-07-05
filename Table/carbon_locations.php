<?php
namespace Table;


use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Interfaces\iRest;

class carbon_locations extends Entities implements iRest
{
    const PRIMARY = "entity_id";

    const COLUMNS = [
    'entity_id','latitude','longitude','street','city','state','elevation',
    ];

    const BINARY = [
    'entity_id',
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

        $sql = 'SELECT ' .  $sql . ' FROM statscoach.carbon_locations';

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
        $sql = 'INSERT INTO statscoach.carbon_locations (entity_id, latitude, longitude, street, city, state, elevation) VALUES ( UNHEX(:entity_id), :latitude, :longitude, :street, :city, :state, :elevation)';
        $stmt = Database::database()->prepare($sql);
            $entity_id = $id = isset($argv['entity_id']) ? $argv['entity_id'] : self::new_entity('carbon_locations');
            $stmt->bindParam(':entity_id',$entity_id, \PDO::PARAM_STR, 16);
            
                $latitude = isset($argv['latitude']) ? $argv['latitude'] : null;
                $stmt->bindParam(':latitude',$latitude, \PDO::PARAM_STR, 225);
                    
                $longitude = isset($argv['longitude']) ? $argv['longitude'] : null;
                $stmt->bindParam(':longitude',$longitude, \PDO::PARAM_STR, 225);
                    $stmt->bindValue(':street',isset($argv['street']) ? $argv['street'] : null, \PDO::PARAM_STR);
                    
                $city = isset($argv['city']) ? $argv['city'] : null;
                $stmt->bindParam(':city',$city, \PDO::PARAM_STR, 40);
                    
                $state = isset($argv['state']) ? $argv['state'] : null;
                $stmt->bindParam(':state',$state, \PDO::PARAM_STR, 10);
                    
                $elevation = isset($argv['elevation']) ? $argv['elevation'] : null;
                $stmt->bindParam(':elevation',$elevation, \PDO::PARAM_STR, 40);
        
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

        $sql = 'UPDATE statscoach.carbon_locations ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

        if (isset($argv['entity_id'])) {
            $set .= 'entity_id=UNHEX(:entity_id),';
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
            $entity_id = 'UNHEX('.$argv['entity_id'].')';
            $stmt->bindParam(':entity_id', $entity_id, \PDO::PARAM_STR, 16);
        }
        if (isset($argv['latitude'])) {
            $latitude = $argv['latitude'];
            $stmt->bindParam(':latitude',$latitude, \PDO::PARAM_STR, 225 );
        }
        if (isset($argv['longitude'])) {
            $longitude = $argv['longitude'];
            $stmt->bindParam(':longitude',$longitude, \PDO::PARAM_STR, 225 );
        }
        if (isset($argv['street'])) {
            $stmt->bindValue(':street',$argv['street'], \PDO::PARAM_STR );
        }
        if (isset($argv['city'])) {
            $city = $argv['city'];
            $stmt->bindParam(':city',$city, \PDO::PARAM_STR, 40 );
        }
        if (isset($argv['state'])) {
            $state = $argv['state'];
            $stmt->bindParam(':state',$state, \PDO::PARAM_STR, 10 );
        }
        if (isset($argv['elevation'])) {
            $elevation = $argv['elevation'];
            $stmt->bindParam(':elevation',$elevation, \PDO::PARAM_STR, 40 );
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