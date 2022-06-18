<?php

declare(strict_types=1);

namespace App\Tests\Reader;

use App\Contract\ReaderInterface;
use App\Exception\Reader\ReaderNotFoundException;
use App\Reader\Pool;
use PHPUnit\Framework\TestCase;

class PoolTest extends TestCase
{
    private ReaderInterface $reader;

    protected function setUp(): void
    {
        $this->reader = $this->createMock(ReaderInterface::class);
    }

    public function testGet()
    {
        // given
        $expectedReaderCode = 'reader_code';

        $subject = new Pool([$this->reader]);

        $this->reader->method('getCode')->willReturn($expectedReaderCode);

        // when
        $reader = $subject->get($expectedReaderCode);

        // then
        $this->assertInstanceOf(ReaderInterface::class, $reader);
    }

    public function testGetWillThrowBecauseReaderNotFound()
    {
        // given
        $expectedReaderCode = 'reader_code';

        $subject = new Pool([$this->reader]);

        $this->reader->method('getCode')->willReturn('other_reader_code');

        $this->expectException(ReaderNotFoundException::class);

        // when, then
        $subject->get($expectedReaderCode);
    }
}
