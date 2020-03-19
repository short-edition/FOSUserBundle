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

use FOS\UserBundle\Security\LoginManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;

class LoginManagerTest extends TestCase
{
    public function testLogInUserWithRequestStack(): void
    {
        $loginManager = $this->createLoginManager('main');
        $loginManager->logInUser('main', $this->mockUser());
    }

    public function testLogInUserWithRememberMeAndRequestStack(): void
    {
        $response = $this->getMockBuilder(Response::class)->getMock();

        $loginManager = $this->createLoginManager('main', $response);
        $loginManager->logInUser('main', $this->mockUser(), $response);
    }

    private function createLoginManager(string $firewallName, Response $response = null): LoginManager
    {
        $tokenStorage = $this->getMockBuilder(TokenStorageInterface::class)->getMock();

        $tokenStorage
            ->expects($this->once())
            ->method('setToken')
            ->with($this->isInstanceOf(TokenInterface::class));

        $userChecker = $this->getMockBuilder(UserCheckerInterface::class)->getMock();
        $userChecker
            ->expects($this->once())
            ->method('checkPreAuth')
            ->with($this->isInstanceOf(User::class));

        $request = $this->getMockBuilder(Request::class)->getMock();

        $sessionStrategy = $this->getMockBuilder(SessionAuthenticationStrategyInterface::class)->getMock();
        $sessionStrategy
            ->expects($this->once())
            ->method('onAuthentication')
            ->with($request, $this->isInstanceOf(TokenInterface::class));

        $requestStack = $this->getMockBuilder(RequestStack::class)->getMock();
        $requestStack
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($request);

        $rememberMe = null;
        if (null !== $response) {
            $rememberMe = $this->getMockBuilder(RememberMeServicesInterface::class)->getMock();
            $rememberMe
                ->expects($this->once())
                ->method('loginSuccess')
                ->with($request, $response, $this->isInstanceOf(TokenInterface::class));
        }

        return new LoginManager($tokenStorage, $userChecker, $sessionStrategy, $requestStack, $rememberMe);
    }

    /**
     * @return mixed
     */
    private function mockUser()
    {
        $user = $this->getMockBuilder(User::class)->getMock();
        $user
            ->expects($this->once())
            ->method('getRoles')
            ->willReturn(['ROLE_USER']);

        return $user;
    }
}
