<?php

declare(strict_types=1);

namespace App\Contract;

use App\Exception\CommandException;

interface CommandInterface
{
    /**
     * @param array<string, mixed> $context
     * @throws CommandException
     */
    public function execute(array $context = []): void;
}
