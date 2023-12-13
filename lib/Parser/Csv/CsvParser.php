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

namespace AA\FileParser\Parser\Csv;


use AA\FileParser\Parser\AbstractParser;
use League\Csv\CharsetConverter;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\Statement;

class CsvParser extends AbstractParser
{
    /**
     * @var Reader
     */
    private Reader $csv;

    /**
     * @param $path
     * @param $mode
     * @return Reader
     */
    public function parse($path, $mode): Reader
    {
        $content = file_get_contents($path);
        $csv = Reader::createFromString($content, $mode);
         /* uncomment this if you need to convert to UTF8 */
//        $this->csv = $this->convertToUtf8($csv);
        return $this->csv;
    }

    /**
     * @throws Exception
     */
    public function setHeaderOffset(?int $offset): Reader
    {
        $this->csv->setHeaderOffset($offset);
        return $this->csv;
    }

    public function setDelimiter($delimiter): Reader
    {
        $this->csv->setDelimiter($delimiter);
        return $this->csv;
    }

    /**
     * @param Reader $csv
     * @return Reader
     */
    private function convertToUtf8(Reader $csv): Reader
    {
        $input_bom = $csv->getInputBOM();

        if ($input_bom === Reader::BOM_UTF16_LE || $input_bom === Reader::BOM_UTF16_BE) {
            CharsetConverter::addTo($csv, 'utf-16', 'utf-8');

        }
        if ($input_bom === Reader::BOM_UTF32_LE || $input_bom === Reader::BOM_UTF32_BE) {
            CharsetConverter::addTo($csv, 'utf-32', 'utf-8');

        }

        if ($input_bom === '') {
            CharsetConverter::addTo($csv, 'ASCII', 'utf-8');
        }
        return $csv;
    }

    /**
     * @param $offset
     * @param $limit
     * @return \League\Csv\TabularDataReader
     * @throws Exception
     * @throws \League\Csv\SyntaxError
     */
    public function getRecords($params = []): \League\Csv\TabularDataReader
    {
        $offset = $params['offset'] ?? 0;
        $limit = $params['limit'] ?? null;

        $stmt = (new Statement())
            ->offset($offset);
        if (!empty($limit)) {
            $stmt->limit($limit);
        }

        return $stmt->process($this->csv);
    }
    public function getHeader(): array
    {
        return $this->csv->getHeader();
    }
}
 
