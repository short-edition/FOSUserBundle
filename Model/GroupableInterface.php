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

use Traversable;

/**
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
interface GroupableInterface
{
    /**
     * Gets the groups granted to the user.
     */
    public function getGroups(): Traversable;

    /**
     * Gets the name of the groups which includes the user.
     */
    public function getGroupNames(): array;

    /**
     * Indicates whether the user belongs to the specified group or not.
     */
    public function hasGroup(string $name): bool;

    /**
     * Add a group to the user groups.
     */
    public function addGroup(GroupInterface $group);

    /**
     * Remove a group from the user groups.
     */
    public function removeGroup(GroupInterface $group);
}
