<?php

namespace GermBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use GermBundle\Model\Germ\PublicSchema\Member;

class MemberController extends Controller
{
    public function listAction(Request $request)
    {
        $model = $this->get('pomm')['germ']
            ->getModel('GermBundle\Model\Germ\PublicSchema\MemberModel');

        $members = $model->findAll();

        // $where = $model->generateWhere();
        // $paginator  = $this->get('knp_paginator');
        // $members = $paginator->paginate(
        //     array($model, $where),
        //     $request->query->getInt('page', 1)+1,
        //     30
        // );

        return $this->render(
            'GermBundle:Member:list.html.twig',
            array(
                'members' => $members,
            )
        );

    }

    public function editAction($memberId)
    {
        $member = $this->get('pomm')['germ']
            ->getModel('GermBundle\Model\Germ\PublicSchema\MemberModel')
            ->findByPK(['id'=>$memberId]);

        return $this->render(
            'GermBundle:Member:edit.html.twig',
            array(
                'form' => $this->buildForm($member)->createView(),
            )
        );
    }

    public function createAction()
    {
        $member = new Member();
        $member->setFirstname('');
        $member->setLastname('');

        return $this->render(
            'GermBundle:Member:edit.html.twig',
            array(
                'form' => $this->buildForm($member)->createView(),
            )
        );
    }

    private function buildForm(Member $member)
    {
        return $this->createFormBuilder($member)
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Save the member'))
            ->getForm();
    }
}
