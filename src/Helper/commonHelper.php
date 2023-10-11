<?php

namespace App\Helper;

use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class commonHelper
{
    private $requestStack;
    private $urlGenerator;
    public function __construct(RequestStack $requestStack, UrlGeneratorInterface $urlGenerator)
    {
        $this->requestStack = $requestStack;
        $this->urlGenerator = $urlGenerator;
    }

    public static function uploadImage($file = '', $mimeType = 'image/png')
    {
        // Initialize the Google API client
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

        // Create a Google Drive service
        $service = new Google_Service_Drive($client);

        // Upload the image
        $fileMetadata = new Google_Service_Drive_DriveFile([
            'name' => 'testimage.png',
            'parents' => [$_ENV['GOOGLE_DRIVE_FOLDER_ID']]
        ]);

        $file = $service->files->create($fileMetadata, [
            'data' => $file,
            'mimeType' => $mimeType,
            'uploadType' => 'multipart',
        ]);

        return $file->id;
    }
}
