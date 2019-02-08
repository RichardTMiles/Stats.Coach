<?php
/**
 * Created by IntelliJ IDEA.
 * User: richardmiles
 * Date: 2/7/19
 * Time: 1:58 AM
 */

namespace App\Tests;


/** Selenium2TestCase
 */
class NavigationTest extends \PHPUnit_Extensions_Selenium2TestCase
{
    public function setUp()
    {
        print PHP_EOL.'java -jar '. __DIR__ .'/selenium-server-standalone-3.141.59.jar' . PHP_EOL;
        self::shareSession(TRUE);
        $this->setHost('localhost');
        $this->setPort(4444);
        $this->setBrowser('firefox');
        $this->setBrowserUrl('http://localhost:80/');
    }


    public function testBasicNavigation()
    {
        $this->url('/');
        $this->assertEquals(SITE_TITLE, $this->title());
    }

}