<?php

declare(strict_types=1);

namespace App\Writer;

use App\Contract\Spreadsheet\WriterInterface;
use App\Exception\Writer\WriterNotFoundException;

class Pool
{
    /**
     * @var iterable<WriterInterface>
     */
    private iterable $writers;

    /**
     * @param WriterInterface[]|iterable $writers
     */
    public function __construct(iterable $writers = [])
    {
        $this->writers = $writers;
    }

    /**
     * @throws WriterNotFoundException
     */
    public function get(string $code): WriterInterface
    {
        foreach ($this->writers as $writer) {
            if ($writer->getCode() === $code) {
                return $writer;
            }
        }

        throw new WriterNotFoundException(
            sprintf('Writer %s not found', $code)
        );
    }
}
