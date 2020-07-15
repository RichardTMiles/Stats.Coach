<?php
/**
 * Created by IntelliJ IDEA.
 * User: richardmiles
 * Date: 2/7/19
 * Time: 1:58 AM
 */

namespace App\tests\browser;

use App\Tests\Config;
use PHPUnit\Extensions\Selenium2TestCase;

/** Selenium2TestCase
 * @Depends App\Tests\Feature\UserTest::class
 * @link https://github.com/giorgiosironi/phpunit-selenium/blob/master/Tests/Selenium2TestCaseTest.php
 * @link http://apigen.juzna.cz/doc/sebastianbergmann/phpunit-selenium/class-PHPUnit_Extensions_Selenium2TestCase.html
 */
class Selenium2Test extends Selenium2TestCase
{

    public function setUp(): void {
        print PHP_EOL . 'java -jar ' . COMPOSER . 'bin/selenium-server-standalone' . PHP_EOL;
        // self::shareSession(true);
        $this->setDesiredCapabilities([
            'chromeOptions' => [
                'w3c' => false
            ]
        ]);
        $this->setHost('localhost');
        $this->setPort(4444);
        $this->setBrowser('chrome');
        $this->setBrowserUrl(Config::URL);
        $this->prepareSession()->currentWindow()->maximize();
        $this->setSeleniumServerRequestsTimeout(10);

    }

    public function testSetupNavigationAndTitle(): void
    {
        $this->assertEquals(1, version_compare(Selenium2TestCase::VERSION, '8.0.0'));

        $this->url('/');

        $this->assertEquals(SITE_TITLE, $this->title());
    }

}
