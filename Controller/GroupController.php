<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Controller;

use FOS\UserBundle\Event\FilterGroupResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseGroupEvent;
use FOS\UserBundle\Event\GroupEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\GroupManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * RESTful controller managing group CRUD.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 *
 * @final
 */
class GroupController extends AbstractController
{
    private $eventDispatcher;
    private $formFactory;
    private $groupManager;

    public function __construct(EventDispatcherInterface $eventDispatcher, FactoryInterface $formFactory, GroupManagerInterface $groupManager)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->formFactory = $formFactory;
        $this->groupManager = $groupManager;
    }

    public function listAction(): Response
    {
        return $this->render('@FOSUser/Group/list.html.twig', [
            'groups' => $this->groupManager->findGroups(),
        ]);
    }

    public function showAction(string $groupName): Response
    {
        return $this->render('@FOSUser/Group/show.html.twig', [
            'group' => $this->findGroupBy('name', $groupName),
        ]);
    }

    public function editAction(Request $request, string $groupName): Response
    {
        $group = $this->findGroupBy('name', $groupName);

        $event = new GetResponseGroupEvent($group, $request);
        $this->eventDispatcher->dispatch($event, FOSUserEvents::GROUP_EDIT_INITIALIZE);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->formFactory->createForm();
        $form->setData($group);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = new FormEvent($form, $request);
            $this->eventDispatcher->dispatch($event, FOSUserEvents::GROUP_EDIT_SUCCESS);

            $this->groupManager->updateGroup($group);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_group_show', ['groupName' => $group->getName()]);
                $response = new RedirectResponse($url);
            }

            $this->eventDispatcher->dispatch(new FilterGroupResponseEvent($group, $request, $response), FOSUserEvents::GROUP_EDIT_COMPLETED);

            return $response;
        }

        return $this->render('@FOSUser/Group/edit.html.twig', [
            'form' => $form->createView(),
            'group_name' => $group->getName(),
        ]);
    }

    public function newAction(Request $request): Response
    {
        $group = $this->groupManager->createGroup('');

        $this->eventDispatcher->dispatch(new GroupEvent($group, $request), FOSUserEvents::GROUP_CREATE_INITIALIZE);

        $form = $this->formFactory->createForm();
        $form->setData($group);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = new FormEvent($form, $request);
            $this->eventDispatcher->dispatch($event, FOSUserEvents::GROUP_CREATE_SUCCESS);

            $this->groupManager->updateGroup($group);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_group_show', ['groupName' => $group->getName()]);
                $response = new RedirectResponse($url);
            }

            $this->eventDispatcher->dispatch(new FilterGroupResponseEvent($group, $request, $response), FOSUserEvents::GROUP_CREATE_COMPLETED);

            return $response;
        }

        return $this->render('@FOSUser/Group/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function deleteAction(Request $request, string $groupName): RedirectResponse
    {
        $group = $this->findGroupBy('name', $groupName);
        $this->groupManager->deleteGroup($group);

        $response = new RedirectResponse($this->generateUrl('fos_user_group_list'));

        $this->eventDispatcher->dispatch(new FilterGroupResponseEvent($group, $request, $response), FOSUserEvents::GROUP_DELETE_COMPLETED);

        return $response;
    }

    /** Find a group by a specific property. */
    protected function findGroupBy(string $key, $value): GroupInterface
    {
        if (!empty($value)) {
            $group = $this->groupManager->{'findGroupBy'.ucfirst($key)}($value);
        }

        if (empty($group)) {
            throw new NotFoundHttpException(sprintf('The group with "%s" does not exist for value "%s"', $key, $value));
        }

        return $group;
    }
}
