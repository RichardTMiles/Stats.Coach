<?php
/**
 * Created by IntelliJ IDEA.
 * User: richardmiles
 * Date: 12/16/17
 * Time: 4:24 PM
 */

namespace Controller;

use CarbonPHP\Request;


class Search extends Request
{
    public function all($search){
        return $this->set($search)->alnum();
    }
}