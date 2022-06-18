<?php

declare(strict_types=1);

namespace App\Loader;

use App\Contract\LoaderInterface;
use App\Exception\Reader\InvalidInputException;

// todo: use a client for file downloads and a file wrapper component for loading files
class DefaultLoader implements LoaderInterface
{
    public function load(string $inputPath): string
    {
        if (!filter_var($inputPath, FILTER_VALIDATE_URL) && !is_readable($inputPath)) {
            throw InvalidInputException::becauseInputPathNotReadable($inputPath);
        }

        $data = \file_get_contents($inputPath);

        // @codeCoverageIgnoreStart
        if (!is_string($data)) {
            throw InvalidInputException::becauseDataIsInvalid();
        }
        // @codeCoverageIgnoreEnd

        return $data;
    }
}
