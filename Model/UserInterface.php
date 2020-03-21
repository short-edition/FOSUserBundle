<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Model;

use DateTime;
use Serializable;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

interface UserInterface extends Serializable, BaseUserInterface, EquatableInterface
{
    public const ROLE_DEFAULT = 'ROLE_USER';

    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * Returns the user unique id.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Sets the username.
     *
     * @return static
     */
    public function setUsername(?string $username);

    /**
     * Gets the canonical username in search and sort queries.
     */
    public function getUsernameCanonical(): string;

    /**
     * Sets the canonical username.
     *
     * @return static
     */
    public function setUsernameCanonical(string $usernameCanonical);

    /**
     * @return static
     */
    public function setSalt(?string $salt);

    /**
     * Gets email.
     */
    public function getEmail(): ?string;

    /**
     * Sets the email.
     *
     * @return static
     */
    public function setEmail(string $email);

    /**
     * Gets the canonical email in search and sort queries.
     */
    public function getEmailCanonical(): string;

    /**
     * Sets the canonical email.
     *
     * @return static
     */
    public function setEmailCanonical(string $emailCanonical);

    /**
     * Gets the plain password.
     */
    public function getPlainPassword(): ?string;

    /**
     * Sets the plain password.
     *
     * @return static
     */
    public function setPlainPassword(string $password);

    /**
     * Sets the hashed password.
     *
     * @return static
     */
    public function setPassword(string $password);

    /**
     * Tells if the the given user has the super admin role.
     */
    public function isSuperAdmin(): bool;

    /**
     * @return static
     */
    public function setEnabled(bool $boolean);

    /**
     * Sets the super admin status.
     *
     * @return static
     */
    public function setSuperAdmin(bool $boolean);

    /**
     * Gets the confirmation token.
     */
    public function getConfirmationToken(): ?string;

    /**
     * Sets the confirmation token.
     *
     * @return static
     */
    public function setConfirmationToken(?string $confirmationToken);

    /**
     * Sets the timestamp that the user requested a password reset.
     *
     * @return static
     */
    public function setPasswordRequestedAt(DateTime $date = null);

    /**
     * Checks whether the password reset request has expired.
     *
     * @return bool
     */
    public function isPasswordRequestNonExpired(int $ttl): bool;

    /**
     * Sets the last login time.
     *
     * @return static
     */
    public function setLastLogin(DateTime $time = null);

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the AuthorizationChecker, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $authorizationChecker->isGranted('ROLE_USER');
     *
     * @return bool
     */
    public function hasRole(string $role): bool;

    /**
     * Sets the roles of the user.
     *
     * This overwrites any previous roles.
     *
     * @return static
     */
    public function setRoles(array $roles);

    /**
     * Adds a role to the user.
     *
     * @return static
     */
    public function addRole(string $role);

    /**
     * Removes a role to the user.
     *
     * @return static
     */
    public function removeRole(string $role);

    /**
     * Checks whether the user's account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired(): bool;

    /**
     * Checks whether the user is locked.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @see LockedException
     */
    public function isAccountNonLocked(): bool;

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a CredentialsExpiredException and prevent login.
     *
     * @see CredentialsExpiredException
     */
    public function isCredentialsNonExpired(): bool;

    /**
     * Checks whether the user is enabled.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * @see DisabledException
     */
    public function isEnabled(): bool;
}