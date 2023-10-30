<?php

namespace App\Controller;

use App\Entity\Portfolio;
use App\Helper\commonHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PortfolioController extends GlobalController
{
    #[Route('/portfolio', name: 'app_portfolio')]
    public function index(): Response
    {
        return $this->render('frontend/portfolio/index.html.twig', [
            'page_title' => 'Portfolio',
        ]);
    }

    #[Route('/portfolio/{slug}', name: 'app_portfolio_single')]
    public function singlePortfolio($slug, EntityManagerInterface $entityManagerInterface): Response
    {
        $portfolioDetails = $entityManagerInterface->getRepository(Portfolio::class)->findOneBy(['user_id' => 1, 'slug' => $slug]);
        return $this->render('frontend/portfolio/portfolio_single.html.twig', [
            'page_title' => $slug,
            "portfolio" => $portfolioDetails,
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
            $fileId = $checkData = $existingFileName = '';
            $checkData = new Portfolio;
            if ($inputs['portfolio_number']) {
                $checkData = $entityManagerInterface->getRepository(Portfolio::class)->findOneBy(['user_id' => $this->getUser()->getId(), 'id' => $inputs['portfolio_number']]);
                $existingFileName = $checkData->getImage();
            }
            if ($file) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                if ($file instanceof UploadedFile) {
                    // Ensure it's a valid file
                    $content = file_get_contents($file->getPathname());
                    $mimeType = $file->getMimeType();
                    $fileId = $this->uplaodFileToDrive($content, $mimeType, $fileName, $existingFileName);
                }
            }
            $checkData->setHeading($inputs['heading']);
            $checkData->setUserId($this->getUser()->getId());
            $checkData->setDescription($inputs['description']);
            $checkData->setTechnology($inputs['category']);
            $checkData->setCategory($inputs['technology']);
            $checkData->setSlug($inputs['slug']);
            if ($fileId != '')
                $checkData->setImage($fileId);
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
}
