<?php
/**
 * Created by IntelliJ IDEA.
 * User: richardmiles
 * Date: 2/8/19
 * Time: 12:57 AM
 */

namespace App\tests\browser;

use PHPUnit\Framework\TestCase;


class webDriver extends TestCase
{
    static $desired_capabilities;
    protected $webDriver;
    protected $url = 'http://www.google.com';


    public static function getDriver($browser)
    {
        $host = 'http://localhost:4444/wd/hub';
        switch (strtoupper($browser)) {
            case 'CHROME':
                self::$desired_capabilities = DesiredCapabilities::chrome();
                break;
            case 'FIREFOX':
                self::$desired_capabilities = DesiredCapabilities::firefox();
                break;
            case 'IE':
                self::$desired_capabilities = DesiredCapabilities::internetExplorer();
                break;
            case 'OPERA':
                self::$desired_capabilities = DesiredCapabilities::opera();
                break;
            case 'EDGE':
                self::$desired_capabilities = DesiredCapabilities::microsoftEdge();
                break;
            case 'SAFARI':
                self::$desired_capabilities = DesiredCapabilities::safari();
                break;
            case 'PHANTOMJS':
                self::$desired_capabilities = DesiredCapabilities::phantomjs();
                break;
            default:
                self::$desired_capabilities = DesiredCapabilities::chrome();
                break;
        }
        return RemoteWebDriver::create($host, self::$desired_capabilities);
    }

    public function setUp()
    {
        global $argv;
        $this->webDriver = self::getDriver($argv[2]);
        $this->googlesearchpage = new GoogleSearchPage($this->webDriver);
        $this->searchresultspage = new SearchResultsPage($this->webDriver);
    }
    public function testGoogleHome()
    {
        $this->googlesearchpage->openURL();
        $this->assertEquals('Google', $this->googlesearchpage->title());
    }
    public function testGoogleSearch()
    {
        $this->googlesearchpage->openURL();
        $this->googlesearchpage->searchFor('Selenium');
        $this->assertTrue($this->searchresultspage->isSeleniumResultPresent(),'Selenium Result Found');
    }
    public function tearDown()
    {
        $this->webDriver->quit();
    }
}