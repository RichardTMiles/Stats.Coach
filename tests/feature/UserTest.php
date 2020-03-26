<?php
/**
 * Created by IntelliJ IDEA.
 * User: rmiles
 * Date: 6/26/2018
 * Time: 3:21 PM
 */

declare(strict_types=1);

namespace App\Tests\Feature;

use App\Tests\Config;
use PHPUnit\Framework\TestCase;
use Tables\carbon_users as Users;
use \CarbonPHP\Database;

final class UserTest extends TestCase
{

    public $user;

    /**
     * Ideally this is run with a fresh build. If not, the relation between create new users
     * must depend on can be deleted. This is cyclic and can not be annotated.
     */
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
            'where' => [
                Users::USER_USERNAME => Config::ADMIN_USERNAME
            ]
        ]);

    }

    public function commit(callable $lambda = null): bool
    {
        $commit = new class extends Database {
            public function testCommit(callable $lambda = null): bool
            {
                /** @noinspection MissUsingParentKeywordInspection */
                return parent::commit($lambda);
            }
        };
        return $commit->testCommit($lambda);
    }


    public function testUserCanBeCreated(): void
    {
        if (!empty($this->user)) {
            $this->testUserCanBeDeleted();
        }

        $this->assertIsString($id = Users::Post([
            Users::USER_TYPE => 'Athlete',
            Users::USER_IP => '127.0.0.1',
            Users::USER_SPORT => 'GOLF',
            Users::USER_EMAIL_CONFIRMED => 1,
            Users::USER_USERNAME => Config::ADMIN_USERNAME,
            Users::USER_PASSWORD => Config::ADMIN_PASSWORD,
            Users::USER_EMAIL => 'richard@miles.systems',
            Users::USER_FIRST_NAME => 'Richard',
            Users::USER_LAST_NAME => 'Miles',
            Users::USER_GENDER => 'Male'
        ]));

        $this->commit();
    }


    /**
     * @depends testUserCanBeCreated
     */
    public function testUserCanBeRetrieved(): void
    {
        $this->user = [];
        $this->assertTrue(
            Users::Get($this->user, null, [
                    'where' => [
                        Users::USER_USERNAME => Config::ADMIN_USERNAME
                    ],
                    'pagination' => [
                        'limit' => 1
                    ]
                ]
            ));

        $this->assertIsArray($this->user);

        $this->assertArrayHasKey(Users::USER_EMAIL, $this->user);

    }

    /**
     * @depends testUserCanBeRetrieved
     */
    public function testUserCanBeUpdated(): void
    {
        $this->assertTrue(
            Users::Get($this->user, null, [
                    'where' => [
                        Users::USER_USERNAME => Config::ADMIN_USERNAME
                    ]
                ]
            ));

        $this->user = $this->user[0];

        $this->assertTrue(
            Users::Put($this->user, $this->user[Users::USER_ID], [
                Users::USER_FIRST_NAME => 'lil\'Rich'
            ]));

        $this->commit();

        $this->user = [];

        $this->assertTrue(
            Users::Get($this->user, null, [
                    'where' => [
                        Users::USER_USERNAME => Config::ADMIN_USERNAME
                    ],
                    'pagination' => [
                        'limit' => 1
                    ]
                ]
            ));

        $this->assertEquals('lil\'Rich', $this->user[Users::USER_FIRST_NAME]);
    }


    /**
     * @depends testUserCanBeRetrieved
     */
    public function testUserCanBeDeleted(): void
    {
        $user = [];

        Users::Get($user, null, [
            'where' => [
                Users::USER_USERNAME => Config::ADMIN_USERNAME
            ]
        ]);

        $this->assertNotEmpty($user, 'User (' . Config::ADMIN_USERNAME . ') does not appear to exist.');

        $user = $user[0];

        $this->assertTrue(
            Users::Delete($this->user, $user[Users::USER_ID], [])
        );

        $this->assertNull($this->user);

        $this->user = [];

        Users::Get($this->user, null, [
            'where' => [
                Users::USER_USERNAME => Config::ADMIN_USERNAME
            ]
        ]);

        $this->assertEmpty($this->user);

    }


}