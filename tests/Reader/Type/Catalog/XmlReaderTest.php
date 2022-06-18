<?php

declare(strict_types=1);

namespace App\Tests\Reader\Type\Catalog;

use App\Contract\LoaderInterface;
use App\Reader\Type\Catalog\XmlReader;
use PHPUnit\Framework\TestCase;

class XmlReaderTest extends TestCase
{
    private LoaderInterface $loader;

    private XmlReader $subject;

    protected function setUp(): void
    {
        $this->loader = $this->createMock(LoaderInterface::class);

        $this->subject = new XmlReader($this->loader);
    }

    public function testGetCode()
    {
        // when
        $expectedCode = XmlReader::CODE;

        // when, then
        $this->assertSame($expectedCode, $this->subject->getCode());
    }

    /**
     * @dataProvider getXmlData
     */
    public function testRead(string $inputFile, ?string $expectedOutputFile, ?string $exceptionMessage)
    {
        // given
        $basePath = dirname(__FILE__) . '/../../../files/';
        $inputString = \file_get_contents($basePath . $inputFile);

        $this
            ->loader
            ->expects($this->once())
            ->method('load')
            ->with($inputFile)
            ->willReturn($inputString);

        if ($exceptionMessage !== null) {
            $this->expectExceptionMessage($exceptionMessage);
        }

        // when
        $result = $this->subject->read($inputFile);

        // then
        if ($expectedOutputFile !== null) {
            $expectedOutput = \file_get_contents($basePath . $expectedOutputFile);
            $this->assertSame(\json_decode($expectedOutput, true), $result);
        }
    }

    public static function getXmlData()
    {
        return [
            [
                'xml_reader_input.xml',
                'xml_reader_output.json',
                null
            ],
            [
                'xml_reader_input_no_root_tag.xml',
                null,
                'Missing root tag'
            ]
        ];
    }
}
