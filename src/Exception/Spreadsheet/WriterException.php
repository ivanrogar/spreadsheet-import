<?php

declare(strict_types=1);

namespace App\Exception\Spreadsheet;

use App\Exception\BaseException;
use Throwable;

/**
 * @codeCoverageIgnore
 */
class WriterException extends BaseException
{
    public static function becauseSpreadsheetDoesNotExist(
        string $identifier,
        ?Throwable $previous = null
    ): WriterException {
        return new self(
            sprintf('Spreadsheet %s does not exist', $identifier),
            0,
            $previous
        );
    }

    public static function becauseSpreadsheetUpdateFailed(
        string $identifier,
        ?Throwable $previous = null
    ): WriterException {
        return new self(
            sprintf('Spreadsheet %s update failed', $identifier),
            0,
            $previous
        );
    }
}
