<?php

declare(strict_types=1);

namespace App\Contract;

use App\Exception\Reader\InvalidInputException;

interface LoaderInterface
{
    /**
     * @throws InvalidInputException
     */
    public function load(string $inputPath): string;
}
