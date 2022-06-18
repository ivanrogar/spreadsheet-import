<?php

declare(strict_types=1);

namespace App\Tests\Data;

use App\Data\Flattener;
use PHPUnit\Framework\TestCase;

class FlattenerTest extends TestCase
{
    public function testFlatten()
    {
        // given
        $expectedInput = [
            [
                'key' => 'value1',
                'key2' => 'value2',
                'key3' => null,
            ],
            [
                'key' => 'value3',
                'key2' => 'value4',
                'key3' => null,
            ],
        ];

        $expectedOutput = [
            [
                'key',
                'key2',
                'key3',
            ],
            [
                'value1',
                'value2',
                '',
            ],
            [
                'value3',
                'value4',
                '',
            ],
        ];

        $subject = new Flattener();

        // when
        $result = $subject->flatten($expectedInput);

        // then
        $this->assertSame($expectedOutput, $result);
    }
}
