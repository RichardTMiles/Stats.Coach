<?php
/**
 * Created by IntelliJ IDEA.
 * User: richardmiles
 * Date: 2/7/19
 * Time: 1:58 AM
 */

namespace App\tests\browser;

use App\Tests\Config;
use App\Tests\Feature\UserTest;
use Tables\Carbon_Users as Users;

/** Selenium2TestCase
 * @Depends App\Tests\Feature\UserTest::class
 * @link https://github.com/giorgiosironi/phpunit-selenium/blob/master/Tests/Selenium2TestCaseTest.php
 * @link http://apigen.juzna.cz/doc/sebastianbergmann/phpunit-selenium/class-PHPUnit_Extensions_Selenium2TestCase.html
 */
class NavigationTest extends Selenium2Test
{
    public const URL = Config::URL;
    public const ADMIN_USERNAME = Config::ADMIN_USERNAME;
    public const ADMIN_PASSWORD = Config::ADMIN_PASSWORD;

    public function setUp() : void
    {
        parent::setUp();

        static $count = 0;

        if (!$count) {
            $count++;
            try {
                $user = [];

                Users::Get($user, null, [
                    'where' => [
                        Users::USER_USERNAME => self::ADMIN_USERNAME
                    ]
                ]);

                $user = $user[0] ?? null;

                $user and $this->assertTrue(
                    Users::Delete($user, $user['user_id'], [])
                );
            } catch (\Throwable $e) {
                // do nothing
            }
        }

        self::shareSession(true);
    }

    public function testBasicNavigation(): void
    {
        $this->url('/');

        $this->assertEquals(SITE_TITLE, $this->title());

        $this->timeouts()->implicitWait(10000);//10 seconds

        $register = $this->byId('register');

        $this->assertEquals('Register a new membership', $register->text());

        $link = $this->byLinkText('Register a new membership');

        $link->click(); // we must grab all new elements now

        $this->timeouts()->implicitWait(10000);//10 seconds

    }

    /**
     * @depends testBasicNavigation
     */
    public function testLoginFormExists(): void
    {
        $this->url('/');

        $this->timeouts()->implicitWait(10000);//10 seconds

        $user = $this->byName('username');
        $user->value('Test Username');

        $pass = $this->byName('password');
        $pass->value('Test Password');

        //$submit = $this->byName( 'signin' )->value('Sign In');
        // test that input above was a
        $this->assertEquals('Test Username', $user->value());
        $this->assertEquals('Test Password', $pass->value());
        // $this->assertEquals( 'Sign In', $submit->value() );

    }


    public function testRegister(): void
    {
        $this->timeouts()->implicitWait(30000);

        $this->url('/');

        $this->assertEquals(SITE_TITLE, $this->title());

        $link = $this->byLinkText('Register a new membership');

        $link->click(); // we must grab all new elements now

        $form = $this->byId('registerForm');

        # $register = $this->byName('submit'); // another way to do it
        # $register = $form->attribute( 'action' );
        # $register->click();

        $this->byName('firstname')->value('Richard');
        $this->byName('lastname')->value('Miles');
        $this->byName('email')->value('Richard@Miles.Systems');
        $this->byName('username')->value(self::ADMIN_USERNAME);
        $this->byName('password')->value(self::ADMIN_PASSWORD);
        $this->byName('password2')->value(self::ADMIN_PASSWORD);

        $this->select($this->byName('gender'))->selectOptionByValue('male');

        $this->byClassName('icheckbox_square-blue')->click();

        $form->submit();
    }


    /**
     * This is used in other tests. Please do not change sleeps.
     */
    public function testLogin(): void
    {
        // set the url
        $this->url('/');

        // create a form object for reuse
        $form = $this->byId('loginForm');

        // get the form action
        $action = $form->attribute('action');

        // check the action value
        $this->assertEquals(self::URL . 'login/', $action);

        // fill in the form field values
        $this->byName('username')->value(self::ADMIN_USERNAME);

        $this->byName('password')->value(self::ADMIN_PASSWORD);

        sleep(2);

        // submit the form
        // $form->submit();

        $submit = $this->byName('signin');
        $submit->submit();

        sleep(2);

    }

    protected function waitForId($id, $wait = 30)
    {   // this actually works
        for ($i = 0; $i <= $wait; $i++) {
            try {
                return $this->byId($id);
            } catch (\Exception $e) {
                sleep(1);
            }
        }
        return false;
    }

    /**
     * @depends testLogin
     */
    public function testPostScores()
    {
        $this->testLogin();

        $this->byId('postScoreHeader')->click();

        sleep(1);

        $this->select($this->byClassName('select2-hidden-accessible'))->selectOptionByValue('Texas');

        sleep(1);

        $this->select($this->byId('course'))->selectOptionByValue('Add');

        sleep(1);

        $this->byId('clear')->click();

        sleep(1);

        $this->byName('c_name')->value('Lake Park');
        $this->select($this->byId('course_type'))->selectOptionByValue('Semi-private');
        $this->select($this->byId('course_play'))->selectOptionByValue('18-hole');
        $this->byId('phone')->value('2145551234');
        $this->byName('c_street')->value('6 Lake Park Rd, TX 75057');
        $this->byName('c_city')->value('Flower Mound');
        $this->select($this->byId('state'))->selectOptionByValue('Texas');
        $this->select($this->byName('tee_boxes'))->selectOptionByValue('3');
        $this->select($this->byName('Handicap_number'))->selectOptionByValue('2');
        $this->byId('next')->click();

        sleep(2);

        $dataFile = APP_ROOT . 'data/golf/lakeparkgc.json';

        $this->assertFileExists($dataFile);

        $courseData = file_get_contents($dataFile);

        $courseData = json_decode($courseData, true);

        $courses = $courseData['courses'] ?? false;

        $this->assertNotFalse($courses);

        foreach ($courses as $key => $value) {
            /** @noinspection DisconnectedForeachInstructionInspection */
            $this->byId('color-tee-box-selection-dropdown')->click();
            sleep(1);
            $this->byId($value['tees']['name'])->click();
            sleep(1);
            $this->byName('general_difficulty')->value($value['tees']['rating']);
            $this->byName('general_slope')->value($value['tees']['slope']);
            $this->byName('women_difficulty')->value($value['tees']['rating']);
            $this->byName('women_slope')->value($value['tees']['slope']);
            sleep(5);
            /** @noinspection DisconnectedForeachInstructionInspection */
            $this->byId('next')->click();
        }

        for ($i = 1; $i <= 18; $i++) {
            sleep(1);
            $this->byId('par_hole_' . $i)->value('2');
            $this->byName('Black')->value('5');
            $this->byName('Blue')->value('7');
            $this->byName('White')->value('9');
            $this->byName('hc_Men')->value('2');
            $this->byName('hc_Women')->value('3');
            $this->byId('submit')->click();
        }
        sleep(3);


        //$this->select($this->byClassName('knob'))->selectOptionByValue('13.2');

        sleep(10);


    }

    /**
     * @depends testLogin
     */
    public function testCreateTeam()
    {
        $this->testLogin();
        $this->byId('navMenu')->click();
        $this->byId('createTeamLink')->click();
        sleep(3);
        $this->byName('teamName')->value('A team');
        $this->byName('schoolName')->value('southlake');
        sleep(2);
        $this->byId('teamSubmit')->click();
        sleep(2);
    }

    /**
     * @depends testLogin
     */
    public function testDeleteAccount()
    {
        $this->testLogin();
        $this->byId('navUserTopRightUserImage')->click();
        $this->byId('navTopRightUserDropdownProfile')->click();
        sleep(3);
        $this->byId('profileDeleteButton')->click();
        sleep(5);
        $this->byId('confirmDeleteButton')->click();
        sleep(10);
    }

}
