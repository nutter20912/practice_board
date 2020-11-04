<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class DefaultController extends AbstractController
{
    /**
     * @Route("/")
     * @Method("GET")
     */
    public function index(): Response
    {
        return $this->render('base.html.twig');
    }
}
