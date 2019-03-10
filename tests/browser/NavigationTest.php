<?php
/**
 * Created by IntelliJ IDEA.
 * User: richardmiles
 * Date: 2/7/19
 * Time: 1:58 AM
 */

namespace App\tests\browser;


/** Selenium2TestCase
 * @link https://github.com/giorgiosironi/phpunit-selenium/blob/master/Tests/Selenium2TestCaseTest.php
 */
class NavigationTest extends \PHPUnit_Extensions_Selenium2TestCase
{
    public function setUp()
    {
        print PHP_EOL.'java -jar '. dirname(__DIR__) .'/selenium-server-standalone-3.141.59.jar' . PHP_EOL;
        self::shareSession(TRUE);
        $this->setHost('localhost');
        $this->setPort(4444);
        $this->setBrowser('chrome');
        $this->setBrowserUrl('http://localhost:80/');
        $this->prepareSession()->currentWindow()->maximize();
    }


    public function testBasicNavigation()
    {
        $this->url('/');

        $this->assertEquals(SITE_TITLE, $this->title());

        $register = $this->byId('register');

        $this->assertEquals('Register a new membership', $register->text());

        $link = $this->byLinkText('Register a new membership');

        $link->click();

        $this->moveto($register);

        $register->click();

        $this->assertStringEndsWith('register', $this->title());

    }

    public function testLoginFormExists()
    {
        $this->url( '/' );

        $this->timeouts()->implicitWait(10000);//10 seconds


        $user = $this->byName( 'username' )->value('Username');
        $pass = $this->byName( 'password' )->value('Password');

        $submit = $this->byName( 'signin' );

        // test that input above was a
        $this->assertEquals( 'Username', $user->value() );
        $this->assertEquals( 'Password', $pass->value() );
        $this->assertEquals( 'Sign In', $submit->value() );

    }

    public function testSubmitToSelf()
    {
        // set the url
        $this->url( '/' );

        // create a form object for reuse
        $form = $this->byId( 'loginForm' );

        // get the form action
        $action = $form->attribute( 'action' );

        // check the action value
        $this->assertEquals( 'http://localhost/login/', $action );

        // fill in the form field values
        $this->byName( 'username' )->value( 'admin' );

        $this->byName( 'password' )->value( 'adminadmin' );

        sleep(10);

        // submit the form
        $form->submit();

        sleep(10);


    }

}