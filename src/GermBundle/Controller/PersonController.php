<?php

namespace GermBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use GermBundle\Model\Germ\PublicSchema\Person;

class PersonController extends Controller
{

    public function listAction(Request $request)
    {
        $model = $this->get('pomm')['germ']
            ->getModel('GermBundle\Model\Germ\PublicSchema\PersonModel');

        $persons = $model->findAll();

        // $where = $model->generateWhere();
        // $paginator  = $this->get('knp_paginator');
        // $persons = $paginator->paginate(
        //     array($model, $where),
        //     $request->query->getInt('page', 1)+1,
        //     30
        // );

        return $this->render(
            'GermBundle:Person:list.html.twig',
            array(
                'persons' => $persons,
            )
        );

    }

    public function editAction(Request $request, $personId)
    {
        $personModel = $this->get('pomm')['germ']
            ->getModel('GermBundle\Model\Germ\PublicSchema\PersonModel');
        $person = $personModel->findByPK(['id'=>$personId]);
        if (!$person) {
            throw $this->createNotFoundException('The person does not exist');
        }
        $form = $this->buildForm($person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $personModel->updateOne($person, array_keys($form->getData()->extract()));
            $translator = $this->get('translator');
            $this->get('session')->getFlashBag()->add('success', $translator->trans('Person updated'));
            return $this->redirectToRoute('germ_person_edit', ['personId' => $person->getId()]);
        }

        return $this->render(
            'GermBundle:Person:edit.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    public function showAction(Request $request, $personId)
    {
        $personModel = $this->get('pomm')['germ']
            ->getModel('GermBundle\Model\Germ\PublicSchema\PersonModel');
        $person = $personModel->findByPK(['id'=>$personId]);
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
        $person = new Person();
        $person->setFirstname('');
        $person->setLastname('');
        $form = $this->buildForm($person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $personModel = $this->get('pomm')['germ']
                ->getModel('GermBundle\Model\Germ\PublicSchema\PersonModel');
            $personModel->insertOne($person, array_keys($form->getData()->extract()));
            $translator = $this->get('translator');
            $this->get('session')->getFlashBag()->add('success', $translator->trans('Person created'));
            return $this->redirectToRoute('germ_person_edit', ['personId' => $person->getId()]);
        }

        return $this->render(
            'GermBundle:Person:edit.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    private function buildForm(Person $person)
    {
        return $this->get('form.factory')
            ->createNamedBuilder('Person', FormType::class, $person)
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Save the person'))
            ->getForm();
    }
}
