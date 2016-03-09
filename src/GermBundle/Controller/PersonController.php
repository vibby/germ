<?php

namespace GermBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GermBundle\Form\Type\PersonFormType;
use GermBundle\Form\Type\ChangePasswordFormType;
use GermBundle\Form\Type\AccountFormType;
use GermBundle\Entity\Person;

class PersonController extends Controller
{

    public function listAction(Request $request)
    {
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT p FROM GermBundle:Person p";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            2
        );

        return $this->render(
            'GermBundle:Person:list.html.twig',
            array(
                'pagination' => $pagination,
            )
        );
    }

    public function editAction(Request $request, $personId)
    {
        $person = $this->getPersonOr404($personId);
        $form = $this->createForm(PersonFormType::class, $person);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $passwordForm = null;
        $accountForm = null;
        $accountCreateForm = null;
        if ($person->getAccount() && $person->getAccount()->isEnabled() === true) {
            $passwordForm = $this->createForm(ChangePasswordFormType::class, $person->getAccount());
            $accountForm = $this->createForm(AccountFormType::class, $person->getAccount());
        } elseif (!$person->getAccount()) {
            $accountCreateForm = $this->createForm(AccountFormType::class);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $person = $form->getData();

            $em->persist($person);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                'Person was edited successuly'
            );

            return $this->redirectToRoute('germ_person_edit', ['personId' => $person->getId()]);
        }

        return $this->render(
            'GermBundle:Person:edit.html.twig',
            array(
                'form' => $form->createView(),
                'passwordForm' => $passwordForm ? $passwordForm->createView() : null,
                'accountForm' => $accountForm ? $accountForm->createView() : null,
                'accountCreateForm' => $accountCreateForm ? $accountCreateForm->createView() : null,
            )
        );
    }

    public function removeAction(Request $request, $personId)
    {
        $person = $this->getPersonOr404($personId);
        if ($request->getMethod() !== 'POST') {
            return $this->render(
                'GermBundle:Person:remove.html.twig',
                array(
                    'person' => $person,
                )
            );
        } else {
            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($person);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                'Person was removed successuly'
            );
            return $this->redirectToRoute('germ_person_list');
        }
    }

    public function showAction(Request $request, $personId)
    {
        $person = $this->getPersonOr404($personId);

        return $this->render(
            'GermBundle:Person:show.html.twig',
            array(
                'person' => $person,
            )
        );
    }

    private function getPersonOr404($personId)
    {
        $em = $this->getDoctrine()->getManager();
        $person = $this->getDoctrine()
            ->getRepository('GermBundle:Person')
            ->find($personId);
        if (!$person) {
            throw $this->createNotFoundException('The person does not exist');
        }
        return $person;
    }

    public function createAction(Request $request)
    {
        $person = new Person;
        $form = $this->createForm(PersonFormType::class, $person);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $person = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($person);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                'Person was created successuly'
            );

            return $this->redirectToRoute('germ_person_edit', ['personId' => $person->getId()]);
        }

        return $this->render(
            'GermBundle:Person:create.html.twig',
            array(
                'form' => $form->createView(),
                'accountForm' => null,
            )
        );
    }

    public function accountActivationAction(Request $request, $personId, $enable)
    {
        $person = $this->getPersonOr404($personId);
        $account = $person->getAccount();
        if (!$account) {
            throw $this->createNotFoundException('The account does not exist');
        }
        if ($account->isEnabled() === $enable) {
            throw $this->createNotFoundException('The account is already ' . ($enable ? 'enabled' : 'disabled'));
        }
        $account->setEnabled($enable);

        $em = $this->getDoctrine()->getManager();
        $em->persist($account);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',
            'Account was ' . ($enable ? 'enabled' : 'disabled') . ' successuly'
        );

        return $this->redirectToRoute('germ_person_edit', ['personId' => $person->getId()]);
    }
}
