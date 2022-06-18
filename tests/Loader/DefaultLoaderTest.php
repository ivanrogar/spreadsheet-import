<?php

declare(strict_types=1);

namespace App\Tests\Loader;

use App\Loader\DefaultLoader;
use PHPUnit\Framework\TestCase;

class DefaultLoaderTest extends TestCase
{
    public function testLoad()
    {
        // given
        $expectedInputPath = dirname(__FILE__) . '/../files/xml_reader_input.xml';
        $expectedInputData = \file_get_contents($expectedInputPath);

        $subject = new DefaultLoader();

        // when
        $result = $subject->load($expectedInputPath);

        // then
        $this->assertSame($expectedInputData, $result);
    }

    public function testLoadShouldFailBecauseOfInvalidInputPath()
    {
        // given
        $expectedInputPath = dirname(__FILE__) . '/../files/invalid_file.xml';

        $subject = new DefaultLoader();

        $this->expectExceptionMessage('Input path not readable');

        // when, then
        $subject->load($expectedInputPath);
    }

    public function testLoadFromUrl()
    {
        // given
        $expectedInputPath = 'https://www.w3schools.com/xml/simple.xml';

        $subject = new DefaultLoader();

        // when
        $result = $subject->load($expectedInputPath);

        // then
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $result);
    }
}
