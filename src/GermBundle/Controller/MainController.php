<?php

namespace GermBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    public function indexAction()
    {
        return $this->render('GermBundle:Main:index.html.twig');
    }
}
