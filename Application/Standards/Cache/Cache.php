<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 1/30/17
 * Time: 4:36 PM
 */

namespace Psr\Cache;

use Psr\Singleton;


class Cache implements CacheItemInterface
{
    use Singleton;

    public function getKey()
    {

    }

    public function get()
    {

    }

    public function isHit()
    {

    }

    public function set($value)
    {

    }

    public function expiresAt($expiration)
    {

    }

    public function expiresAfter($time)
    {

    }

    private function Cache()
    {

    }
}