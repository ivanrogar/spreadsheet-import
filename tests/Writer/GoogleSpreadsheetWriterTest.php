<?php

declare(strict_types=1);

namespace App\Tests\Writer;

use App\Data\Flattener;
use App\Factory\Service\GoogleServiceSheetsFactory;
use App\Writer\GoogleSpreadsheetWriter;
use Google\Exception as GoogleException;
use Google\Service\Sheets;
use PHPUnit\Framework\TestCase;

class GoogleSpreadsheetWriterTest extends TestCase
{
    private GoogleServiceSheetsFactory $sheetsFactory;
    private Sheets $sheets;
    private Sheets\Resource\SpreadsheetsValues $spreadsheetsValues;
    private Flattener $flattener;

    private GoogleSpreadsheetWriter $subject;

    protected function setUp(): void
    {
        $this->sheetsFactory = $this->createMock(GoogleServiceSheetsFactory::class);
        $this->sheets = $this->createMock(Sheets::class);
        $this->sheetsFactory->method('create')->willReturn($this->sheets);
        $this->spreadsheetsValues = $this->createMock(Sheets\Resource\SpreadsheetsValues::class);
        $this->sheets->spreadsheets_values = $this->spreadsheetsValues;
        $this->flattener = new Flattener();

        $this->subject = new GoogleSpreadsheetWriter($this->sheetsFactory, $this->flattener);
    }

    public function testGetCode()
    {
        // given
        $expectedCode = GoogleSpreadsheetWriter::CODE;

        // when, then
        $this->assertSame($expectedCode, $this->subject->getCode());
    }

    public function testWrite()
    {
        // given
        $expectedIdentifier = 'identifier';
        $expectedRange = 'Sheet1';
        $expectedParams = [
            'valueInputOption' => 'RAW'
        ];

        $inputData = [
            [
                'key' => 'value1',
                'key2' => 'value2',
            ],
            [
                'key' => 'value3',
                'key2' => 'value4',
            ],
        ];

        $expectedData = [
            [
                'key',
                'key2',
            ],
            [
                'value1',
                'value2',
            ],
            [
                'value3',
                'value4',
            ],
        ];

        $expectedBody = new Sheets\ValueRange([
            'values' => $expectedData
        ]);

        $expectedClearBody = new Sheets\ClearValuesRequest();

        $this
            ->spreadsheetsValues
            ->expects($this->once())
            ->method('clear')
            ->with(
                $expectedIdentifier,
                $expectedRange,
                $expectedClearBody
            );

        $this
            ->spreadsheetsValues
            ->expects($this->once())
            ->method('update')
            ->with(
                $expectedIdentifier,
                $expectedRange,
                $expectedBody,
                $expectedParams
            );

        // when, then
        $this->subject->write($expectedIdentifier, $inputData);
    }

    public function testWriteWillFailBecauseOfGoogleException()
    {
        // given
        $expectedIdentifier = 'identifier';

        $inputData = [
            [
                'key' => 'value1',
                'key2' => 'value2',
            ],
            [
                'key' => 'value3',
                'key2' => 'value4',
            ],
        ];

        $this
            ->spreadsheetsValues
            ->expects($this->once())
            ->method('update')
            ->willThrowException(new GoogleException());

        $this->expectExceptionMessage(
            sprintf(
                'Spreadsheet %s update failed',
                $expectedIdentifier
            )
        );

        // when, then
        $this->subject->write($expectedIdentifier, $inputData);
    }
}
