<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\Doctrine;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use FOS\UserBundle\Doctrine\UserManager;
use FOS\UserBundle\Model\User;
use FOS\UserBundle\Util\CanonicalFieldsUpdater;
use FOS\UserBundle\Util\PasswordUpdaterInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class UserManagerTest extends TestCase
{
    public const USER_CLASS = DummyUser::class;

    /** @var UserManager */
    protected $userManager;
    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $om;
    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $repository;

    public function setUp(): void
    {
        if (!interface_exists(ObjectManager::class)) {
            $this->markTestSkipped('Doctrine Common has to be installed for this test to run.');
        }

        $passwordUpdater = $this->getMockBuilder(PasswordUpdaterInterface::class)->getMock();
        $fieldsUpdater = $this->getMockBuilder(CanonicalFieldsUpdater::class)
            ->disableOriginalConstructor()
            ->getMock();
        $class = $this->getMockBuilder(ClassMetadata::class)->getMock();
        $this->om = $this->getMockBuilder(ObjectManager::class)->getMock();
        $this->repository = $this->getMockBuilder(ObjectRepository::class)->getMock();

        $this->om->expects($this->any())
            ->method('getRepository')
            ->with($this->equalTo(static::USER_CLASS))
            ->willReturn($this->repository);
        $this->om->expects($this->any())
            ->method('getClassMetadata')
            ->with($this->equalTo(static::USER_CLASS))
            ->willReturn($class);
        $class->expects($this->any())
            ->method('getName')
            ->willReturn(static::USER_CLASS);

        $this->userManager = new UserManager($passwordUpdater, $fieldsUpdater, $this->om, static::USER_CLASS);
    }

    public function testDeleteUser(): void
    {
        $user = $this->getUser();
        $this->om->expects($this->once())->method('remove')->with($this->equalTo($user));
        $this->om->expects($this->once())->method('flush');

        $this->userManager->deleteUser($user);
    }

    public function testGetClass(): void
    {
        $this->assertSame(static::USER_CLASS, $this->userManager->getClass());
    }

    public function testFindUserBy(): void
    {
        $crit = ['foo' => 'bar'];
        $this->repository->expects($this->once())->method('findOneBy')->with($this->equalTo($crit))->willReturn(null);

        $this->userManager->findUserBy($crit);
    }

    public function testFindUsers(): void
    {
        $this->repository->expects($this->once())->method('findAll')->willReturn([]);

        $this->userManager->findUsers();
    }

    public function testUpdateUser(): void
    {
        $user = $this->getUser();
        $this->om->expects($this->once())->method('persist')->with($this->equalTo($user));
        $this->om->expects($this->once())->method('flush');

        $this->userManager->updateUser($user);
    }

    /**
     * @return mixed
     */
    protected function getUser()
    {
        $userClass = static::USER_CLASS;

        return new $userClass();
    }
}

class DummyUser extends User
{
}
