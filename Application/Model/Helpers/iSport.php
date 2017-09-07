<?php

/*
The user relay runs through out Database connection code, no PDO is actually run here
Do not use try catch, as it is not needed

Email needs to be edited in function "register"
*/

namespace Model\Helpers;

use Modules\Interfaces\iSingleton;

interface iSport extends iSingleton
{
    public function stats($object ,$id);
}












