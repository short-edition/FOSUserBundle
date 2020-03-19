<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\Util;

use FOS\UserBundle\Tests\TestUser;
use FOS\UserBundle\Util\PasswordUpdater;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\NativePasswordEncoder;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class PasswordUpdaterTest extends TestCase
{
    /**
     * @var PasswordUpdater
     */
    private $updater;
    private $encoderFactory;

    protected function setUp(): void
    {
        $this->encoderFactory = $this->getMockBuilder(EncoderFactoryInterface::class)->getMock();

        $this->updater = new PasswordUpdater($this->encoderFactory);
    }

    public function testUpdatePassword(): void
    {
        $encoder = $this->getMockPasswordEncoder();
        $user = new TestUser();
        $user->setPlainPassword('password');

        $this->encoderFactory->expects($this->once())
            ->method('getEncoder')
            ->with($user)
            ->willReturn($encoder);

        $encoder->expects($this->once())
            ->method('encodePassword')
            ->with('password', $this->isType('string'))
            ->willReturn('encodedPassword');

        $this->updater->hashPassword($user);
        $this->assertSame('encodedPassword', $user->getPassword(), '->updatePassword() sets encoded password');
        $this->assertNotNull($user->getSalt());
        $this->assertNull($user->getPlainPassword(), '->updatePassword() erases credentials');
    }

    public function testUpdatePasswordWithBCrypt(): void
    {
        $encoder = new NativePasswordEncoder(null, null, null, PASSWORD_BCRYPT);
        $user = new TestUser();
        $user->setPlainPassword('password');
        $user->setSalt('old_salt');

        $this->encoderFactory->expects($this->once())
            ->method('getEncoder')
            ->with($user)
            ->willReturn($encoder);

//        $encoder->expects($this->once())
//            ->method('encodePassword')
//            ->with('password', $this->isNull())
//            ->willReturn('encodedPassword');

        $this->updater->hashPassword($user);
        //$this->assertSame('encodedPassword', $user->getPassword(), '->updatePassword() sets encoded password');
        $this->assertNull($user->getSalt());
        $this->assertNull($user->getPlainPassword(), '->updatePassword() erases credentials');
    }

    public function testDoesNotUpdateWithoutPlainPassword(): void
    {
        $user = new TestUser();
        $user->setPassword('hash');

        $user->setPlainPassword('');

        $this->updater->hashPassword($user);
        $this->assertSame('hash', $user->getPassword());
    }

    private function getMockPasswordEncoder(): MockObject
    {
        return $this->getMockBuilder(PasswordEncoderInterface::class)->getMock();
    }
}
