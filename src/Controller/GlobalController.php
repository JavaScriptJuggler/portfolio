<?php

namespace App\Controller;

use App\Helper\commonHelper;
use Google_Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Google_Service_Drive_DriveFile;
use Google_Service_Drive;

class GlobalController extends AbstractController
{
    #[Route('/administrator/google-drive-upload-initiate', name: "google_drive_connect")]
    public function googleRedirect(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $result = commonHelper::uploadImage();
        return new Response($result);
    }
    #[Route('/google/auth')]
    public function googleRedirectAuth(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $client = new Google_Client();
        $client->setApplicationName('Your Symfony App');
        $client->setScopes([Google_Service_Drive::DRIVE]);
        $client->setClientId($_ENV['GOOGLE_DRIVE_CLIENT_ID']);
        $client->setClientSecret($_ENV['GOOGLE_DRIVE_SECRECT_KEY']);
        $client->setRedirectUri($_ENV['APPLICATION_URL'] . '/google/auth');
        $client->setAccessType('offline'); // Use 'offline' for long-lasting access
        $accessToken = $client->fetchAccessTokenWithAuthCode($request->query->get('code'));
        file_put_contents($this->getParameter('kernel.project_dir') . '/public/token.json', json_encode($accessToken));
        echo 'Authorization Successfull';
        return $this->redirectToRoute('app_admin_dashboard');
    }
}
