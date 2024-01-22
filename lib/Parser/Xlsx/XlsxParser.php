<?php
/*
 * MIT License
 *
 * Copyright (c) 2023 Anis Ajengui
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */

namespace AA\FileParser\Parser\Xlsx;


use AA\FileParser\Parser\AbstractParser;
use PhpOffice\PhpSpreadsheet\IOFactory;

class XlsxParser extends AbstractParser
{
    /**
     * @var int|null
     */
    private ?int $headerIndex = 1;

    private \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet;
    private array $headerRow = [];
    public function parse($path): \PhpOffice\PhpSpreadsheet\Spreadsheet
    {
        $this->spreadsheet = IOFactory::load($path);
        return $this->spreadsheet;
    }

    /**
     * @throws \Exception
     */
    public function getRecords($params=[]): array
    {
        $sheetName=$params['sheetName'] ?? 'import';
        $offset=$params['offset'] ?? 0;
        $limit=$params['limit'] ?? null;
        // Select the sheet based on the provided sheet name
        $worksheet = $this->spreadsheet->getSheetByName($sheetName);
        if ($worksheet === null) {
            throw new \Exception(sprintf('Sheet %s not found in the XLSX file.', $sheetName));
        }

        // Get the highest row and column indexes to iterate through
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();

        // Initialize an array to store the data
        $data = [];
        if((int)$offset===0 && (int) $limit!==0){
            $limit= (int)$limit + 1;
        }
        // Determine the maximum row based on the provided limit (if not null)
        $maxRow = ($limit !== null) ? min($highestRow, $offset + $limit) : $highestRow;

        // Initialize an array for the header row
        $headerRow = [];

        // Iterate through the rows
        for ($row = 1 + $offset; $row <= $maxRow; $row++) {
            // Initialize an array for the current row's data
            $rowData = [];
            // Iterate through the columns
            for ($col = 'A'; $col != $highestColumn; $col++) {
                // Get the cell value
                $cellValue = $worksheet->getCell($col . $row)->getValue();

                // Check if this is the header row
                if ($row === 1) {
                    $headerRow[$col] = $cellValue;

                } else {
                    // Use the header row to set keys for the data
                    $rowData[$headerRow[$col]] = $cellValue;
                }
            }

            // If this is not the header row, add the row data to the main data array
            if ($row !== $this->headerIndex) {
                $data[] = $rowData;
            }
        }
        // Return the data array with header keys
        return $data;
    }

    /**
     * @param int|null $offset
     * @return int|null
     */
    public function setHeaderOffset(?int $offset): ?int
    {
        $offset=$offset < 1 ? 1 : $offset;
        $this->headerIndex = $offset;
        return $this->headerIndex;
    }

    public function setDelimiter($delimiter): void
    {
    }
    public function getHeader()
    {
        if (empty($this->headerRow)) {
            $this->populateHeader();
        }
        return $this->headerRow;
        
    }
    private function populateHeader(): void
    {
        // Select the sheet based on the provided sheet name
        $worksheet = $this->spreadsheet->getActiveSheet();

        // Get the highest column index to iterate through
        $highestColumn = $worksheet->getHighestColumn();

        // Initialize an array for the header row
        $headerRow = [];

        // Iterate through the columns
        for ($col = 'A'; $col != $highestColumn; $col++) {
            // Get the cell value
            $cellValue = $worksheet->getCell($col . $this->headerIndex)->getValue();
            $headerRow[$col] = $cellValue;
        }

        $this->headerRow = $headerRow;
    }

}
