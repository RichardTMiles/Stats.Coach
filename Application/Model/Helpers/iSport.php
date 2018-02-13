<?php
/**
 * Created by IntelliJ IDEA.
 * User: richardmiles
 * Date: 2/13/18
 * Time: 12:18 AM
 */

/*
The user relay runs through out Database connection code, no PDO is actually run here
Do not use try catch, as it is not needed

Email needs to be edited in function "register"
*/

namespace Model\Helpers;

use Carbon\Interfaces\iSingleton;

interface iSport extends iSingleton
{
    public function stats(&$object ,$id);
}











