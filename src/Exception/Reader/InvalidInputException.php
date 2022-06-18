<?php

declare(strict_types=1);

namespace App\Exception\Reader;

use App\Exception\BaseException;

/**
 * @codeCoverageIgnore
 */
class InvalidInputException extends BaseException
{
    public static function becauseInputPathNotReadable(string $inputPath): InvalidInputException
    {
        return new self(sprintf('Input path not readable: %s', $inputPath));
    }

    public static function becauseDataIsInvalid(): InvalidInputException
    {
        return new self('Invalid data');
    }

    public static function becauseOfMissingRootTag(string $rootTag): InvalidInputException
    {
        return new self(sprintf('Missing root tag: %s', $rootTag));
    }

    public static function becauseOfInvalidDOMElementConversionResult(): InvalidInputException
    {
        return new self('DOM element conversion result invalid. Array expected.');
    }
}
