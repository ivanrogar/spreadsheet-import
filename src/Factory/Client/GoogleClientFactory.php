<?php

declare(strict_types=1);

namespace App\Factory\Client;

use Google\Client as GoogleClient;

class GoogleClientFactory
{
    public const CREDENTIALS_FILE = 'google_client_credentials.json';

    private string $credentialsFolder;

    public function __construct(string $credentialsFolder)
    {
        $this->credentialsFolder = $credentialsFolder;
    }

    public function create(): GoogleClient
    {
        $client = new GoogleClient();

        $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');
        $client->setAuthConfig($this->credentialsFolder . self::CREDENTIALS_FILE);

        return $client;
    }
}
