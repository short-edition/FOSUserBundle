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

use FOS\UserBundle\Command\ChangePasswordCommand;
use FOS\UserBundle\Util\UserManipulator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;

class ChangePasswordCommandTest extends TestCase
{
    public function testExecute(): void
    {
        $commandTester = $this->createCommandTester($this->getManipulator('user', 'pass'));
        $exitCode = $commandTester->execute([
            'username' => 'user',
            'password' => 'pass',
        ], [
            'decorated' => false,
            'interactive' => false,
        ]);

        $this->assertSame(0, $exitCode, 'Returns 0 in case of success');
        $this->assertRegExp('/Changed password for user user/', $commandTester->getDisplay());
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
            ->willReturn('pass');

        $application->getHelperSet()->set($helper, 'question');

        $commandTester = $this->createCommandTester($this->getManipulator('user', 'pass'), $application);
        $exitCode = $commandTester->execute([], [
            'decorated' => false,
            'interactive' => true,
        ]);

        $this->assertSame(0, $exitCode, 'Returns 0 in case of success');
        $this->assertRegExp('/Changed password for user user/', $commandTester->getDisplay());
    }

    /**
     * @param UserManipulator $container
     *
     * @return CommandTester
     */
    private function createCommandTester(UserManipulator $userManipulator, Application $application = null): CommandTester
    {
        if (null === $application) {
            $application = new Application();
        }

        $application->setAutoExit(false);

        $command = new ChangePasswordCommand($userManipulator);

        $application->add($command);

        return new CommandTester($application->find('fos:user:change-password'));
    }

    /**
     * @param $username
     * @param $password
     *
     * @return mixed
     */
    private function getManipulator($username, $password)
    {
        $manipulator = $this->getMockBuilder(UserManipulator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $manipulator
            ->expects($this->once())
            ->method('changePassword')
            ->with($username, $password)
        ;

        return $manipulator;
    }
}
