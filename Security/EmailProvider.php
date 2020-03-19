<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Security;

use FOS\UserBundle\Model\User;

class EmailProvider extends UserProvider
{
    /**
     * {@inheritdoc}
     */
    protected function findUser($username): ?User
    {
        return $this->userManager->findUserByEmail($username);
    }
}
