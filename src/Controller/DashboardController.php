<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends GlobalController
{
    #[Route('administrator/dashboard', name: 'app_admin_dashboard')]
    public function index(RequestStack $requestStack): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        return $this->render('backend/dashboard/index.html.twig', []);
    }
}
