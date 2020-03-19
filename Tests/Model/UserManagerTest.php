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

use FOS\UserBundle\Model\User;
use FOS\UserBundle\Model\UserManager;
use FOS\UserBundle\Util\CanonicalFieldsUpdater;
use FOS\UserBundle\Util\PasswordUpdaterInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class UserManagerTest extends TestCase
{
    /** @var UserManager|PHPUnit_Framework_MockObject_MockObject */
    private $manager;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $passwordUpdater;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $fieldsUpdater;

    protected function setUp(): void
    {
        $this->passwordUpdater = $this->getMockBuilder(PasswordUpdaterInterface::class)->getMock();
        $this->fieldsUpdater = $this->getMockBuilder(CanonicalFieldsUpdater::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->manager = $this->getUserManager([
            $this->passwordUpdater,
            $this->fieldsUpdater,
        ]);
    }

    public function testUpdateCanonicalFields(): void
    {
        $user = $this->getUser();

        $this->fieldsUpdater->expects($this->once())
            ->method('updateCanonicalFields')
            ->with($this->identicalTo($user));

        $this->manager->updateCanonicalFields($user);
    }

    public function testUpdatePassword(): void
    {
        $user = $this->getUser();

        $this->passwordUpdater->expects($this->once())
            ->method('hashPassword')
            ->with($this->identicalTo($user));

        $this->manager->updatePassword($user);
    }

    public function testFindUserByUsername(): void
    {
        $this->manager->expects($this->once())
            ->method('findUserBy')
            ->with($this->equalTo(['usernameCanonical' => 'jack']));
        $this->fieldsUpdater->expects($this->once())
            ->method('canonicalizeUsername')
            ->with('jack')
            ->willReturn('jack');

        $this->manager->findUserByUsername('jack');
    }

    public function testFindUserByUsernameLowercasesTheUsername(): void
    {
        $this->manager->expects($this->once())
            ->method('findUserBy')
            ->with($this->equalTo(['usernameCanonical' => 'jack']));
        $this->fieldsUpdater->expects($this->once())
            ->method('canonicalizeUsername')
            ->with('JaCk')
            ->willReturn('jack');

        $this->manager->findUserByUsername('JaCk');
    }

    public function testFindUserByEmail(): void
    {
        $this->manager->expects($this->once())
            ->method('findUserBy')
            ->with($this->equalTo(['emailCanonical' => 'jack@email.org']));
        $this->fieldsUpdater->expects($this->once())
            ->method('canonicalizeEmail')
            ->with('jack@email.org')
            ->willReturn('jack@email.org');

        $this->manager->findUserByEmail('jack@email.org');
    }

    public function testFindUserByEmailLowercasesTheEmail(): void
    {
        $this->manager->expects($this->once())
            ->method('findUserBy')
            ->with($this->equalTo(['emailCanonical' => 'jack@email.org']));
        $this->fieldsUpdater->expects($this->once())
            ->method('canonicalizeEmail')
            ->with('JaCk@EmAiL.oRg')
            ->willReturn('jack@email.org');

        $this->manager->findUserByEmail('JaCk@EmAiL.oRg');
    }

    public function testFindUserByUsernameOrEmailWithUsername(): void
    {
        $this->manager->expects($this->once())
            ->method('findUserBy')
            ->with($this->equalTo(['usernameCanonical' => 'jack']));
        $this->fieldsUpdater->expects($this->once())
            ->method('canonicalizeUsername')
            ->with('JaCk')
            ->willReturn('jack');

        $this->manager->findUserByUsernameOrEmail('JaCk');
    }

    public function testFindUserByUsernameOrEmailWithEmail(): void
    {
        $this->manager->expects($this->once())
            ->method('findUserBy')
            ->with($this->equalTo(['emailCanonical' => 'jack@email.org']))
            ->willReturn($this->getUser());
        $this->fieldsUpdater->expects($this->once())
            ->method('canonicalizeEmail')
            ->with('JaCk@EmAiL.oRg')
            ->willReturn('jack@email.org');

        $this->manager->findUserByUsernameOrEmail('JaCk@EmAiL.oRg');
    }

    public function testFindUserByUsernameOrEmailWithUsernameThatLooksLikeEmail(): void
    {
        $usernameThatLooksLikeEmail = 'bob@example.com';
        $user = $this->getUser();

        $this->manager->expects($this->at(0))
            ->method('findUserBy')
            ->with($this->equalTo(['emailCanonical' => $usernameThatLooksLikeEmail]))
            ->willReturn(null);
        $this->fieldsUpdater->expects($this->once())
            ->method('canonicalizeEmail')
            ->with($usernameThatLooksLikeEmail)
            ->willReturn($usernameThatLooksLikeEmail);

        $this->manager->expects($this->at(1))
            ->method('findUserBy')
            ->with($this->equalTo(['usernameCanonical' => $usernameThatLooksLikeEmail]))
            ->willReturn($user);
        $this->fieldsUpdater->expects($this->once())
            ->method('canonicalizeUsername')
            ->with($usernameThatLooksLikeEmail)
            ->willReturn($usernameThatLooksLikeEmail);

        $actualUser = $this->manager->findUserByUsernameOrEmail($usernameThatLooksLikeEmail);

        $this->assertSame($user, $actualUser);
    }

    /**
     * @return mixed
     */
    private function getUser()
    {
        return $this->getMockBuilder(User::class)
            ->getMockForAbstractClass();
    }

    /**
     * @return mixed
     */
    private function getUserManager(array $args)
    {
        return $this->getMockBuilder(UserManager::class)
            ->setConstructorArgs($args)
            ->getMockForAbstractClass();
    }
}
