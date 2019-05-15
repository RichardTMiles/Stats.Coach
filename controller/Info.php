<?php




namespace Controller;

use CarbonPHP\Controller;
use CarbonPHP\Error\PublicAlert;
use CarbonPHP\Request;
use Tables\carbon_locations;
use Tables\carbon_golf_courses as Course;

class Info extends Request  // Validation
{


    public function NewTournament()
    {
        global $json;
        $json['date'] = date('m/d/Y', filemtime(''));
        return null;
    }




}
