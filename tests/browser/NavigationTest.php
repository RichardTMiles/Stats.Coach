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
        static $count;
        $count or $count=1 and print PHP_EOL.'java -jar '. dirname(__DIR__) .'/selenium-server-standalone-3.141.59.jar' . PHP_EOL;
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

        $link->click(); // we must grab all new elements now

        $this->timeouts()->implicitWait(10000);//10 seconds

    }

    public function testLoginFormExists()
    {
        $this->url( '/' );

        $this->timeouts()->implicitWait(10000);//10 seconds

        $user = $this->byName( 'username' )->value('Username');
        $pass = $this->byName( 'password' )->value('Password');
        $submit = $this->byName( 'signin' )->value('Sign In');

        // test that input above was a
        $this->assertEquals( 'Username', $user->value() );
        $this->assertEquals( 'Password', $pass->value() );
        $this->assertEquals( 'Sign In', $submit->value() );

    }


    public function testRegister() {
        $this->url('/');

        $this->assertEquals(SITE_TITLE, $this->title());

        $link = $this->byLinkText('Register a new membership');

        $link->click(); // we must grab all new elements now

        $this->timeouts()->implicitWait(2000);//10 seconds

        $register = $this->byName('submit');

        # $register = $form->attribute( 'action' ); // another way to do it
        # $register->submit();

        $this->timeouts()->implicitWait(5000);//10 seconds

        $this->byName( 'firstname' )->value( 'Richard' );
        $this->byName( 'lastname' )->value( 'Miles' );
        $this->byName( 'email' )->value( 'Richard@Miles.Systems' );
        $this->byName( 'username' )->value( 'admin' );
        $this->byName( 'password' )->value( 'adminadmin' );
        $this->byName( 'password2' )->value( 'adminadmin' );

        $this->select($this->byName('gender'))->selectOptionByValue('male');

        $this->timeouts()->implicitWait(3000);//10 seconds

        $this->byClassName('icheckbox_square-blue')->click();

        $this->timeouts()->implicitWait(3000);//10 seconds

        $register->click();

        sleep(10);

    }

    public function testLogin()
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

        sleep(2);

        // submit the form
        $form->submit();


        sleep(3);


    }

    public function testPostScores()
    {

        $this->testLogin();

        $this->byId('postScoreHeader')->click();

       // $this->byId('select2-uzdm-container')->click();

        sleep(5);


        $this->select($this->byClassName('select2-hidden-accessible'))->selectOptionByValue('Alaska');

        sleep(2);

        $this->select($this->byId('course'))->selectOptionByValue('Add');


        sleep(10);

        $this->byName( 'c_name' )->value( 'Lake Park' );
        sleep(2);
        $this->select($this->byId('course_type'))->selectOptionByValue('Semi-private');
        $this->select($this->byId('course_play'))->selectOptionByValue('9');

        $this->byId( 'phone' )->value( '2145551234' );
        $this->byName( 'c_street' )->value( '6 Lake Park Rd, TX 75057' );
        $this->byName( 'c_city' )->value( 'Lewisville' );
        $this->select($this->byId('state'))->selectOptionByValue('California');
        $this->select($this->byName('tee_boxes'))->selectOptionByValue('3');
        $this->select($this->byName('Handicap_number'))->selectOptionByValue('2');
        $this->byId('next')->click();
        sleep(3);

        $this->select($this->byName('general_difficulty'))->ByValue('13.2');
        //$this->select($this->byClassName('knob'))->selectOptionByValue('13.2');

        sleep(10);




    }


    public function testCreateTeam()
    {
        $this->testLogin();
        $this->byId('navMenu')->click();
        $this->byId('createTeamLink')->click();
        $this->byName('teamName')->value('Ateam');
        $this->byName('schoolName')->value('southlake');
        sleep(3);
        $this->byId('teamSubmit')->click();
        sleep(10);
    }

    public function testDeleteAccount()
    {
        $this->testLogin();
        $this->byId('navUserTopRightUserImage')->click();
        $this->byId('navTopRightUserDropdownProfile')->click();
        sleep(1);
        $this->byId('profileDeleteButton')->click();
        sleep(1);
        $this->byId('confirmDeleteButton')->click();
        sleep(10);
    }

}