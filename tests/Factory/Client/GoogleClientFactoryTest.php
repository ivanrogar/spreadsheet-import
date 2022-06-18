<?php

declare(strict_types=1);

namespace App\Tests\Factory\Client;

use App\Factory\Client\GoogleClientFactory;
use PHPUnit\Framework\TestCase;

class GoogleClientFactoryTest extends TestCase
{
    public function testCreate()
    {
        // given
        $expectedCredentialsFolder = dirname(__FILE__) . '/../../files/';
        $expectedScopes = [\Google_Service_Sheets::SPREADSHEETS];
        $expectedAccessType = 'offline';

        $subject = new GoogleClientFactory($expectedCredentialsFolder);

        // when
        $client = $subject->create();

        // then
        $this->assertSame($expectedScopes, $client->getScopes());
        $this->assertSame($expectedAccessType, $client->getConfig('access_type'));
    }
}
