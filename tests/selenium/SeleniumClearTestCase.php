<?php
/**
 * Created by IntelliJ IDEA.
 * User: richardmiles
 * Date: 2/9/19
 * Time: 7:22 PM
 */

namespace App\Tests\Selenium;


class SeleniumClearTestCase extends MigrationToSelenium2
{
    protected $baseUrl = 'http://yourservice.dev';

    protected function setUp()
    {
        $screenshots_dir = __DIR__.'/screenshots';
        if (! file_exists($screenshots_dir)) {
            mkdir($screenshots_dir, 0777, true);
        }
        $this->listener = new PHPUnit_Extensions_Selenium2TestCase_ScreenshotListener($screenshots_dir);

        $this->setBrowser('firefox');
        $this->setBrowserUrl($this->baseUrl);
        $this->createApplication(); // bootstrap laravel app
    }

    public function onNotSuccessfulTest($e)
    {
        $this->listener->addError($this, $e, null);
        parent::onNotSuccessfulTest($e);
    }

    /**
     * Wykonaj screenshot w danym mommencie.
     * @return
     */
    public function screenshot()
    {
        $this->listener->addError($this, new Exception, null); // ta funkcja troche myli nazwą, ale wykona ona tylko screenshota nic ponadto
    }

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

}