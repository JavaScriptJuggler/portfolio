<?php

namespace App\Controller;

use App\Entity\Portfolio;
use App\Helper\commonHelper;
use Doctrine\ORM\EntityManagerInterface;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PortfolioController extends AbstractController
{
    #[Route('/portfolio', name: 'app_portfolio')]
    public function index(): Response
    {
        return $this->render('frontend/portfolio/index.html.twig', [
            'page_title' => 'Portfolio',
        ]);
    }

    #[Route('/portfolio/{slug}', name: 'app_portfolio_single')]
    public function singlePortfolio($slug): Response
    {
        return $this->render('frontend/portfolio/portfolio_single.html.twig', [
            'page_title' => $slug,
        ]);
    }

    #[Route('/administrator/portfolio-list', name: 'app_portfolio_list')]
    public function portfolioList(EntityManagerInterface $entityManagerInterface): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $getDatas = $entityManagerInterface->getRepository(Portfolio::class)->findBy(['user_id' => $this->getUser()->getId()]);
        return $this->render('backend/portfolio/portfolio.html.twig', [
            'portfolio' => $getDatas,
        ]);
    }

    #[Route('/administrator/add-portfolio', name: 'app_add_portfolio')]
    public function addPortfolio(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        if ($request->query->get('portfolio_id')) {
            $getData = $entityManagerInterface->getRepository(Portfolio::class)->findOneBy(['user_id' => $this->getUser()->getId(), 'id' => $request->query->get('portfolio_id')]);
            if ($getData) {
                return $this->render('backend/portfolio/add_portfolio.html.twig', [
                    "portfolio" => $getData,
                ]);
            }
        }
        return $this->render('backend/portfolio/add_portfolio.html.twig', []);
    }

    #[Route('/administrator/save-portfolio', name: 'app_save_portfolio')]
    public function savePortfolio(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        if ($request->request) {
            $inputs = $request->request->all();
            $file = $request->files->get('image');
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->getParameter('brochures_directory'), $fileName);
            $filePath = 'asset/' . $fileName;
            $checkData = '';
            $checkData = new Portfolio;
            if ($inputs['portfolio_number']) {
                $checkData = $entityManagerInterface->getRepository(Portfolio::class)->findOneBy(['user_id' => $this->getUser()->getId(), 'id' => $inputs['portfolio_number']]);
            }
            $checkData->setHeading($inputs['heading']);
            $checkData->setUserId($this->getUser()->getId());
            $checkData->setDescription($inputs['description']);
            $checkData->setTechnology($inputs['category']);
            $checkData->setCategory($inputs['technology']);
            $checkData->setSlug($inputs['slug']);
            $checkData->setImage($filePath);
            $entityManagerInterface->persist($checkData);
            $entityManagerInterface->flush();
            return $this->redirectToRoute('app_portfolio_list');
        }
    }

    #[Route('/administrator/delete-portfolio', name: 'app_delete_portfolio')]
    public function deletePortfolio(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $record = $entityManagerInterface->getRepository(Portfolio::class)->findOneBy(['user_id' => $this->getUser()->getId(), 'id' => $request->query->get('portfolio_id')]);
        $entityManagerInterface->remove($record);
        $entityManagerInterface->flush();
        return $this->redirectToRoute('app_portfolio_list');
    }

    #[Route('/google')]
    public function googleRedirect(): Response
    {
        $result = commonHelper::uploadImage();
        return new Response($result);
    }
    #[Route('/google/auth')]
    public function googleRedirectAuth(Request $request): Response
    {
        $client = new Google_Client();
        $client->setApplicationName('Your Symfony App');
        $client->setScopes([Google_Service_Drive::DRIVE]);
        $client->setClientId('982462715601-0s0id6ht324p71cjedjargtmepu4tr09.apps.googleusercontent.com');
        $client->setClientSecret('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');
        $client->setRedirectUri('http://localhost:8000/google/auth');
        $client->setAccessType('offline'); // Use 'offline' for long-lasting access
        $accessToken = $client->fetchAccessTokenWithAuthCode($request->query->get('code'));
        file_put_contents($this->getParameter('kernel.project_dir') . '/public/token.json', json_encode($accessToken));
        $result = commonHelper::uploadImage();
        // You can call a helper method or handle the OAuth token exchange logic here

        return new Response('Authorization Callback Completed'); // Replace with appropriate response
    }
}
