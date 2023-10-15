<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends GlobalController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManagerInterface): Response
    {
        $connection = $entityManagerInterface->getConnection();
        $sql = "SELECT u.*,titleDescription.* from user u left join title_description titleDescription on u.id=titleDescription.user_id where u.id=1";
        $getUser = $connection->executeQuery($sql)->fetchAll();
        return $this->render('frontend/home/home.html.twig', [
            'page_title' => 'Home',
            'userData' => $getUser,
        ]);
    }
}
