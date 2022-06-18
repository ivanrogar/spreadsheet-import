<?php

declare(strict_types=1);

namespace App\Tests\Console\Command;

use App\Command\Spreadsheet\ImportCommand;
use App\Console\Command\SpreadsheetImportCommand;
use App\Contract\CommandInterface;
use App\Exception\CommandException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;

class SpreadsheetImportCommandTest extends TestCase
{
    private CommandInterface $command;
    private QuestionHelper $questionHelper;

    private SpreadsheetImportCommand $subject;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->command = $this->createMock(ImportCommand::class);
        $this->questionHelper = $this->createMock(QuestionHelper::class);

        $this->subject = $this
            ->getMockBuilder(SpreadsheetImportCommand::class)
            ->onlyMethods(['getHelper'])
            ->setConstructorArgs([$this->command, 'spreadsheet:import'])
            ->getMock();

        $application = new Application();

        $application->add($this->subject);

        $command = $application->find('spreadsheet:import');

        $this->commandTester = new CommandTester($command);
    }

    public function testExecute()
    {
        // given
        $expectedIdentifier = 'identifier';
        $expectedInputPath = 'input_path';
        $expectedStatusCode = Command::SUCCESS;

        // when
        $this->commandTester->execute(
            [
                SpreadsheetImportCommand::ARGUMENT_INPUT_PATH => $expectedInputPath,
                SpreadsheetImportCommand::ARGUMENT_SPREADSHEET_IDENTIFIER => $expectedIdentifier,
            ]
        );

        // then
        $this->assertSame($expectedStatusCode, $this->commandTester->getStatusCode());
    }

    public function testExecuteWillAskForInput()
    {
        // given
        $expectedIdentifier = 'identifier';
        $expectedInputPath = 'input_path';
        $expectedStatusCode = Command::SUCCESS;

        $this
            ->subject
            ->expects($this->exactly(2))
            ->method('getHelper')
            ->with('question')
            ->willReturn($this->questionHelper);

        $this
            ->questionHelper
            ->expects($this->exactly(2))
            ->method('ask')
            ->willReturnOnConsecutiveCalls([
                $expectedInputPath, $expectedIdentifier
            ]);

        // when
        $this->commandTester->execute([]);

        // then
        $this->assertSame($expectedStatusCode, $this->commandTester->getStatusCode());
    }

    public function testExecuteWillFailBecauseOfImportException()
    {
        // given
        $expectedIdentifier = 'identifier';
        $expectedInputPath = 'input_path';
        $expectedStatusCode = Command::FAILURE;

        $this
            ->command
            ->expects($this->once())
            ->method('execute')
            ->willThrowException(CommandException::becauseOfInvalidContextParameters());

        // when
        $this->commandTester->execute([
            SpreadsheetImportCommand::ARGUMENT_INPUT_PATH => $expectedInputPath,
            SpreadsheetImportCommand::ARGUMENT_SPREADSHEET_IDENTIFIER => $expectedIdentifier,
        ]);

        // then
        $this->assertSame($expectedStatusCode, $this->commandTester->getStatusCode());
    }
}
