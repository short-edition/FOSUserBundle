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
 * Interface to be implemented by group managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to groups should happen through this interface.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
interface GroupManagerInterface
{
    /**
     * Returns an empty group instance.
     */
    public function createGroup(string $name): GroupInterface;

    /**
     * Deletes a group.
     */
    public function deleteGroup(GroupInterface $group): void;

    /**
     * Finds one group by the given criteria.
     */
    public function findGroupBy(array $criteria): GroupInterface;

    /**
     * Finds a group by name.
     */
    public function findGroupByName(string $name): GroupInterface;

    /**
     * Returns a collection with all group instances.
     */
    public function findGroups(): array;

    /**
     * Returns the group's fully qualified class name.
     */
    public function getClass(): string;

    /**
     * Updates a group.
     */
    public function updateGroup(GroupInterface $group): void;
}
