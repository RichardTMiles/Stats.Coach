<?php




namespace Controller;

use CarbonPHP\Request;

class Info extends Request  // Validation
{
    public function NewTournament()
    {
        global $json;
        $json['date'] = date('m/d/Y', filemtime(''));
        return null;
    }
}
