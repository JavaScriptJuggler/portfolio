<?php

namespace App\Controller;

use App\Entity\Phrase;
use App\Entity\TitleDescription;
use App\Form\PharseEntryFormType;
use App\Form\TitleDescriptionFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LandingController extends GlobalController
{
    #[Route('/administrator/landing', name: 'app_landing')]
    public function index(Request $request, EntityManagerInterface $enitityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $titleDescriptionForm = $this->createForm(TitleDescriptionFormType::class);
        $phraseForm = $this->createForm(PharseEntryFormType::class);

        // setting prefilled value
        $check = $enitityManager->getRepository(TitleDescription::class)->findOneBy(['user_id' => $this->getUser()->getId()]);
        $titleDescriptionForm->get('title')->setData($check->getTitle());
        $titleDescriptionForm->get('tagline')->setData($check->getTagline());

        $check1 = $enitityManager->getRepository(Phrase::class)->findOneBy(['user_id' => $this->getUser()->getId()]);
        $phraseForm->get('phrase_name')->setData($check1->getPhraseName());
        $phraseForm->get('phrase_description')->setData($check1->getPhraseDescription());


        // this should be always after preffling better do it before isSubbmitted if clause
        $titleDescriptionForm->handleRequest($request);
        $phraseForm->handleRequest($request);

        if ($titleDescriptionForm->isSubmitted() && $titleDescriptionForm->isValid()) {
            if (!$check) {
                $titleDescription = new TitleDescription();
                $titleDescription->setTitle($titleDescriptionForm->get('title')->getData());
                $titleDescription->setTagline($titleDescriptionForm->get('tagline')->getData());
                $titleDescription->setUserId($this->getUser()->getId());
                $enitityManager->persist($titleDescription);
                $enitityManager->flush();
                $this->addFlash('success', 'Title Or Description Inserted Sucessfully');
                return $this->redirectToRoute('app_landing');
            } else {
                $check->setTitle($titleDescriptionForm->get('title')->getData());
                $check->setTagline($titleDescriptionForm->get('tagline')->getData());
                $check->setUserId($this->getUser()->getId());
                $enitityManager->persist($check);
                $enitityManager->flush();
                $this->addFlash('success', 'Title Or Description Updated Sucessfully');
                return $this->redirectToRoute('app_landing');
            }
        }
        if ($phraseForm->isSubmitted() && $phraseForm->isValid()) {
            if (!$check1) {
                $pharseAdder = new Phrase();
                $pharseAdder->setPhraseName($phraseForm->get('phrase_name')->getData());
                $pharseAdder->setPhraseDescription($phraseForm->get('phrase_description')->getData());
                $pharseAdder->setUserId($this->getUser()->getId());
                $enitityManager->persist($pharseAdder);
                $enitityManager->flush();
                $this->addFlash('success', 'Phrase inserted Successfully');
                return $this->redirectToRoute('app_landing');
            } else {
                $check1->setPhraseName($phraseForm->get('phrase_name')->getData());
                $check1->setPhraseDescription($phraseForm->get('phrase_description')->getData());
                $check1->setUserId($this->getUser()->getId());
                $enitityManager->persist($check1);
                $enitityManager->flush();
                $this->addFlash('success', 'Phrase Updated Successfully');
                return $this->redirectToRoute('app_landing');
            }
        }
        return $this->render('backend/landing/index.html.twig', [
            'titleDescriptionForm' => $titleDescriptionForm,
            'phraseForm' => $phraseForm,
        ]);
    }
}
