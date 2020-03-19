<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\Mailer;

use FOS\UserBundle\Mailer\Mailer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Swift_Events_EventDispatcher;
use Swift_Mailer;
use Swift_RfcComplianceException;
use Swift_Transport_NullTransport;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class MailerTest extends TestCase
{
    /**
     * @dataProvider goodEmailProvider
     */
    public function testSendConfirmationEmailMessageWithGoodEmails($emailAddress): void
    {
        $mailer = $this->getMailer();
        $mailer->sendConfirmationEmailMessage($this->getUser($emailAddress));

        $this->assertTrue(true);
    }

    /**
     * @dataProvider badEmailProvider
     * @expectedException Swift_RfcComplianceException
     */
    public function testSendConfirmationEmailMessageWithBadEmails($emailAddress): void
    {
        $this->expectException(Swift_RfcComplianceException::class);
        $mailer = $this->getMailer();
        $mailer->sendConfirmationEmailMessage($this->getUser($emailAddress));
    }

    /**
     * @dataProvider goodEmailProvider
     */
    public function testSendResettingEmailMessageWithGoodEmails($emailAddress): void
    {
        $mailer = $this->getMailer();
        $mailer->sendResettingEmailMessage($this->getUser($emailAddress));

        $this->assertTrue(true);
    }

    /**
     * @dataProvider badEmailProvider
     * @expectedException Swift_RfcComplianceException
     */
    public function testSendResettingEmailMessageWithBadEmails($emailAddress): void
    {
        $this->expectException(Swift_RfcComplianceException::class);
        $mailer = $this->getMailer();
        $mailer->sendResettingEmailMessage($this->getUser($emailAddress));
    }

    public function goodEmailProvider(): array
    {
        return [
            ['foo@example.com'],
            ['foo@example.co.uk'],
            [$this->getEmailAddressValueObject('foo@example.com')],
            [$this->getEmailAddressValueObject('foo@example.co.uk')],
        ];
    }

    public function badEmailProvider(): array
    {
        return [
            ['foo'],
            [$this->getEmailAddressValueObject('foo')],
        ];
    }

    private function getMailer(): Mailer
    {
        return new Mailer(
            new Swift_Mailer(
                new Swift_Transport_NullTransport(
                    $this->getMockBuilder(Swift_Events_EventDispatcher::class)->getMock()
                )
            ),
            $this->getMockBuilder(UrlGeneratorInterface::class)->getMock(),
            $this->getTemplating(),
            [
                'confirmation.template' => 'foo',
                'resetting.template' => 'foo',
                'from_email' => [
                    'confirmation' => 'foo@example.com',
                    'resetting' => 'foo@example.com',
                ],
            ]
        );
    }

    private function getTemplating(): MockObject
    {
        return $this->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getUser(string $emailAddress): MockObject
    {
        $user = $this->getMockBuilder(User::class)->getMock();
        $user->method('getEmail')
            ->willReturn($emailAddress);

        return $user;
    }

    private function getEmailAddressValueObject($emailAddressAsString): MockObject
    {
        $emailAddress = $this->getMockBuilder('EmailAddress')
            ->setMethods(['__toString'])
            ->getMock();

        $emailAddress->method('__toString')
            ->willReturn($emailAddressAsString);

        return $emailAddress;
    }
}
