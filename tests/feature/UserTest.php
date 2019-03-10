<?php
/**
 * Created by IntelliJ IDEA.
 * User: rmiles
 * Date: 6/26/2018
 * Time: 3:21 PM
 */

declare(strict_types=1);

namespace App\Tests\Feature;

use PHPUnit\Framework\TestCase;
use Tables\carbon_users as Users;
use \CarbonPHP\Database;

final class UserTest extends TestCase
{

    public $user;

    public function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        parent::setUp();

        $_POST = [
            'username' => 'Admin',
            'password' => 'goldteamrules',
            'password2' => 'goldteamrules',
            'email' => 'Tmiles199@gmail.com',
            'firstname' => 'Dick',
            'lastname' => 'Miles',
            'gender' => 'Male',
            'Terms' => '1'
        ];

        $this->user = [];

        Users::Get($this->user, null, [
            'user_username' => 'Admin'
        ]);
    }

    public function commit(callable $lambda = null): bool
    {
        $commit = new class extends Database
        {
            public function testCommit($lambda)
            {
                return self::commit($lambda);
            }
        };
        return $commit->testCommit($lambda);
    }

    /**
     * @runInSeparateProcess
     */
    public function testUserCanBeCreated(): void
    {

        if (!empty($this->user)) {
            $this->testUserCanBeDeleted();
        }

        $this->assertInternalType('string', $id = Users::Post([
            'user_type' => 'Athlete',
            'user_ip' => '127.0.0.1',
            'user_sport' => 'GOLF',
            'user_email_confirmed' => 1,
            'user_username' => 'admin',
            'user_password' => 'goldteam',
            'user_email' => 'richard@miles.systems',
            'user_first_name' => 'Richard',
            'user_last_name' => 'Miles',
            'user_gender' => 'Male'
        ]));

        $this->commit();

    }


    /**
     *
     * @runInSeparateProcess
     */
    public function testUserCanBeRetrieved(): void
    {
        $this->user = [];
        $this->assertTrue(
            Users::Get($this->user, null, [
                    'where' => [
                        'user_username' => 'admin'
                    ],
                    'pagination' => [
                        'limit' => 1
                    ]
                ]
            ));

        $this->assertInternalType('array', $this->user);

        $this->assertArrayHasKey('user_email', $this->user);
    }

    /**
     * @depends testUserCanBeRetrieved
     * @runInSeparateProcess
     */
    public function testUserCanBeUpdated(): void
    {
        $this->assertTrue(
            Users::Get($this->user, null, ['user_username' => 'admin']
            ));

        $this->user = $this->user[0];

        $this->assertTrue(
            Users::Put($this->user, $this->user['user_id'], [
                'user_first_name' => 'lil\'Rich'
            ]));

        $this->commit();

        $this->user = [];

        $this->assertTrue(
            Users::Get($this->user, null, [
                    'where' => ['user_username' => 'admin'],
                    'pagination' => ['limit' => 1]
                ]
            ));

        $this->assertEquals('lil\'Rich', $this->user['user_first_name']);
    }


    /**
     * @depends testUserCanBeRetrieved
     * @runInSeparateProcess
     */
    public function testUserCanBeDeleted(): void
    {
        $user = [];
        Users::Get($user, null, [
            'user_username' => 'Admin'
        ]);

        $user = $user[0];

        $this->assertTrue(
            Users::Delete($this->user, $user['user_id'], [])
        );

        $this->assertNull($this->user);

        $this->user = [];

        Users::Get($this->user, null, ['user_username' => 'Admin']);

        $this->assertTrue(empty($this->user));

    }


}