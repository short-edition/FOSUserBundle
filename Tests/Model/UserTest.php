<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\Model;

use DateTime;
use FOS\UserBundle\Model\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUsername(): void
    {
        $user = $this->getUser();
        $this->assertNull($user->getUsername());

        $user->setUsername('tony');
        $this->assertSame('tony', $user->getUsername());
    }

    public function testEmail(): void
    {
        $user = $this->getUser();
        $this->assertNull($user->getEmail());

        $user->setEmail('tony@mail.org');
        $this->assertSame('tony@mail.org', $user->getEmail());
    }

    public function testIsPasswordRequestNonExpired(): void
    {
        $user = $this->getUser();
        $passwordRequestedAt = new DateTime('-10 seconds');

        $user->setPasswordRequestedAt($passwordRequestedAt);

        $this->assertSame($passwordRequestedAt, $user->getPasswordRequestedAt());
        $this->assertTrue($user->isPasswordRequestNonExpired(15));
        $this->assertFalse($user->isPasswordRequestNonExpired(5));
    }

    public function testIsPasswordRequestAtCleared(): void
    {
        $user = $this->getUser();
        $passwordRequestedAt = new DateTime('-10 seconds');

        $user->setPasswordRequestedAt($passwordRequestedAt);
        $user->setPasswordRequestedAt(null);

        $this->assertFalse($user->isPasswordRequestNonExpired(15));
        $this->assertFalse($user->isPasswordRequestNonExpired(5));
    }

    public function testTrueHasRole(): void
    {
        $user = $this->getUser();
        $defaultrole = User::ROLE_DEFAULT;
        $newrole = 'ROLE_X';
        $this->assertTrue($user->hasRole($defaultrole));
        $user->addRole($defaultrole);
        $this->assertTrue($user->hasRole($defaultrole));
        $user->addRole($newrole);
        $this->assertTrue($user->hasRole($newrole));
    }

    public function testFalseHasRole(): void
    {
        $user = $this->getUser();
        $newrole = 'ROLE_X';
        $this->assertFalse($user->hasRole($newrole));
        $user->addRole($newrole);
        $this->assertTrue($user->hasRole($newrole));
    }

    public function testIsEqualTo(): void
    {
        $user = $this->getUser();
        $this->assertTrue($user->isEqualTo($user));
        $this->assertTrue($user->isEqualTo($this->getMockBuilder(User::class)->getMock()));

        $user2 = $this->getUser();
        $user2->setPassword('secret');
        $this->assertFalse($user->isEqualTo($user2));

        $user3 = $this->getUser();
        $user3->setSalt('pepper');
        $this->assertFalse($user->isEqualTo($user3));

        $user4 = $this->getUser();
        $user4->setUsername('f00b4r');
        $this->assertFalse($user->isEqualTo($user4));
    }

    protected function getUser(): User
    {
        return $this->getMockForAbstractClass(User::class);
    }
}
