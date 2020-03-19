<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Form\DataTransformer;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * Transforms between a User instance and a username string.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class UserToUsernameTransformer implements DataTransformerInterface
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * UserToUsernameTransformer constructor.
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Transforms a User instance into a username string.
     *
     * @param User|null $value User instance
     *
     * @return string|null Username
     *
     * @throws UnexpectedTypeException if the given value is not a User instance
     */
    public function transform($value): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof UserInterface) {
            throw new UnexpectedTypeException($value, UserInterface::class);
        }

        return $value->getUsername();
    }

    /**
     * Transforms a username string into a User instance.
     *
     * @param string $value Username
     *
     * @return UserInterface the corresponding User instance
     *
     * @throws UnexpectedTypeException if the given value is not a string
     */
    public function reverseTransform($value): ?User
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        return $this->userManager->findUserByUsername($value);
    }
}
