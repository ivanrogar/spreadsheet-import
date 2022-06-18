<?php

declare(strict_types=1);

namespace App\Writer;

use App\Contract\Spreadsheet\WriterInterface;
use App\Data\Flattener;
use App\Exception\Spreadsheet\WriterException;
use App\Factory\Service\GoogleServiceSheetsFactory;
use Google\Service\Sheets;
use Google\Exception as GoogleException;

class GoogleSpreadsheetWriter implements WriterInterface
{
    public const CODE = 'google_spreadsheet';

    private GoogleServiceSheetsFactory $sheetsFactory;
    private Flattener $flattener;
    private ?Sheets $sheets = null;

    public function __construct(GoogleServiceSheetsFactory $sheetsFactory, Flattener $flattener)
    {
        $this->sheetsFactory = $sheetsFactory;
        $this->flattener = $flattener;
    }

    public function getCode(): string
    {
        return self::CODE;
    }

    /**
     * @inheritDoc
     */
    public function write(string $identifier, array $data): void
    {
        try {
            $sheets = $this->getSheets();

            $flattenedData = $this->flattener->flatten($data);

            $body = new Sheets\ValueRange([
                'values' => $flattenedData
            ]);

            $clearBody = new Sheets\ClearValuesRequest();

            $params = [
                'valueInputOption' => 'RAW'
            ];

            $range = 'Sheet1';

            $sheets
                ->spreadsheets_values
                ->clear(
                    $identifier,
                    $range,
                    $clearBody
                );

            $sheets
                ->spreadsheets_values
                ->update(
                    $identifier,
                    $range,
                    $body,
                    $params
                );
        } catch (GoogleException $exception) {
            throw WriterException::becauseSpreadsheetUpdateFailed($identifier, $exception);
        }
    }

    private function getSheets(): Sheets
    {
        if ($this->sheets === null) {
            $this->sheets = $this->sheetsFactory->create();
        }

        return $this->sheets;
    }
}
