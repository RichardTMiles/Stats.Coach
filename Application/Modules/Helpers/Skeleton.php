<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 3/1/17
 * Time: 11:29 PM
 */

namespace Modules\Helpers;

use Psr\Singleton;

class Skeleton
{
    use Singleton;

    public function &__get($variable)
    {
        return $this->storage[$variable];
    }

    public function __set($variable, $value)
    {
        $this->storage[$variable] = $value;
    }

    public function __isset($variable)
    {
        return array_key_exists( $variable, $this->storage );
    }

    public function __unset($variable)
    {
        unset($this->storage[$variable]);
    }
}
