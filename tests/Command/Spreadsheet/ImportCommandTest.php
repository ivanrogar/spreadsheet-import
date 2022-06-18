<?php

declare(strict_types=1);

namespace App\Tests\Command\Spreadsheet;

use App\Command\Spreadsheet\ImportCommand;
use App\Contract\ReaderInterface;
use App\Contract\Spreadsheet\WriterInterface;
use App\Exception\Reader\InvalidInputException;
use App\Exception\Reader\ReaderNotFoundException;
use App\Exception\Spreadsheet\WriterException;
use App\Exception\Writer\WriterNotFoundException;
use App\Reader\Pool as ReaderPool;
use App\Writer\Pool as WriterPool;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ImportCommandTest extends TestCase
{
    private ReaderInterface $reader;
    private WriterInterface $writer;

    private ReaderPool $readerPool;
    private WriterPool $writerPool;
    private LoggerInterface $logger;

    private ImportCommand $subject;

    protected function setUp(): void
    {
        $this->reader = $this->createMock(ReaderInterface::class);
        $this->writer = $this->createMock(WriterInterface::class);

        $this->readerPool = $this->createMock(ReaderPool::class);
        $this->writerPool = $this->createMock(WriterPool::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->subject = new ImportCommand(
            $this->readerPool,
            $this->writerPool,
            $this->logger
        );
    }

    public function testExecute()
    {
        // given
        $expectedReaderCode = 'reader_code';
        $expectedWriterCode = 'writer_code';
        $expectedSpreadsheetIdentifier = 'identifier';
        $expectedInputPath = 'input_path';

        $this
            ->readerPool
            ->expects($this->once())
            ->method('get')
            ->with($expectedReaderCode)
            ->willReturn($this->reader);

        $this
            ->writerPool
            ->expects($this->once())
            ->method('get')
            ->with($expectedWriterCode)
            ->willReturn($this->writer);

        $context = [
            ImportCommand::CONTEXT_PARAM_READER_CODE => $expectedReaderCode,
            ImportCommand::CONTEXT_PARAM_WRITER_CODE => $expectedWriterCode,
            ImportCommand::CONTEXT_PARAM_SPREADSHEET_IDENTIFIER => $expectedSpreadsheetIdentifier,
            ImportCommand::CONTEXT_PARAM_INPUT_PATH => $expectedInputPath,
        ];

        $this
            ->reader
            ->expects($this->once())
            ->method('read')
            ->with($expectedInputPath);

        $this
            ->writer
            ->expects($this->once())
            ->method('write')
            ->with($expectedSpreadsheetIdentifier, []);

        $this->logger->expects($this->never())->method('critical');

        // when, then
        $this->subject->execute($context);
    }

    /**
     * @dataProvider getContextParameters
     */
    public function testExecuteShouldFailBecauseOfInvalidContextParameters(
        mixed $expectedReaderCode,
        mixed $expectedWriterCode,
        mixed $expectedSpreadsheetIdentifier,
        mixed $expectedInputPath,
        string $expectedExceptionMessage
    ) {
        $context = [
            ImportCommand::CONTEXT_PARAM_READER_CODE => $expectedReaderCode,
            ImportCommand::CONTEXT_PARAM_WRITER_CODE => $expectedWriterCode,
            ImportCommand::CONTEXT_PARAM_SPREADSHEET_IDENTIFIER => $expectedSpreadsheetIdentifier,
            ImportCommand::CONTEXT_PARAM_INPUT_PATH => $expectedInputPath,
        ];

        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->logger->expects($this->once())->method('critical');

        // when, then
        $this->subject->execute($context);
    }

    public function testExecuteWillFailBecauseOfMissingReader()
    {
        // given
        $expectedReaderCode = 'reader_code';
        $expectedWriterCode = 'writer_code';
        $expectedSpreadsheetIdentifier = 'identifier';
        $expectedInputPath = 'input_path';

        $this
            ->readerPool
            ->expects($this->once())
            ->method('get')
            ->willThrowException(new ReaderNotFoundException());

        $context = [
            ImportCommand::CONTEXT_PARAM_READER_CODE => $expectedReaderCode,
            ImportCommand::CONTEXT_PARAM_WRITER_CODE => $expectedWriterCode,
            ImportCommand::CONTEXT_PARAM_SPREADSHEET_IDENTIFIER => $expectedSpreadsheetIdentifier,
            ImportCommand::CONTEXT_PARAM_INPUT_PATH => $expectedInputPath,
        ];

        $this->logger->expects($this->once())->method('critical');

        $this->expectExceptionMessage('Reader exception');

        // when, then
        $this->subject->execute($context);
    }

    public function testExecuteWillFailBecauseOfFailedReader()
    {
        // given
        $expectedReaderCode = 'reader_code';
        $expectedWriterCode = 'writer_code';
        $expectedSpreadsheetIdentifier = 'identifier';
        $expectedInputPath = 'input_path';

        $this
            ->readerPool
            ->expects($this->once())
            ->method('get')
            ->with($expectedReaderCode)
            ->willReturn($this->reader);

        $this
            ->reader
            ->method('read')
            ->willThrowException(new InvalidInputException());

        $context = [
            ImportCommand::CONTEXT_PARAM_READER_CODE => $expectedReaderCode,
            ImportCommand::CONTEXT_PARAM_WRITER_CODE => $expectedWriterCode,
            ImportCommand::CONTEXT_PARAM_SPREADSHEET_IDENTIFIER => $expectedSpreadsheetIdentifier,
            ImportCommand::CONTEXT_PARAM_INPUT_PATH => $expectedInputPath,
        ];

        $this->logger->expects($this->once())->method('critical');

        $this->expectExceptionMessage('Reader exception');

        // when, then
        $this->subject->execute($context);
    }

    public function testExecuteWillFailBecauseOfMissingWriter()
    {
        // given
        $expectedReaderCode = 'reader_code';
        $expectedWriterCode = 'writer_code';
        $expectedSpreadsheetIdentifier = 'identifier';
        $expectedInputPath = 'input_path';

        $this
            ->readerPool
            ->expects($this->once())
            ->method('get')
            ->with($expectedReaderCode)
            ->willReturn($this->reader);

        $this
            ->writerPool
            ->expects($this->once())
            ->method('get')
            ->willThrowException(new WriterNotFoundException());

        $context = [
            ImportCommand::CONTEXT_PARAM_READER_CODE => $expectedReaderCode,
            ImportCommand::CONTEXT_PARAM_WRITER_CODE => $expectedWriterCode,
            ImportCommand::CONTEXT_PARAM_SPREADSHEET_IDENTIFIER => $expectedSpreadsheetIdentifier,
            ImportCommand::CONTEXT_PARAM_INPUT_PATH => $expectedInputPath,
        ];

        $this->logger->expects($this->once())->method('critical');

        $this->expectExceptionMessage('Writer exception');

        // when, then
        $this->subject->execute($context);
    }

    public function testExecuteWillFailBecauseOfFailedWriter()
    {
        // given
        $expectedReaderCode = 'reader_code';
        $expectedWriterCode = 'writer_code';
        $expectedSpreadsheetIdentifier = 'identifier';
        $expectedInputPath = 'input_path';

        $this
            ->readerPool
            ->expects($this->once())
            ->method('get')
            ->with($expectedReaderCode)
            ->willReturn($this->reader);

        $this
            ->writerPool
            ->expects($this->once())
            ->method('get')
            ->with($expectedWriterCode)
            ->willReturn($this->writer);

        $this
            ->writer
            ->expects($this->once())
            ->method('write')
            ->with($expectedSpreadsheetIdentifier, [])
            ->willThrowException(new WriterException());

        $context = [
            ImportCommand::CONTEXT_PARAM_READER_CODE => $expectedReaderCode,
            ImportCommand::CONTEXT_PARAM_WRITER_CODE => $expectedWriterCode,
            ImportCommand::CONTEXT_PARAM_SPREADSHEET_IDENTIFIER => $expectedSpreadsheetIdentifier,
            ImportCommand::CONTEXT_PARAM_INPUT_PATH => $expectedInputPath,
        ];

        $this->logger->expects($this->once())->method('critical');

        $this->expectExceptionMessage('Writer exception');

        // when, then
        $this->subject->execute($context);
    }

    public static function getContextParameters(): array
    {
        return [
            [
                ImportCommand::CONTEXT_PARAM_READER_CODE => null,
                ImportCommand::CONTEXT_PARAM_WRITER_CODE => null,
                ImportCommand::CONTEXT_PARAM_SPREADSHEET_IDENTIFIER => null,
                ImportCommand::CONTEXT_PARAM_INPUT_PATH => null,
                sprintf(
                    'Invalid context parameters: %s',
                    implode(', ', [
                        ImportCommand::CONTEXT_PARAM_SPREADSHEET_IDENTIFIER,
                        ImportCommand::CONTEXT_PARAM_INPUT_PATH,
                        ImportCommand::CONTEXT_PARAM_READER_CODE,
                        ImportCommand::CONTEXT_PARAM_WRITER_CODE
                    ])
                )
            ]
        ];
    }
}
