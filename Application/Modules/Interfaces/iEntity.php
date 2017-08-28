<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 7/31/17
 * Time: 6:57 PM
 */

namespace Modules\Interfaces;


interface iEntity
{
    static function get($object, $id);

    static function add($object, $id, $argv);

    static function remove($object, $id);
}