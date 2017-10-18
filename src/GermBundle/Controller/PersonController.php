<?php

namespace GermBundle\Controller;

use PommProject\ModelManager\Model\Model;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use GermBundle\Model\Germ\PersonSchema\Person;
use GermBundle\Model\Germ\PersonSchema\Account;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Util\LegacyFormHelper;
use PommProject\Foundation\Where;

class PersonController extends Controller
{

    public function listAction(Request $request, $page, $search = null)
    {
        $finder = $this->get('GermBundle\Model\Germ\PersonSchema\PersonFinder');
        if ($request->get('_format') != 'html') {
            $output['persons'] = $finder->findAll();
        } else {
            $searcher = $this->get('GermBundle\Filter\Person\Searcher');
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

        $response = $this->render('GermBundle:Person:list.'.$request->get('_format').'.twig', $output);

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

    public function editAction(Request $request, $personSlug)
    {
        $accountModel = $this->get('pomm')['germ']->getModel('GermBundle\Model\Germ\PersonSchema\AccountModel');
        $person = $this->getPersonOr404($personSlug);

        $personForm = $this->buildPersonForm($person);
        $personForm->handleRequest($request);
        if ($personForm->isSubmitted() && $personForm->isValid()) {
            $personModel = $this->get('pomm')['germ']->getModel('GermBundle\Model\Germ\PersonSchema\PersonModel');
            $personModel->updateOne($person, array_keys($personForm->getData()->extract()));
            $this->get('session')->getFlashBag()->add('success', 'Person updated');
            return $this->redirectToRoute('germ_person_edit', ['personSlug' => $person->getSlugCanonical()]);
        }
        $personForm = $personForm->createView();

        $account = $accountModel->findWhere("person_id = $*", [$person->getId()])->current();
        if ($account) {
            if ($account->getEnabled()) {
                $accountForm = $this->buildAccountForm($account);
                $accountForm->handleRequest($request);
                if ($accountForm->isSubmitted() && $accountForm->isValid()) {
                    $accountModel->updateOne($account, array_keys($accountForm->getData()->extract()));
                    $this->get('session')->getFlashBag()->add('success', 'Person updated');
                    return $this->redirectToRoute('germ_person_edit', ['personSlug' => $person->getId()]);
                }
                $accountForm = $accountForm->createView();

                $passwordForm = $this->buildPasswordForm($account);
                $passwordForm->handleRequest($request);
                if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
                    $accountModel->updateOne($account, array_keys($passwordForm->getData()->extract()));
                    $this->get('session')->getFlashBag()->add('success', 'Person updated');
                    return $this->redirectToRoute('germ_person_edit', ['personSlug' => $person->getId()]);
                }
                $passwordForm = $passwordForm->createView();
            } else {
                $accountForm = null;
                $passwordForm = null;
            }
            $accountCreateForm = null;
        } else {
            $accountCreateForm = $this->buildAccountCreateForm();
            $accountCreateForm->handleRequest($request);
            if ($accountCreateForm->isSubmitted() && $accountCreateForm->isValid()) {
                $accountModel->insertOne($account, array_keys($accountCreateForm->getData()->extract()));
                $this->get('session')->getFlashBag()->add('success', 'Person updated');
                return $this->redirectToRoute('germ_person_edit', ['personSlug' => $person->getId()]);
            }
            $accountCreateForm = $accountCreateForm->createView();

            $accountForm = null;
            $passwordForm = null;
        }

        return $this->render(
            'GermBundle:Person:edit.html.twig',
            array(
                'mode' => 'edit',
                'form' => $personForm,
                'accountForm' => $accountForm,
                'passwordForm' => $passwordForm,
                'accountCreateForm' => $accountCreateForm,
            )
        );
    }

    public function showAction(Request $request, $personSlug)
    {
        $personModel = $this->get('pomm')['germ']
            ->getModel('GermBundle\Model\Germ\PersonSchema\PersonModel');
        $person = $personModel->findByPK(['id'=>$personSlug]);
        if (!$person) {
            throw $this->createNotFoundException('The person does not exist');
        }

        return $this->render(
            'GermBundle:Person:show.html.twig',
            array(
                'person' => $person,
            )
        );
    }

    public function createAction(Request $request)
    {
        $form = $this->buildPersonCreateForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $personModel = $this->get('pomm')['germ']->getModel('GermBundle\Model\Germ\PersonSchema\PersonModel');
            $person = new Person();
            foreach ($form->getData()->extract() as $key => $value) {
                $method = 'set'.ucfirst($key);
                $person->$method($value);
            }
            $personModel->insertOne($person);
            $translator = $this->get('translator');
            $this->get('session')->getFlashBag()->add('success', $translator->trans('Person created'));

            return $this->redirectToRoute('germ_person_edit', ['personSlug' => $person->getSlugCanonical()]);
        }

        return $this->render(
            'GermBundle:Person:edit.html.twig',
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
        /** @var Model $personModel */
        $personModel = $this->get('pomm')['germ']->getModel('GermBundle\Model\Germ\PersonSchema\PersonModel');
        $personModel->updateOne($person);
        $this->get('session')->getFlashBag()->add('success', 'Person deleted');

        return $this->redirectToRoute('germ_person_list');
    }

    public function recreateAction($personSlug)
    {
        $person = $this->getPersonOr404($personSlug);
        $person['is_deleted'] = false;
        /** @var Model $personModel */
        $personModel = $this->get('pomm')['germ']->getModel('GermBundle\Model\Germ\PersonSchema\PersonModel');
        $personModel->updateOne($person);
        $this->get('session')->getFlashBag()->add('success', 'Person created');

        return $this->redirectToRoute('germ_person_edit', ['personSlug' => $person->getSlugCanonical()]);
    }

    private function getPersonOr404($personSlug)
    {
        $finder = $this->get('GermBundle\Model\Germ\PersonSchema\PersonFinder');
        $person = $finder->findOneBySlug($personSlug);
        if (!$person) {
            throw $this->createNotFoundException('The person does not exist');
        }
        return $person;
    }

    private function getAccountOr404($personSlug)
    {
        $accountModel = $this->get('pomm')['germ']->getModel('GermBundle\Model\Germ\PersonSchema\AccountModel');
        $account = $accountModel->findWhere("person_id = $*", [$personSlug])->current();
        if (!$account) {
            throw $this->createNotFoundException('The account does not exist');
        }
        return $account;
    }

    public function accountActivationAction(Request $request, $personSlug, $enable)
    {
        $account = $this->getAccountOr404($personSlug);
        if ($account->getEnabled() === $enable) {
            throw $this->createNotFoundException('The account is already ' . ($enable ? 'enabled' : 'disabled'));
        }
        $account->setEnabled($enable);
        $accountModel = $this->get('pomm')['germ']->getModel('GermBundle\Model\Germ\PersonSchema\AccountModel');
        $accountModel->updateOne($account, ['enabled']);
        $this->get('session')->getFlashBag()->add(
            'success',
            'Account was ' . ($enable ? 'enabled' : 'disabled') . ' successuly'
        );
        return $this->redirectToRoute('germ_person_edit', ['personSlug' => $personSlug]);
    }

    private function buildPersonForm(Person $person, $isNew = false)
    {
        return $this->get('form.factory')
            ->createNamedBuilder('Person', FormType::class, $person)
            ->setAction($isNew ? $this->generateUrl('germ_person_create') : $this->generateUrl('germ_person_edit', ['personSlug' => $person->getSlugCanonical()]))
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('birthdate', TextType::class, [
                'required'  => false,
                'render_optional_text' => false,
            ])
            ->add('phone', CollectionType::class, array(
                'entry_type'    => TextType::class,
                'entry_options' => array(
                    'required'  => false,
                    'attr'      => array('class' => 'phone-number'),
                    'label'     => false,
                    'render_optional_text' => false,
                ),
                'allow_add'     =>true,
                'allow_delete'  =>true,
            ))
            ->add('address', TextareaType::class, array(
                'required' => false,
                'render_optional_text' => false,
            ))
            ->add('latlong', TextType::class, array(
                'required' => false,
                'render_optional_text' => false,
            ))
            ->add('email', EmailType::class, array(
                'required' => false,
                'render_optional_text' => false,
            ))
            ->add('roles', CollectionType::class, [
                'entry_type'   => ChoiceType::class,
                'entry_options'  => array(
                    'choices' => $this->get('GermBundle\Person\RoleManager')->getAssignable(),
                    'label'   => false,
                ),
                'allow_add'     =>true,
                'allow_delete'  =>true,
            ])
            ->getForm();
    }

    private function buildPersonCreateForm()
    {
        $authorizationChecker = $this->get('security.authorization_checker');
        if ($authorizationChecker->isGranted('ROLE_PERSON_CREATE')) {
            $person = new Person();
            $person->setFirstname('');
            $person->setLastname('');
            $person->setBirthdate('');
            $person->setPhone([]);
            $person->setAddress('');
            $person->setEmail('');
            $person->setLatlong('');
            $form = $this->buildPersonForm($person, true);

            return $form;
        }

        return null;
    }

    private function buildAccountForm(Account $account)
    {
        return $this->get('form.factory')
            ->createNamedBuilder('Account', FormType::class, $account)
            ->add('username', TextType::class, [
                'label' => 'form.username',
                'translation_domain' => 'FOSUserBundle'
            ])
            ->add('email', null, [
                'label' => 'form.email',
                'translation_domain' => 'FOSUserBundle',
                'required' => true,
            ])
            ->getForm();
    }

    private function buildAccountCreateForm(Account $account = null)
    {
        if (!$account) {
            $account = new Account();
            $account->setUsername('');
            $account->setEmail('');
        }
        return $this->get('form.factory')
            ->createNamedBuilder('Account', FormType::class, $account)
            ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
            ->add('email', null, array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
            ->add('plainPassword', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\RepeatedType'), array(
                'type' => LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\PasswordType'),
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.new_password'),
                'second_options' => array('label' => 'form.new_password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            ->getForm();
    }

    private function buildPasswordForm(Account $account)
    {
        return $this->get('form.factory')
            ->createNamedBuilder('Account', FormType::class, $account)
            ->add('plainPassword', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\RepeatedType'), array(
                'type' => LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\PasswordType'),
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.new_password'),
                'second_options' => array('label' => 'form.new_password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            ->getForm();
    }
}
