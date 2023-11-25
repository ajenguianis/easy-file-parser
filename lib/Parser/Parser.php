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

namespace AA\FileParser\Parser;

use AA\FileParser\Parser\Csv\CsvParser;
use AA\FileParser\Parser\Txt\TxtParser;
use AA\FileParser\Parser\Xlsx\XlsxParser;
use League\Csv\Reader;

/**
 * @method Reader|\PhpOffice\PhpSpreadsheet\Spreadsheet parse($path, $mode = null) parse file from path
 * @method Reader|int setHeaderOffset($offset) Set header offset
 * @method Reader setDelimiter(string $delimiter) delimiter can be ";", ",", "|" or other character
 * @method array getRecords($params = []) get file records
 * @method array getHeader($params = []) get file header
 *
 * @author  Anis Ajengui <https://github.com/ajenguianis>
 */
class Parser
{
    private TxtParser|XlsxParser|CsvParser $parser;

    /**
     * @param $path
     * @throws \Exception
     */
    public function __construct($path)
    {
        $this->loadFileParser($path);
    }

    /**
     * Call an ftp or sftp method handled by the connector.
     *
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     *
     */
    public function __call($method, array $arguments)
    {
        if (!$this->parser) {
            throw new \Exception('You need to set up parser before calling methods');
        }
        return $this->parser->__call($method, $arguments);
    }

    /**
     * @param $extension
     * @return CsvParser|TxtParser|XlsxParser
     */
    public function loadFileParser($path)
    {
        if (!file_exists($path)) {
            throw new \Exception(sprintf('File is not found in this path %s', $path));
        }
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $this->parser = new TxtParser();
        if ($extension === 'csv') {
            $this->parser = new CsvParser();
        }

        if ($extension === 'xlsx') {
            $this->parser = new XlsxParser();
        }
        return $this->parser;
    }
}
