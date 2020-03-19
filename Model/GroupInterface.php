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
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
interface GroupInterface
{
    public function addRole(string $role);

    /**
     * @return mixed
     */
    public function getId();

    public function getName(): string;

    public function hasRole(string $role): bool;

    public function getRoles(): array;

    public function removeRole(string $role);

    public function setName(string $name);

    public function setRoles(array $roles);
}
