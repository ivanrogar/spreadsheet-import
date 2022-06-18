<?php

declare(strict_types=1);

namespace App\Command\Spreadsheet;

use App\Contract\CommandInterface;
use App\Exception\CommandException;
use App\Exception\Reader\InvalidInputException;
use App\Exception\Reader\ReaderNotFoundException;
use App\Exception\Spreadsheet\WriterException;
use App\Exception\Writer\WriterNotFoundException;
use App\Logger\LoggerTrait;
use App\Reader\Pool as ReaderPool;
use App\Writer\Pool as WriterPool;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings(Static)
 */
class ImportCommand implements CommandInterface
{
    use LoggerTrait;

    public const CONTEXT_PARAM_SPREADSHEET_IDENTIFIER = 'identifier';
    public const CONTEXT_PARAM_INPUT_PATH = 'input_path';
    public const CONTEXT_PARAM_READER_CODE = 'reader_code';
    public const CONTEXT_PARAM_WRITER_CODE = 'writer_code';

    private ReaderPool $readerPool;
    private WriterPool $writerPool;

    public function __construct(
        ReaderPool $readerPool,
        WriterPool $writerPool,
        LoggerInterface $logger
    ) {
        $this->readerPool = $readerPool;
        $this->writerPool = $writerPool;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute(array $context = []): void
    {
        try {
            [$identifier, $inputPath, $readerCode, $writerCode] = $this->getParams($context);

            try {
                $reader = $this->readerPool->get($readerCode);
            } catch (ReaderNotFoundException $exception) {
                throw CommandException::becauseOfReaderException($exception);
            }

            try {
                $data = $reader->read($inputPath);
            } catch (InvalidInputException $exception) {
                throw CommandException::becauseOfReaderException($exception);
            }

            try {
                $writer = $this->writerPool->get($writerCode);
            } catch (WriterNotFoundException $exception) {
                throw CommandException::becauseOfWriterException($exception);
            }

            try {
                $writer->write($identifier, $data);
            } catch (WriterException $exception) {
                throw CommandException::becauseOfWriterException($exception);
            }
        } catch (CommandException $exception) {
            $this->logException($exception);
            throw $exception;
        }
    }

    /**
     * @param array<mixed> $context
     * @return array<string>
     */
    private function getParams(array $context): array
    {
        $identifier = $context[self::CONTEXT_PARAM_SPREADSHEET_IDENTIFIER] ?? null;
        $inputPath = $context[self::CONTEXT_PARAM_INPUT_PATH] ?? null;
        $readerCode = $context[self::CONTEXT_PARAM_READER_CODE] ?? null;
        $writerCode = $context[self::CONTEXT_PARAM_WRITER_CODE] ?? null;

        $this->validateMany(
            [
                self::CONTEXT_PARAM_SPREADSHEET_IDENTIFIER => $identifier,
                self::CONTEXT_PARAM_INPUT_PATH => $inputPath,
                self::CONTEXT_PARAM_READER_CODE => $readerCode,
                self::CONTEXT_PARAM_WRITER_CODE => $writerCode,
            ]
        );

        return [$identifier, $inputPath, $readerCode, $writerCode];
    }

    private function validateStringNotEmpty(mixed $input): bool
    {
        return is_string($input) && !empty(trim($input));
    }

    /**
     * @param array<string, mixed> $values
     */
    private function validateMany(array $values): void
    {
        $invalidParams = [];

        foreach ($values as $key => $value) {
            if (!$this->validateStringNotEmpty($value)) {
                $invalidParams[] = $key;
            }
        }

        if (!empty($invalidParams)) {
            throw CommandException::becauseOfInvalidContextParameters($invalidParams);
        }
    }
}
