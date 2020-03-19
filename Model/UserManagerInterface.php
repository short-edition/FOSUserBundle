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

/**
 * Interface to be implemented by user managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to users should happen through this interface.
 *
 * The class also contains ACL annotations which will only work if you have the
 * SecurityExtraBundle installed, otherwise they will simply be ignored.
 *
 * @author Gordon Franke <info@nevalon.de>
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface UserManagerInterface
{
    /**
     * Creates an empty user instance.
     */
    public function createUser(): UserInterface;

    /**
     * Deletes a user.
     */
    public function deleteUser(UserInterface $user): void;

    /**
     * Finds one user by the given criteria.
     */
    public function findUserBy(array $criteria): ?UserInterface;

    /**
     * Find a user by its username.
     */
    public function findUserByUsername(string $username): ?UserInterface;

    /**
     * Finds a user by its email.
     */
    public function findUserByEmail(string $email): ?UserInterface;

    /**
     * Finds a user by its username or email.
     */
    public function findUserByUsernameOrEmail(string $usernameOrEmail): ?UserInterface;

    /**
     * Finds a user by its confirmationToken.
     */
    public function findUserByConfirmationToken(int $token): ?UserInterface;

    /**
     * Returns a collection with all user instances.
     */
    public function findUsers(): array;

    /**
     * Returns the user's fully qualified class name.
     */
    public function getClass(): string;

    /**
     * Reloads a user.
     */
    public function reloadUser(UserInterface $user): void;

    /**
     * Updates a user.
     */
    public function updateUser(UserInterface $user, bool $andFlush = true);

    /**
     * Updates the canonical username and email fields for a user.
     */
    public function updateCanonicalFields(UserInterface $user): void;

    /**
     * Updates a user password if a plain password is set.
     */
    public function updatePassword(UserInterface $user): void;
}
