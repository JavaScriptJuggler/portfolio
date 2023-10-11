<?php

namespace App\Helper;

use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelInterface;

class commonHelper
{
    public static function uploadImage($authCode = '', $mimeType = 'image/png')
    {
        $publicDir = realpath(__DIR__ . '../../public');
        // Initialize the Google API client
        $client = new Google_Client();
        $client->setApplicationName('Your Symfony App');
        $client->setScopes([Google_Service_Drive::DRIVE]);
        $client->setClientId('982462715601-0s0id6ht324p71cjedjargtmepu4tr09.apps.googleusercontent.com');
        $client->setClientSecret('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');
        $client->setRedirectUri('http://localhost:8000/google/auth');
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
        ]);
        $content = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/favicon.png');

        $file = $service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $mimeType,
            'uploadType' => 'multipart',
        ]);

        return 'Image uploaded successfully. File ID: ' . $file->id;
    }
}
