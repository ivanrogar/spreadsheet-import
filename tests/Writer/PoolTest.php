<?php

declare(strict_types=1);

namespace App\Tests\Writer;

use App\Contract\Spreadsheet\WriterInterface;
use App\Exception\Writer\WriterNotFoundException;
use App\Writer\Pool;
use PHPUnit\Framework\TestCase;

class PoolTest extends TestCase
{
    private WriterInterface $writer;

    protected function setUp(): void
    {
        $this->writer = $this->createMock(WriterInterface::class);
    }

    public function testGet()
    {
        // given
        $expectedWriterCode = 'writer_code';

        $subject = new Pool([$this->writer]);

        $this->writer->method('getCode')->willReturn($expectedWriterCode);

        // when
        $writer = $subject->get($expectedWriterCode);

        // then
        $this->assertInstanceOf(WriterInterface::class, $writer);
    }

    public function testGetWillThrowBecauseReaderNotFound()
    {
        // given
        $expectedWriterCode = 'writer_code';

        $subject = new Pool([$this->writer]);

        $this->writer->method('getCode')->willReturn('other_reader_code');

        $this->expectException(WriterNotFoundException::class);

        // when, then
        $subject->get($expectedWriterCode);
    }
}
