<?php

namespace Germ\Controller;

use Germ\Email\Mailer;
use Germ\Filter\Person\Searcher;
use Germ\Model\Germ\Person\AccountSaver;
use Germ\Model\Germ\Person\PersonFinder;
use Germ\Model\Germ\Person\PersonSaver;
use Germ\Model\Germ\PersonSchema\AccountModel;
use Germ\Model\Germ\PersonSchema\PersonModel;
use Germ\Type\AccountType;
use Germ\Type\PersonType;
use Knp\Component\Pager\PaginatorInterface;
use PommProject\Foundation\Pomm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class PersonController extends AbstractController
{
    private $finder;
    private $searcher;
    private $saver;
    private $accountSaver;
    private $personModel;
    private $accountModel;

    public function __construct(PersonFinder $finder, Searcher $searcher, PersonSaver $saver, AccountSaver $accountSaver, Pomm $pomm)
    {
        $this->finder = $finder;
        $this->searcher = $searcher;
        $this->saver = $saver;
        $this->accountSaver = $accountSaver;
        $this->personModel = $pomm['germ']->getModel(PersonModel::class);
        $this->accountModel = $pomm['germ']->getModel(AccountModel::class);
    }

    public function listAction(Request $request, $page, PaginatorInterface $paginator)
    {
        if ('html' != $request->get('_format')) {
            $output['persons'] = $this->finder->findForListWhere();
        } else {
            if ($redirect = $this->searcher->handleRequest($request)) {
                return $redirect;
            }
            $output['searchForm'] = $this->searcher->getForm()->createView();
            $output['paginatedPersons'] = $paginator->paginate(
                [
                    $this->finder,
                    'paginateFilterQuery',
                    [$this->searcher],
                ],
                $page,
                min((int) $request->get('perPage', 30), 250)
            );
        }

        $response = $this->render('Person/list.'.$request->get('_format').'.twig', $output);

        if ('html' != $request->get('_format')) {
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

    public function editAction(Request $request, Mailer $emailer, $personSlug = null)
    {
        if (! $personSlug) {
            $personSlug = $this->getUser()->getUsername();
        }
        $person = $this->getPersonOr404($personSlug);

        $personForm = $this->createForm(PersonType::class, $person);
        $personForm->handleRequest($request);
        if ($personForm->isSubmitted() && $personForm->isValid()) {
            $this->saver->update($person);
            $request->getSession()->getFlashBag()->add('success', 'Person updated');

            return $this->redirectToRoute('germ_person_edit', ['personSlug' => $person->getSlug()]);
        }
        $personForm = $personForm->createView();

        $account = $this->accountModel->findWhere('person_id = $*', [$person->getId()])->current();
        $accountForm = $this->get('form.factory')->create(AccountType::class, $account, ['person' => $person]);
        $accountForm->handleRequest($request);
        if ($accountForm->isSubmitted() && $accountForm->isValid()) {
            $this->accountSaver->insertOrUpdate($accountForm->getData(), $person);
            $accountRequest = $request->request->get('account');
            if (isset($accountRequest['sendEmail'])) {
                $emailer->sendEmailChangePassword(
                    $person,
                    $accountRequest['plainPassword']['first']
                );
            }
            $request->getSession()->getFlashBag()->add('success', 'Person updated');

            return $this->redirectToRoute('germ_person_edit', ['personSlug' => $person->getSlug()]);
        }

        return $this->render(
            'Person/edit.html.twig',
            [
                'mode' => 'edit',
                'form' => $personForm,
                'accountForm' => $accountForm->createView(),
                'account' => $account,
                'currentAccount' => $this->getUser(),
            ]
        );
    }

    public function showAction($personSlug)
    {
        $person = $this->getPersonOr404($personSlug);

        return $this->render(
            'Person/show.html.twig',
            [
                'person' => $person,
            ]
        );
    }

    public function createAction(Request $request, TranslatorInterface $translator)
    {
        $form = $this->createForm(PersonType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $person = $this->saver->create($form->getData());
            $request->getSession()->getFlashBag()->add('success', $translator->trans('Person created'));

            return $this->redirectToRoute('germ_person_edit', ['personSlug' => $person->getSlug()]);
        }

        return $this->render(
            'Person/edit.html.twig',
            [
                'mode' => 'create',
                'form' => $form->createView(),
            ]
        );
    }

    public function removeAction($personSlug)
    {
        $person = $this->getPersonOr404($personSlug);
        $person['is_deleted'] = true;
        if ($this->getUser()['person_id'] === $person['id']) {
            $request->getSession()->getFlashBag()->add('error', 'You cannot remove yourself');

            return $this->redirectToRoute('germ_person_list');
        }
        $this->personModel->updateOne($person);
        $request->getSession()->getFlashBag()->add('success', 'Person deleted');

        return $this->redirectToRoute('germ_person_list');
    }

    public function recreateAction($personSlug)
    {
        $person = $this->getPersonOr404($personSlug);
        $person['is_deleted'] = false;
        $this->personModel->updateOne($person);
        $request->getSession()->getFlashBag()->add('success', 'Person created');

        return $this->redirectToRoute('germ_person_edit', ['personSlug' => $person->getSlug()]);
    }

    private function getPersonOr404($personSlug)
    {
        $person = $this->finder->findOneBySlug($personSlug);
        if (! $person) {
            throw $this->createNotFoundException('The person does not exist');
        }

        return $person;
    }

    private function getAccountOr404($personSlug)
    {
        $person = $this->getPersonOr404($personSlug);
        $account = $this->accountModel->findWhere('person_id = $*', [$person->getId()])->current();
        if (! $account) {
            throw $this->createNotFoundException('The account does not exist');
        }

        return $account;
    }

    public function accountActivationAction($personSlug, $enable, Request $request)
    {
        $account = $this->getAccountOr404($personSlug);
        $redirectRoute = $this->redirectToRoute('germ_person_edit', ['personSlug' => $personSlug]);
        if ($account->getEnabled() === $enable) {
            $request->getSession()->getFlashBag()->add('error', 'The account is already '.($enable ? 'enabled' : 'disabled'));

            return $redirectRoute;
        }
        if ($this->getUser()['id'] === $account['id']) {
            $request->getSession()->getFlashBag()->add('error', 'You cannot activate or deactivate yourself');

            return $redirectRoute;
        }
        $account->setEnabled($enable);
        $this->accountModel->updateOne($account, ['enabled']);
        $request->getSession()->getFlashBag()->add(
            'success',
            'Account was '.($enable ? 'enabled' : 'disabled').' successuly'
        );

        return $redirectRoute;
    }
}
