<?php

namespace GermBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
        $model = $this->get('pomm')['germ']
            ->getModel('GermBundle\Model\Germ\PublicSchema\MemberModel');

        $member = $model->findByPK(['id'=>$memberId]);

        // $where = $model->generateWhere();
        // $paginator  = $this->get('knp_paginator');
        // $members = $paginator->paginate(
        //     array($model, $where),
        //     $request->query->getInt('page', 1)+1,
        //     30
        // );

        return $this->render(
            'GermBundle:Member:edit.html.twig',
            array(
                'member' => $member,
            )
        );

    }
}
