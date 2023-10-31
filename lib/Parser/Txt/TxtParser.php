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

namespace AA\FileParser\Parser\Txt;


use AA\FileParser\Parser\AbstractParser;
use Symfony\Component\Filesystem\Filesystem;

class TxtParser extends AbstractParser
{
    private Filesystem $filesystem;
    /**
     * the file delimiter
     * @var string
     */
    private ?string $delimiter;
    /**
     * @var int|null
     */
    private ?int $headerIndex;
    /**
     * @var string[]
     */
    private array $lines;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    public function parse($path)
    {
        if (!$this->filesystem->exists($path)) {
            throw new \Exception(sprintf('File not found in path %s', $path));
        }
        $fileContents = file_get_contents($path);

        $this->lines = explode("\n", $fileContents);
        return $this->lines;
    }

    public function getRecords($params=[])
    {
        $offset = $params['offset'] ?? 0;
        $limit = $params['limit'] ?? null;
        // Extract headers if set
        $headers = [];
        $records = [];
        if ($this->headerIndex !== false) {
            $headers = explode($this->delimiter, $this->lines[$this->headerIndex]);
            $this->lines = array_slice($this->lines, $offset + 1);
            if ($limit !== null) {
                $this->lines = array_slice($this->lines, 0, $limit);
            }
        } else {
            $this->lines = array_slice($this->lines, $offset, $limit);
            if ($limit !== null) {
                $this->lines = array_slice($this->lines, 0, $limit);
            }
        }

        // Process the lines
        foreach ($this->lines as $index => $line) {
            $records[$index] = $this->processLine($line, $headers);
        }
        return $records;

    }

    private function processLine(string $line, array $headers = [])
    {
        if (empty($this->delimiter)) {
            throw new \Exception('Delimiter must be set before parse txt file');
        }
        // Split the line using the specified delimiter
        $parts = explode($this->delimiter, $line);

        // If headers are available, create an associative array
        if (!empty($headers)) {
            $record = array_combine($headers, $parts);
        } else {
            $record = $parts;
        }
        return $record;
    }

    public function setDelimiter($delimiter): string
    {
        $this->delimiter = $delimiter;
        return $this->delimiter;
    }

    /**
     * @param int|null $offset
     * @return int|null
     */
    public function setHeaderOffset(?int $offset): ?int
    {
        $this->headerIndex = $offset;
        return $this->headerIndex;
    }

}