<?php

declare(strict_types=1);

namespace App\Factory\Service;

use App\Factory\Client\GoogleClientFactory;
use Google\Service\Sheets;

class GoogleServiceSheetsFactory
{
    private GoogleClientFactory $clientFactory;

    public function __construct(GoogleClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    public function create(): Sheets
    {
        return new Sheets($this->clientFactory->create());
    }
}
