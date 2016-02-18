<?php

namespace GermBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('GermBundle:Default:index.html.twig');
    }

    public function debugAction()
    {
    	dump($this->get('pomm')
            ->getDatabase()
            ->getConnection()
            ->getMapFor('GermBundle\Model\Germ\PublicSchema\MemberModel')
            ->findAll());
    	die;
    }
}
