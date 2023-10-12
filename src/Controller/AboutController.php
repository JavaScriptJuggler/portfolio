<?php

namespace App\Controller;

use App\Entity\About;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
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

    // admin about page
    #[Route('/administrator/about', name: 'app_admin_about')]
    public function goToAdminAbout(EntityManagerInterface $entityManagerInterface): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $getData = $entityManagerInterface->getRepository(About::class)->findOneBy(['user_id' => $this->getUser()->getId()]);
        return $this->render('backend/about/about.html.twig', ["data" => $getData]);
    }

    // admin about save
    #[Route('/administrator/save-about', name: 'app_admin_about_save')]
    public function saveAdminAbout(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $checkData = $entityManagerInterface->getRepository(About::class)->findOneBy(['user_id' => $this->getUser()->getId()]);
        if (!$checkData)
            $checkData = new About;
        $imageFile = $request->files->get('image');
        $signatureFile = $request->files->get('signature');
        $imageFileName = md5(uniqid()) . '.' . $imageFile->guessExtension();
        $signatureFileName = md5(uniqid()) . '.' . $signatureFile->guessExtension();
        $imageFileId = $signatureFileId = '';
        if ($imageFile) {
            if ($imageFile instanceof UploadedFile) {
                // Ensure it's a valid file
                $content = file_get_contents($imageFile->getPathname());
                $mimeType = $imageFile->getMimeType();
                $existingFileName = $checkData ? $checkData->getImage() : "";
                $imageFileId = $this->uplaodFileToDrive($content, $mimeType, $imageFileName, $existingFileName);
            }
        }
        if ($signatureFile) {
            if ($signatureFile instanceof UploadedFile) {
                // Ensure it's a valid file
                $content = file_get_contents($signatureFile->getPathname());
                $mimeType = $signatureFile->getMimeType();
                $existingFileName = $checkData ? $checkData->getSignature() : "";
                $signatureFileId = $this->uplaodFileToDrive($content, $mimeType, $signatureFileName, $existingFileName);
            }
        }

        $checkData->setHeading($request->request->get('heading'));
        $checkData->setUserId($this->getUser()->getId());
        $checkData->setDescription($request->request->get('description'));
        if ($imageFile)
            $checkData->setImage($imageFileId);
        if ($signatureFile)
            $checkData->setSignature($signatureFileId);
        $entityManagerInterface->persist($checkData);
        try {
            $entityManagerInterface->flush();
            $this->addFlash('success', 'Changes saved successfully');
        } catch (\Throwable $th) {
            $this->addFlash('danger', 'Something went wrong');
        }
        return $this->redirectToRoute('app_admin_about');
    }
}
