<?php

namespace App\Controller;

use App\Entity\ExperienceOverView;
use App\Entity\OfficeExperiences;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExperienceController extends AbstractController
{
    #[Route('/administrator/experience', name: 'app_experience')]
    public function index(EntityManagerInterface $entityManagerInterface): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $data = $entityManagerInterface->getRepository(ExperienceOverView::class)->findOneBy(['user_id' => $this->getUser()->getId()]);
        $officeData = $entityManagerInterface->getRepository(OfficeExperiences::class)->findBy(['user_id' => $this->getUser()->getId()]);
        $officeDataArr = [];
        if ($officeData) {
            foreach ($officeData as $key => $value) {
                $officeDataArr[] = [
                    'companyname' => $value->getOfficeName(),
                    'designation' => $value->getDesignation(),
                    'years' => $value->getYears(),
                    'location' => $value->getLocation(),
                ];
            }
        }
        return $this->render('backend/experience/experience.html.twig', [
            'data' => $data,
            'officeDataArr' => json_encode($officeDataArr)
        ]);
    }


    #[Route('/administrator/save-experience-phrase', name: 'app_experience_save_phrase')]
    public function saveExperiencePhrase(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $inputs = $request->request->all();
        $manager = $entityManagerInterface->getRepository(ExperienceOverView::class);
        $getData = $manager->findOneBy(['user_id' => $this->getUser()->getId()]);
        if ($getData) {
            $getData->setExperienceHeading($inputs['heading']);
            $getData->setExperienceDescription($inputs['description']);
            $getData->setTotalProjects($inputs['totalProjects']);
            $getData->setUserId($this->getUser()->getId());
        } else {
            $getData = new ExperienceOverView();
            $getData->setExperienceHeading($inputs['heading']);
            $getData->setExperienceDescription($inputs['description']);
            $getData->setTotalProjects($inputs['totalProjects']);
            $getData->setUserId($this->getUser()->getId());
        }
        $entityManagerInterface->persist($getData);
        $entityManagerInterface->flush();
        $this->addFlash('success', "Changes updated successfully");
        return $this->redirectToRoute('app_experience');
    }
    #[Route('/administrator/save-experience-offices', name: 'app_experience_save_offices')]
    public function saveExperienceOffices(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $inputs = $request->request->all();
        $inputs = $inputs['group-a'];
        $check = $entityManagerInterface->getRepository(OfficeExperiences::class);
        if ($check) {
            $connection = $entityManagerInterface->getConnection();
            $stmt = $connection->prepare("truncate table office_experiences");
            $stmt->executeQuery();
        }
        foreach ($inputs as $key => $value) {
            $officeExperiences = new OfficeExperiences;
            $officeExperiences->setUserId($this->getUser()->getId());
            $officeExperiences->setOfficeName($value['companyname']);
            $officeExperiences->setDesignation($value['designation']);
            $officeExperiences->setYears($value['years']);
            $officeExperiences->setLocation($value['location']);
            $entityManagerInterface->persist($officeExperiences);
            $entityManagerInterface->flush();
        }
        return $this->redirectToRoute('app_experience');
    }
}
