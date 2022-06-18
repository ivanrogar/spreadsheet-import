<?php

declare(strict_types=1);

namespace App\Reader;

use App\Contract\ReaderInterface;
use App\Exception\Reader\ReaderNotFoundException;

class Pool
{
    /**
     * @var iterable<ReaderInterface>
     */
    private iterable $readers;

    /**
     * @param ReaderInterface[]|iterable $readers
     */
    public function __construct(iterable $readers = [])
    {
        $this->readers = $readers;
    }

    /**
     * @throws ReaderNotFoundException
     */
    public function get(string $code): ReaderInterface
    {
        foreach ($this->readers as $reader) {
            if ($reader->getCode() === $code) {
                return $reader;
            }
        }

        throw new ReaderNotFoundException(
            sprintf('Loader %s not found', $code)
        );
    }
}
