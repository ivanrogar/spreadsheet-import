<?php

declare(strict_types=1);

namespace App\Tests\Factory\Service;

use App\Factory\Client\GoogleClientFactory;
use App\Factory\Service\GoogleServiceSheetsFactory;
use Google\Client;
use Google\Service\Sheets;
use PHPUnit\Framework\TestCase;

class GoogleServiceSheetsFactoryTest extends TestCase
{
    private GoogleClientFactory $clientFactory;
    private Client $client;

    private GoogleServiceSheetsFactory $subject;

    protected function setUp(): void
    {
        $this->clientFactory = $this->createMock(GoogleClientFactory::class);
        $this->client = $this->createMock(Client::class);
        $this->clientFactory->method('create')->willReturn($this->client);

        $this->subject = new GoogleServiceSheetsFactory($this->clientFactory);
    }

    public function testCreate()
    {
        // given, when
        $service = $this->subject->create();

        // then
        $this->assertInstanceOf(Sheets::class, $service);
    }
}
