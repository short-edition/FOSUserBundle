<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\Command;

use FOS\UserBundle\Command\DemoteUserCommand;
use FOS\UserBundle\Util\UserManipulator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;

class DemoteUserCommandTest extends TestCase
{
    public function testExecute(): void
    {
        $commandTester = $this->createCommandTester($this->getManipulator('user', 'role', false));
        $exitCode = $commandTester->execute([
            'username' => 'user',
            'role' => 'role',
        ], [
            'decorated' => false,
            'interactive' => false,
        ]);

        $this->assertSame(0, $exitCode, 'Returns 0 in case of success');
        $this->assertRegExp('/Role "role" has been removed from user "user"/', $commandTester->getDisplay());
    }

    public function testExecuteInteractiveWithQuestionHelper(): void
    {
        $application = new Application();

        $helper = $this->getMockBuilder(QuestionHelper::class)
            ->setMethods(['ask'])
            ->getMock();

        $helper->expects($this->at(0))
            ->method('ask')
            ->willReturn('user');
        $helper->expects($this->at(1))
            ->method('ask')
            ->willReturn('role');

        $application->getHelperSet()->set($helper, 'question');

        $commandTester = $this->createCommandTester($this->getManipulator('user', 'role', false), $application);
        $exitCode = $commandTester->execute([], [
            'decorated' => false,
            'interactive' => true,
        ]);

        $this->assertSame(0, $exitCode, 'Returns 0 in case of success');
        $this->assertRegExp('/Role "role" has been removed from user "user"/', $commandTester->getDisplay());
    }

    /**
     * @return CommandTester
     */
    private function createCommandTester(UserManipulator $manipulator, Application $application = null): CommandTester
    {
        if (null === $application) {
            $application = new Application();
        }

        $application->setAutoExit(false);

        $command = new DemoteUserCommand($manipulator);

        $application->add($command);

        return new CommandTester($application->find('fos:user:demote'));
    }

    /**
     * @param $username
     * @param $role
     * @param $super
     *
     * @return mixed
     */
    private function getManipulator($username, $role, $super)
    {
        $manipulator = $this->getMockBuilder(UserManipulator::class)
            ->disableOriginalConstructor()
            ->getMock();

        if ($super) {
            $manipulator
                ->expects($this->once())
                ->method('demote')
                ->with($username)
                ->willReturn(true)
            ;
        } else {
            $manipulator
                ->expects($this->once())
                ->method('removeRole')
                ->with($username, $role)
                ->willReturn(true)
            ;
        }

        return $manipulator;
    }
}
