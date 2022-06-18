<?php

declare(strict_types=1);

namespace App\Exception;

use Throwable;

class CommandException extends BaseException
{
    /**
     * @param array<int, string> $invalidParams
     */
    public static function becauseOfInvalidContextParameters(
        array $invalidParams = [],
        ?Throwable $previous = null
    ): CommandException {
        return new self(
            sprintf(
                'Invalid context parameters: %s',
                implode(', ', $invalidParams)
            ),
            0,
            $previous
        );
    }

    public static function becauseOfReaderException(
        Throwable $previous
    ): CommandException {
        return new self(
            sprintf(
                'Reader exception: %s',
                $previous->getMessage()
            ),
            0,
            $previous
        );
    }

    public static function becauseOfWriterException(
        Throwable $previous
    ): CommandException {
        return new self(
            sprintf(
                'Writer exception: %s',
                $previous->getMessage()
            ),
            0,
            $previous
        );
    }
}
