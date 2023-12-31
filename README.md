# PHP FTP and SFTP Client Connector Library

[![License](https://img.shields.io/github/license/ajenguianis/easy-file-parser)](https://github.com/ajenguianis/easy-file-parser/blob/develop/LICENSE)
[![Latest Release](https://img.shields.io/github/v/release/ajenguianis/easy-file-parser)](https://github.com/ajenguianis/easy-file-parser/releases/latest)
![Total Downloads](https://img.shields.io/packagist/dt/ajenguianis/easy-file-parser)
![Latest Unstable Version](https://img.shields.io/packagist/vpre/ajenguianis/easy-file-parser)
![PHP Version](https://img.shields.io/packagist/php-v/ajenguianis/easy-file-parser)

The EasyFileParser PHP library simplifies the process of parsing various file formats, including TXT, XLSX, and CSV. It offers a collection of reusable code components that streamline data extraction and manipulation from these file types, making it an essential tool for data processing tasks.

## Features

- Seamless parsing of TXT, XLSX, and CSV files.
- Reusable code components for efficient data extraction.
- Simplified integration into your PHP projects.
- Enhance your data processing capabilities with minimal effort.
## Getting Started

To get started with our library, you can install it via Composer:

         composer require ajenguianis/easy-file-parser

## Usage

### Load file

Detect parser by extension and parse file:

        $parser= new Parser($path);
        $parser->parse($path, $mode); mode is mondatory only if csv file

### Set header index

        $parser->setHeaderOffset(0);
     
### Set delimiter

        $parser->setDelimiter(';');
     
### Get rows

		$params=[
          'offset'=>0,
		  'limit'=>10
		];

		#if xlsx file
		
		$params=[
		  'sheetName'=>'import',
          'offset'=>0,
		  'limit'=>10
		];
		
        If you need all records set limit to null
		
       $parser->getRecords($params);
