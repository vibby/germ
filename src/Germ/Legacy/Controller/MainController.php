<?php

namespace Germ\Legacy\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    public function indexAction()
    {
        return $this->render('Main/index.html.twig');
    }
}
