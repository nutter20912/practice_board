<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class DefaultController extends AbstractController
{
    /**
     * @Route("/board")
     * @Method("GET")
     */
    public function board(): Response
    {
        return $this->render('board.html.twig');
    }

    /**
     * @Route("/user")
     * @Method("GET")
     */
    public function user(): Response
    {
        return $this->render('user.html.twig');
    }
}
