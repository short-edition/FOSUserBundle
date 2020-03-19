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
use FOS\UserBundle\Util\CanonicalFieldsUpdater;
use FOS\UserBundle\Util\CanonicalizerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CanonicalFieldsUpdaterTest extends TestCase
{
    /**
     * @var CanonicalFieldsUpdater
     */
    private $updater;
    private $usernameCanonicalizer;
    private $emailCanonicalizer;

    protected function setUp(): void
    {
        $this->usernameCanonicalizer = $this->getMockCanonicalizer();
        $this->emailCanonicalizer = $this->getMockCanonicalizer();

        $this->updater = new CanonicalFieldsUpdater($this->usernameCanonicalizer, $this->emailCanonicalizer);
    }

    public function testUpdateCanonicalFields(): void
    {
        $user = new TestUser();
        $user->setUsername('Username');
        $user->setEmail('User@Example.com');

        $this->usernameCanonicalizer->expects($this->once())
            ->method('canonicalize')
            ->with('Username')
            ->willReturnCallback('strtolower');

        $this->emailCanonicalizer->expects($this->once())
            ->method('canonicalize')
            ->with('User@Example.com')
            ->willReturnCallback('strtolower');

        $this->updater->updateCanonicalFields($user);
        $this->assertSame('username', $user->getUsernameCanonical());
        $this->assertSame('user@example.com', $user->getEmailCanonical());
    }

    private function getMockCanonicalizer(): MockObject
    {
        return $this->getMockBuilder(CanonicalizerInterface::class)->getMock();
    }
}
