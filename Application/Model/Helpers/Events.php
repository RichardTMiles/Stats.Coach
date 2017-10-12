<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 9/3/17
 * Time: 10:33 PM
 */

namespace Model\Helpers;


use Psr\Log\InvalidArgumentException;
use Tables\Followers;
use Tables\Messages;

class Events extends GlobalMap
{

    static function refresh(&$user, $id)
    {
        if (!is_object($user)) throw new InvalidArgumentException('Not logged in');

        Messages::all($user, $id);
        Followers::get($user, $id);
    }
}