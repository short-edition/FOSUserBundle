<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\Security;

use FOS\UserBundle\Security\EmailProvider;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class EmailProviderTest extends TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $userManager;

    /**
     * @var UserProvider
     */
    private $userProvider;

    protected function setUp(): void
    {
        $this->userManager = $this->getMockBuilder(UserManagerInterface::class)->getMock();
        $this->userProvider = new EmailProvider($this->userManager);
    }

    public function testLoadUserByUsername()
    {
        $user = $this->getMockBuilder(User::class)->getMock();
        $this->userManager->expects($this->once())
            ->method('findUserByEmail')
            ->with('foobar')
            ->will($this->returnValue($user));

        $this->assertSame($user, $this->userProvider->loadUserByUsername('foobar'));
    }

    /**
     * @expectedException UsernameNotFoundException
     */
    public function testLoadUserByInvalidUsername()
    {
        $this->userManager->expects($this->once())
            ->method('findUserByEmail')
            ->with('foobar')
            ->will($this->returnValue(null));

        $this->userProvider->loadUserByUsername('foobar');
    }

    public function testRefreshUserBy()
    {
        $user = $this->getMockBuilder(User::class)
            ->setMethods(['getId'])
            ->getMock();

        $user->expects($this->once())
            ->method('getId')
            ->will($this->returnValue('123'));

        $refreshedUser = $this->getMockBuilder(User::class)->getMock();
        $this->userManager->expects($this->once())
            ->method('findUserBy')
            ->with(['id' => '123'])
            ->will($this->returnValue($refreshedUser));

        $this->userManager->expects($this->atLeastOnce())
            ->method('getClass')
            ->will($this->returnValue(get_class($user)));

        $this->assertSame($refreshedUser, $this->userProvider->refreshUser($user));
    }

    /**
     * @expectedException UsernameNotFoundException
     */
    public function testRefreshDeleted()
    {
        $user = $this->getMockForAbstractClass(User::class);
        $this->userManager->expects($this->once())
            ->method('findUserBy')
            ->will($this->returnValue(null));

        $this->userManager->expects($this->atLeastOnce())
            ->method('getClass')
            ->will($this->returnValue(get_class($user)));

        $this->userProvider->refreshUser($user);
    }

    /**
     * @expectedException UnsupportedUserException
     */
    public function testRefreshInvalidUser()
    {
        $user = $this->getMockBuilder('Symfony\Component\Security\Core\User\UserInterface')->getMock();
        $this->userManager->expects($this->any())
            ->method('getClass')
            ->will($this->returnValue(get_class($user)));

        $this->userProvider->refreshUser($user);
    }

    /**
     * @expectedException UnsupportedUserException
     */
    public function testRefreshInvalidUserClass()
    {
        $user = $this->getMockBuilder(User::class)->getMock();
        $providedUser = $this->getMockBuilder(TestUser::class)->getMock();

        $this->userManager->expects($this->atLeastOnce())
            ->method('getClass')
            ->will($this->returnValue(get_class($user)));

        $this->userProvider->refreshUser($providedUser);
    }
}
