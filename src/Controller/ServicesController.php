<?php

namespace App\Controller;

use App\Entity\Services;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServicesController extends GlobalController
{
    #[Route('/administrator/services', name: 'app_services')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $getRecords = $entityManager->getRepository(Services::class)->findBy(['user_id' => $this->getUser()->getId()]);
        $data = [];
        foreach ($getRecords as $key => $value) {
            $data[] = [
                "serviceImage" => $value->getIcon(),
                "serviceName" => $value->getServiceName(),
                "serviceDescription" => $value->getServiceDescription(),
            ];
        }
        return $this->render('backend/services/index.html.twig', [
            'records' => json_encode($data, true),
        ]);
    }
    #[Route('/administrator/save-services', name: 'app_services_save', methods: 'POST')]
    public function saveServices(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $requestGet = $request->request->all();
        $requestData = $request->request;
        if (count($requestGet['group-a']) > 0) {
            $searchData = $entityManager->getRepository(Services::class);
            $totalData = $searchData->createQueryBuilder('query')
                ->select('count(query.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if (!$totalData) {
                foreach ($requestGet['group-a'] as $key => $data) {
                    $serviceModel = new Services;
                    $serviceModel->setServiceName($data['serviceName']);
                    $serviceModel->setServiceDescription($data['serviceDescription']);
                    $serviceModel->setIcon($data['serviceImage']);
                    $serviceModel->setUserId($this->getUser()->getId());
                    $result = $entityManager->persist($serviceModel);
                    $entityManager->flush();
                }
                $this->addFlash('success', 'Service Updated');
                return $this->redirectToRoute('app_services');
            } else {
                $getConnection = $entityManager->getConnection();
                $stmt = $getConnection->prepare("truncate table services");
                $stmt->executeQuery();
                foreach ($requestGet['group-a'] as $key => $data) {
                    $serviceModel = new Services;
                    $serviceModel->setServiceName($data['serviceName']);
                    $serviceModel->setServiceDescription($data['serviceDescription']);
                    $serviceModel->setIcon($data['serviceImage']);
                    $serviceModel->setUserId($this->getUser()->getId());
                    $result = $entityManager->persist($serviceModel);
                    $entityManager->flush();
                }
                $this->addFlash('success', 'Service Updated');
                return $this->redirectToRoute('app_services');
            }
        }
    }
}
