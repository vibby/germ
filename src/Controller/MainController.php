<?php

namespace Germ\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    public function indexAction()
    {
        return $this->render('Germ:Main:index.html.twig');
    }
}
