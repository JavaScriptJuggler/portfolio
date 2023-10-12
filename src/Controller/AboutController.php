<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AboutController extends GlobalController
{
    #[Route('/about', name: 'app_about')]
    public function index(): Response
    {
        return $this->render('frontend/about/index.html.twig', [
            'page_title' => 'About',
        ]);
    }
}
