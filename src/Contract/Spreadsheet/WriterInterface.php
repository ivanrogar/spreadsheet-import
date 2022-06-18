<?php

declare(strict_types=1);

namespace App\Contract\Spreadsheet;

use App\Exception\Spreadsheet\WriterException;

interface WriterInterface
{
    public function getCode(): string;

    /**
     * @param array<array<string, mixed>> $data
     * @throws WriterException
     */
    public function write(string $identifier, array $data): void;
}
