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

use FOS\UserBundle\Model\User;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Security\UserProvider;
use FOS\UserBundle\Tests\TestUser;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class UserProviderTest extends TestCase
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
        $this->userManager = $this->getUserManagerMock();
        $this->userProvider = new UserProvider($this->userManager);
    }

    public function testLoadUserByUsername(): void
    {
        $user = $this->getUserMock();
        $this->userManager->expects($this->once())
            ->method('findUserByUsername')
            ->with('foobar')
            ->willReturn($user);

        $this->assertSame($user, $this->userProvider->loadUserByUsername('foobar'));
    }

    public function testLoadUserByInvalidUsername(): void
    {
        $this->expectException(UsernameNotFoundException::class);

        $this->userManager->expects($this->once())
            ->method('findUserByUsername')
            ->with('foobar')
            ->willReturn(null);

        $this->userProvider->loadUserByUsername('foobar');
    }

    public function testRefreshUserBy(): void
    {
        $user = $this->getMockBuilder(User::class)
            ->setMethods(['getId'])
            ->getMock();

        $user->expects($this->once())
            ->method('getId')
            ->willReturn('123');

        $refreshedUser = $this->getMockBuilder(User::class)->getMock();
        $this->userManager->expects($this->once())
            ->method('findUserBy')
            ->with(['id' => '123'])
            ->willReturn($refreshedUser);

        $this->userManager->expects($this->atLeastOnce())
            ->method('getClass')
            ->willReturn(get_class($user));

        $this->assertSame($refreshedUser, $this->userProvider->refreshUser($user));
    }

    public function testRefreshDeleted(): void
    {
        $this->expectException(UsernameNotFoundException::class);

        $user = $this->getMockForAbstractClass(User::class);
        $this->userManager->expects($this->once())
            ->method('findUserBy')
            ->willReturn(null);

        $this->userManager->expects($this->atLeastOnce())
            ->method('getClass')
            ->willReturn(get_class($user));

        $this->userProvider->refreshUser($user);
    }

    public function testRefreshInvalidUser(): void
    {
        $this->expectException(UnsupportedUserException::class);

        $user = $this->getMockBuilder(UserInterface::class)->getMock();
        $this->userManager->expects($this->any())
            ->method('getClass')
            ->willReturn(get_class($user));

        $this->userProvider->refreshUser($user);
    }

    public function testRefreshInvalidUserClass(): void
    {
        $this->expectException(UnsupportedUserException::class);

        $this->userManager->expects($this->atLeastOnce())
            ->method('getClass')
            ->willReturn(get_class($this->getUserMock()));

        $this->userProvider->refreshUser($this->getTestUserMock());
    }

    private function getUserManagerMock(): UserManagerInterface
    {
        return $this->getMockBuilder(UserManagerInterface::class)->getMock();
    }

    private function getUserMock(): User
    {
        return $this->getMockBuilder(User::class)->getMock();
    }

    private function getTestUserMock(): TestUser
    {
        return $this->getMockBuilder(TestUser::class)->getMock();
    }
}
