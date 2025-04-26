<?php

namespace src\refactors\task4\tests;

use Couchbase\User;
use Exception;
use PHPUnit\Framework\TestCase;
use src\refactors\task4\before\UserManager;

class ClassBeforeTest extends TestCase
{
    public function testRegisterValidUser(): void
    {
        $userManager = new UserManager();

        $this->expectNotToPerformAssertions();

        $userManager->register([
            'email' => 'alex@test.com',
            'password' => '12345678'
        ]);
    }

    public function testRegisterUserWithInvalidEmail(): void
    {
        $userManager = new UserManager();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid email');

        $userManager->register([
            'email' => 'alex_test.com',
            'password' => '12345678'
        ]);
    }

    public function testRegisterUserWithInvalidPassword(): void
    {
        $userManager = new UserManager();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Password too short');

        $userManager->register([
            'email' => 'alex@test.com',
            'password' => '1234'
        ]);
    }
}
