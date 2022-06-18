<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Command\Spreadsheet\ImportCommand;
use App\Exception\CommandException;
use App\Reader\Type\Catalog\XmlReader;
use App\Validator\AnswerValidator;
use App\Writer\GoogleSpreadsheetWriter;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'spreadsheet:import')]
class SpreadsheetImportCommand extends Command
{
    public const ARGUMENT_INPUT_PATH = 'input-path';
    public const ARGUMENT_SPREADSHEET_IDENTIFIER = 'spreadsheet-identifier';
    public const OPTION_READER = 'reader';
    public const OPTION_WRITER = 'writer';

    protected static $defaultName = 'spreadsheet:import';
    protected static $defaultDescription = 'Import data to spreadsheet';

    private ImportCommand $importCommand;

    /**
     * @inheritDoc
     */
    public function __construct(
        ImportCommand $importCommand,
        string $name = null
    ) {
        parent::__construct($name);
        $this->importCommand = $importCommand;
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                self::ARGUMENT_INPUT_PATH,
                InputArgument::OPTIONAL,
                'Path to input file'
            )
            ->addArgument(
                self::ARGUMENT_SPREADSHEET_IDENTIFIER,
                InputArgument::OPTIONAL,
                'Spreadsheet identifier'
            )
            ->addOption(
                self::OPTION_READER,
                null,
                InputOption::VALUE_REQUIRED,
                'Reader',
                XmlReader::CODE
            )
            ->addOption(
                self::OPTION_WRITER,
                null,
                InputOption::VALUE_REQUIRED,
                'Writer',
                GoogleSpreadsheetWriter::CODE
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        $inputPath = $input->getArgument(self::ARGUMENT_INPUT_PATH);
        $identifier = $input->getArgument(self::ARGUMENT_SPREADSHEET_IDENTIFIER);

        $validator = new AnswerValidator();

        if (!$inputPath) {
            /**
             * @var QuestionHelper $helper
             */
            $helper = $this->getHelper('question');

            $question = new Question('Input Path: ');

            $question->setValidator($validator)->setMaxAttempts(2);

            $inputPath = $helper->ask($input, $output, $question);
        }

        if (!$identifier) {
            /**
             * @var QuestionHelper $helper
             */
            $helper = $this->getHelper('question');

            $question = new Question('Spreadsheet Identifier: ');

            $question->setValidator($validator)->setMaxAttempts(2);

            $identifier = $helper->ask($input, $output, $question);
        }

        $context = [
            ImportCommand::CONTEXT_PARAM_INPUT_PATH => $inputPath,
            ImportCommand::CONTEXT_PARAM_SPREADSHEET_IDENTIFIER => $identifier,
            ImportCommand::CONTEXT_PARAM_READER_CODE => $input->getOption(self::OPTION_READER),
            ImportCommand::CONTEXT_PARAM_WRITER_CODE => $input->getOption(self::OPTION_WRITER)
        ];

        $style->info('Working ...');

        try {
            $this->importCommand->execute($context);
        } catch (CommandException $exception) {
            $style->error($exception->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
