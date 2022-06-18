<?php

declare(strict_types=1);

namespace App\Tests\Validator;

use App\Validator\AnswerValidator;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class AnswerValidatorTest extends TestCase
{
    public function testInvoke()
    {
        // given
        $input = 'data';
        $expectedOutput = 'data';

        $validator = new AnswerValidator();

        // when
        $result = $validator($input);

        // then
        $this->assertSame($expectedOutput, $result);
    }

    /**
     * @dataProvider getAnswers
     */
    public function testInvokeShouldFail(mixed $answer)
    {
        // given
        $validator = new AnswerValidator();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid input');

        // when, then
        $validator($answer);
    }

    public static function getAnswers(): array
    {
        return [
            [0],
            ['']
        ];
    }
}
