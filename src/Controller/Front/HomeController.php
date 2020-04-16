<?php

namespace App\controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="front_home_index")
     */
    public function index()
    {
        return $this->render('');
    }
}
