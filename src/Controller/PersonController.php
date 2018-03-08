<?php

namespace Germ\Controller;

use Germ\Type\AccountType;
use Germ\Type\PersonType;
use PommProject\ModelManager\Model\Model;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PersonController extends Controller
{

    public function listAction(Request $request, $page, $search = null)
    {
        $finder = $this->get('Germ\Model\Germ\Person\PersonFinder');
        if ($request->get('_format') != 'html') {
            $output['persons'] = $finder->findForListWhere();
        } else {
            $searcher = $this->get('Germ\Filter\Person\Searcher');
            if ($redirect = $searcher->handleRequest($request)) {
                return $redirect;
            }
            $output['searchForm'] = $searcher->getForm()->createView();
            $paginator = $this->get('knp_paginator');
            $output['paginatedPersons'] = $paginator->paginate(
                [
                    $finder,
                    'paginateFilterQuery',
                    [$searcher],
                ],
                $page,
                min((int) $request->get('perPage', 30), 250)
            );
        }

        $response = $this->render('Germ:Person:list.'.$request->get('_format').'.twig', $output);

        if ($request->get('_format') != 'html') {
            $response->headers->set(
                'Content-Disposition',
                sprintf('attachment; filename="persons.%s";"', $request->get('_format'))
            );
            $response->headers->set(
                'Content-Type',
                sprintf('Content-Type="text/%s";', $request->get('_format'))
            );
        }

        return $response;
    }

    public function editAction(Request $request, $personSlug = null)
    {
        if (!$personSlug) {
            $personSlug = $this->getUser()->getUsername();
        }
        $accountModel = $this->get('pomm')['germ']->getModel('Germ\Model\Germ\PersonSchema\AccountModel');
        $person = $this->getPersonOr404($personSlug);

        $personForm = $this->get('form.factory')->create(PersonType::class, $person);
        $personForm->handleRequest($request);
        if ($personForm->isSubmitted() && $personForm->isValid()) {
            $personSaver = $this->get('Germ\Model\Germ\Person\PersonSaver');
            $personSaver->update($person);
            $this->get('session')->getFlashBag()->add('success', 'Person updated');

            return $this->redirectToRoute('germ_person_edit', ['personSlug' => $person->getSlug()]);
        }
        $personForm = $personForm->createView();

        $account = $accountModel->findWhere("person_id = $*", [$person->getId()])->current();
        $accountForm = $this->get('form.factory')->create(AccountType::class, $account, ['person' => $person]);
        $accountForm->handleRequest($request);
        if ($accountForm->isSubmitted() && $accountForm->isValid()) {
            $saver = $this->get('Germ\Model\Germ\Person\AccountSaver');
            $saver->insertOrUpdate($accountForm->getData(), $person);
            $accountRequest = $request->request->get('account');
            if (isset($accountRequest['sendEmail'])) {
                $emailer = $this->get('Germ\Email\Mailer');
                $emailer->sendEmailChangePassword(
                    $person,
                    $accountRequest['plainPassword']['first']
                );
            }
            $this->get('session')->getFlashBag()->add('success', 'Person updated');

            return $this->redirectToRoute('germ_person_edit', ['personSlug' => $person->getSlug()]);
        }

        return $this->render(
            'Germ:Person:edit.html.twig',
            array(
                'mode' => 'edit',
                'form' => $personForm,
                'accountForm' => $accountForm->createView(),
                'account' => $account,
                'currentAccount' => $this->getUser()
            )
        );
    }

    public function showAction($personSlug)
    {
        $person = $this->getPersonOr404($personSlug);

        return $this->render(
            'Germ:Person:show.html.twig',
            array(
                'person' => $person,
            )
        );
    }

    public function createAction(Request $request)
    {
        $form = $this->get('form.factory')->create(PersonType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $person = $this->get('Germ\Model\Germ\Person\PersonSaver')->create($form->getData());
            $translator = $this->get('translator');
            $this->get('session')->getFlashBag()->add('success', $translator->trans('Person created'));

            return $this->redirectToRoute('germ_person_edit', ['personSlug' => $person->getSlug()]);
        }

        return $this->render(
            'Germ:Person:edit.html.twig',
            array(
                'mode' => 'create',
                'form' => $form->createView(),
            )
        );
    }

    public function removeAction($personSlug)
    {
        $person = $this->getPersonOr404($personSlug);
        $person['is_deleted'] = true;
        if ($this->getUser()['person_id'] === $person['id']) {
            $this->get('session')->getFlashBag()->add('error', 'You cannot remove yourself');

            return $this->redirectToRoute('germ_person_list');
        }
        /** @var Model $personModel */
        $personModel = $this->get('pomm')['germ']->getModel('Germ\Model\Germ\PersonSchema\PersonModel');
        $personModel->updateOne($person);
        $this->get('session')->getFlashBag()->add('success', 'Person deleted');

        return $this->redirectToRoute('germ_person_list');
    }

    public function recreateAction($personSlug)
    {
        $person = $this->getPersonOr404($personSlug);
        $person['is_deleted'] = false;
        /** @var Model $personModel */
        $personModel = $this->get('pomm')['germ']->getModel('Germ\Model\Germ\PersonSchema\PersonModel');
        $personModel->updateOne($person);
        $this->get('session')->getFlashBag()->add('success', 'Person created');

        return $this->redirectToRoute('germ_person_edit', ['personSlug' => $person->getSlug()]);
    }

    private function getPersonOr404($personSlug)
    {
        $finder = $this->get('Germ\Model\Germ\Person\PersonFinder');
        $person = $finder->findOneBySlug($personSlug);
        if (!$person) {
            throw $this->createNotFoundException('The person does not exist');
        }
        return $person;
    }

    private function getAccountOr404($personSlug)
    {
        $person = $this->getPersonOr404($personSlug);
        $accountModel = $this->get('pomm')['germ']->getModel('Germ\Model\Germ\PersonSchema\AccountModel');
        $account = $accountModel->findWhere("person_id = $*", [$person->getId()])->current();
        if (!$account) {
            throw $this->createNotFoundException('The account does not exist');
        }
        return $account;
    }

    public function accountActivationAction($personSlug, $enable)
    {
        $account = $this->getAccountOr404($personSlug);
        $redirectRoute = $this->redirectToRoute('germ_person_edit', ['personSlug' => $personSlug]);
        if ($account->getEnabled() === $enable) {
            $this->get('session')->getFlashBag()->add('error', 'The account is already ' . ($enable ? 'enabled' : 'disabled'));

            return $redirectRoute;
        }
        if ($this->getUser()['id'] === $account['id']) {
            $this->get('session')->getFlashBag()->add('error', 'You cannot activate or deactivate yourself');

            return $redirectRoute;
        }
        $account->setEnabled($enable);
        $accountModel = $this->get('pomm')['germ']->getModel('Germ\Model\Germ\PersonSchema\AccountModel');
        $accountModel->updateOne($account, ['enabled']);
        $this->get('session')->getFlashBag()->add(
            'success',
            'Account was ' . ($enable ? 'enabled' : 'disabled') . ' successuly'
        );
        return $redirectRoute;
    }
}
