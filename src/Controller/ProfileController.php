<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PasswordChangeFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProfileController extends GlobalController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/administrator/profile', name: 'app_profile', methods: ['GET', 'POST'])]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $success = false;
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $user = $this->entityManager->getRepository(User::class)->find($this->getUser()->getId());
        $form = $this->createForm(PasswordChangeFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($passwordHasher->isPasswordValid($user, $form->get('oldPassword')->getData())) {
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $form->get('newPassword')->getData()
                );
                $user->setPassword($hashedPassword);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $success = true;
                // Inside your form processing controller
                $this->addFlash('success', 'Successfully Changed Password');
                // Redirect to a new page or back to the form page for avoid resubmission on refresh
                return $this->redirectToRoute('app_profile');
            }
        }
        return $this->render('backend/profile/profile.html.twig', [
            'username' => $this->getUser()->getName(),
            'email' => $this->getUser()->getEmail(),
            'passwordChangeForm' => $form->createView(),

        ]);
    }

    #[Route('/administrator/save-profile-details', name: 'app_save_profile', methods: 'POST')]
    public function saveProfileDetails(Request $request, PersistenceManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $requests = $request->request;
        $file = $request->files->get('image');
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();
        $fileId = '';
        if ($file) {
            if ($file instanceof UploadedFile) {
                // Ensure it's a valid file
                $content = file_get_contents($file->getPathname());
                $mimeType = $file->getMimeType();
                $fileId = $this->uplaodFileToDrive($content, $mimeType, $fileName, $this->getUser()->getImage() ?? '');
            }
        }
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->find($this->getUser()->getId());
        $user->setEmail($requests->get('email'));
        $user->setName($requests->get('name'));
        $user->setImage($fileId);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        // return $this->redirect($this->generateUrl('app_profile'));
        return $this->redirectToRoute('app_profile');
    }

    /* uplaod resume pdf */
    #[Route('/administrator/upload-resume', name: 'app_save_resume', methods: 'POST')]
    public function uplaodResumePdf(Request $request, EntityManagerInterface $entityManagerInterface)
    {
        $checkIfDataExist = $entityManagerInterface->getRepository(User::class)->findOneBy(['id' => $this->getUser()->getId()]);
        $file = $request->files->get('resume');

        $fileName = md5(uniqid()) . '.' . $file->guessExtension();
        $fileId = '';
        if ($file instanceof UploadedFile) {
            // Ensure it's a valid file
            $content = file_get_contents($file->getPathname());
            $mimeType = $file->getMimeType();
            $fileId = $this->uplaodFileToDrive($content, $mimeType, $fileName, $this->getUser()->getResume() ?? '');
        }
        $checkIfDataExist->setResume($fileId);
        $entityManagerInterface->persist($checkIfDataExist);
        try {
            $entityManagerInterface->flush();
            $success = true;
        } catch (\Throwable $th) {
            $success = false;
        }
        /* if we set flash it will automatically store to session and can access in template like i did */
        if ($success)
            $this->addFlash('success', 'Resume Upoaded & Saved Successfully');
        else
            $this->addFlash('danger', 'Something Went Wrong');
        return $this->redirectToRoute('app_profile');
    }
}
