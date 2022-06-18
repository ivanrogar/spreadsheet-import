<?php

declare(strict_types=1);

namespace App\Validator;

use RuntimeException;

class AnswerValidator
{
    /**
     * @throws RuntimeException
     */
    public function __invoke(mixed $answer): string
    {
        if (!is_string($answer) || empty(trim($answer))) {
            throw new RuntimeException(
                'Invalid input'
            );
        }

        return $answer;
    }
}
