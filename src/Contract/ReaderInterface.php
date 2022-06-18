<?php

declare(strict_types=1);

namespace App\Contract;

use App\Exception\Reader\InvalidInputException;

interface ReaderInterface
{
    public function getCode(): string;

    /**
     * @param string $inputPath
     * @return array<array<string, mixed>>
     * @throws InvalidInputException
     */
    public function read(string $inputPath): array;
}
