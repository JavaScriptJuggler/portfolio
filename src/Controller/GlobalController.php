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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;

class GlobalController extends AbstractController
{
    public function __construct(RequestStack $requestStack)
    {
        $request = $requestStack->getCurrentRequest();
        $session = $request->getSession();
        if (file_exists('token.json'))
            $session->set('isGoogleAuth', 1);
        else
            $session->set('isGoogleAuth', 0);
    }

    #[Route('/administrator/google-drive-upload-initiate', name: "google_drive_connect")]
    public function googleRedirect(): Response
    {
        /* connection initiated */
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $client = new Google_Client();
        $client->setApplicationName('Portfolio Website');
        $client->setScopes([Google_Service_Drive::DRIVE]);
        $client->setClientId($_ENV['GOOGLE_DRIVE_CLIENT_ID']);
        $client->setClientSecret($_ENV['GOOGLE_DRIVE_SECRECT_KEY']);
        $client->setRedirectUri($_ENV['APPLICATION_URL'] . '/google/auth');
        $client->setAccessType('offline'); // Use 'offline' for long-lasting access
        // Check if we have a saved access token
        if (file_exists('token.json')) {
            $accessToken = json_decode(file_get_contents('token.json'), true);
            $client->setAccessToken($accessToken);

            // Check if the access token is expired, and refresh if necessary
            if ($client->isAccessTokenExpired()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                file_put_contents('token.json', json_encode($client->getAccessToken()));
            }
        } else {
            // If no token exists, return an error or handle the authorization flow
            $authUrl = $client->createAuthUrl();
            return new RedirectResponse($authUrl);
        }
        return $this->redirectToRoute('app_admin_dashboard');
    }

    /* auth route. user will come to this route if he/she is not connected to google drive */
    #[Route('/google/auth')]
    public function googleRedirectAuth(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $client = new Google_Client();
        $client->setApplicationName('Portfolio Website');
        $client->setScopes([Google_Service_Drive::DRIVE]);
        $client->setClientId($_ENV['GOOGLE_DRIVE_CLIENT_ID']);
        $client->setClientSecret($_ENV['GOOGLE_DRIVE_SECRECT_KEY']);
        $client->setRedirectUri($_ENV['APPLICATION_URL'] . '/google/auth');
        $client->setAccessType('offline'); // Use 'offline' for long-lasting access
        $accessToken = $client->fetchAccessTokenWithAuthCode($request->query->get('code'));
        file_put_contents($this->getParameter('kernel.project_dir') . '/public/token.json', json_encode($accessToken));
        return $this->googleRedirect();
    }

    /* file upload function. It will only work if you are connected to google drive by above functions */
    public function uplaodFileToDrive($content = '', $mimetype = '', $fileName = '', $existingFileId = '')
    {
        $client = new Google_Client();
        $client->setApplicationName('Portfolio Website');
        $client->setScopes([Google_Service_Drive::DRIVE]);
        $client->setClientId($_ENV['GOOGLE_DRIVE_CLIENT_ID']);
        $client->setClientSecret($_ENV['GOOGLE_DRIVE_SECRECT_KEY']);
        $client->setRedirectUri($_ENV['APPLICATION_URL'] . '/google/auth');
        $client->setAccessType('offline'); // Use 'offline' for long-lasting access
        if (file_exists('token.json')) {
            $accessToken = json_decode(file_get_contents('token.json'), true);
            $client->setAccessToken($accessToken);

            // Check if the access token is expired, and refresh if necessary
            if ($client->isAccessTokenExpired()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                file_put_contents('token.json', json_encode($client->getAccessToken()));
            }
        }
        $service = new Google_Service_Drive($client);
        if ($existingFileId)
            $service->files->delete($existingFileId);
        $fileMetadata = new Google_Service_Drive_DriveFile([
            'name' => $fileName,
            'parents' => [$_ENV['GOOGLE_DRIVE_FOLDER_ID']]
        ]);

        $file = $service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $mimetype,
            'uploadType' => 'multipart',
        ]);

        return $file->id;
    }

    /* file view by google drive file id. was got at the time of upload */
    public function getFileGoogleDrive($fileId)
    {
        $file = '';
        $client = new Google_Client();
        $client->setApplicationName('Portfolio Website');
        $client->setScopes([Google_Service_Drive::DRIVE]);
        $client->setClientId($_ENV['GOOGLE_DRIVE_CLIENT_ID']);
        $client->setClientSecret($_ENV['GOOGLE_DRIVE_SECRECT_KEY']);
        $client->setRedirectUri($_ENV['APPLICATION_URL'] . '/google/auth');
        $client->setAccessType('offline'); // Use 'offline' for long-lasting access

        // Check if we have a saved access token
        if (file_exists('token.json')) {
            $accessToken = json_decode(file_get_contents('token.json'), true);
            $client->setAccessToken($accessToken);

            // Check if the access token is expired, and refresh if necessary
            if ($client->isAccessTokenExpired()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                file_put_contents('token.json', json_encode($client->getAccessToken()));
            }
            // Create a Google Drive service
            $service = new Google_Service_Drive($client);

            // Fetch the file by its ID
            $file = $service->files->get($fileId);
        }
        return $file;
    }
}
