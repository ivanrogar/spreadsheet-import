<?php

declare(strict_types=1);

namespace App\Logger;

use Psr\Log\LoggerInterface;
use Throwable;

trait LoggerTrait
{
    protected LoggerInterface $logger;

    protected function logException(Throwable $exception): void
    {
        $this
            ->logger
            ->critical(
                $this->dissectException($exception),
                ['trace' => $exception->getTraceAsString()]
            );
    }

    private function dissectException(Throwable $exception): string
    {
        $message = sprintf(
            '%s at #%s in %s',
            $exception->getMessage(),
            $exception->getLine(),
            $exception->getFile()
        );

        if ($exception->getPrevious()) {
            $message .= ' -> ' . $this->dissectException($exception->getPrevious());
        }

        return $message;
    }
}
