<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 6/26/17
 * Time: 8:47 PM
 */

namespace Model\Helpers;


abstract class QuickFetch
{
    public function fetch_into_current_class($array)
    {
        $object = get_object_vars( $this );
        foreach ($array as $key => $value)
            if (array_key_exists( $key, $object ))
                $this->$key = $value;
    }
}