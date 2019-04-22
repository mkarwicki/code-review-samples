<?php

/*
 * This is Data Scraper application version 1.0
 *
 * MAIN PURPOSE:
 * 1 - Load data from external source (URL) -(without use of fopen and file_get_contents functions)
 * 2 - Extract selected data (basicly lottery results, but can be any other data as well)
 * 3 - Output them to the desired format (JSON)
 *
 * TECHNOLOGY STACK:
 * 1 - PHP 7.2
 * 2 - PSR Autoloader
 *
 *
 *
 *  USE CASE
 *
 * 1. Load data - With the use of DataLoader
 * 2. Extract data - With use of a specific Adapter
 * 3. Scrap data  - With use of a DataScraper
 * 4. SAVE scraped data to file using DataFactory
 *
 */

require_once 'vendor/autoload.php';


/*MAIN DATA LOADER (CURL)*/
$curlDataLoader = new CurlDataLoader();


//* SCRAP DATA FOR EUROMILONEAR */
$euromillonariaenDataScraper = new DataScraper(new EuromillonariaenDataAdapter(), $curlDataLoader);
$euromillonariaenDataScraper->setUrl('https://www.elgordo.com/results/euromillonariaen.asp');
$euromillonariaenDataScraper->scrap();
$data['euromilonear'] = $euromillonariaenDataScraper->getExtractedData();


//* SCRAP DATA FOR LOTTO */
$lottoDataScraper = new DataScraper(new LottoDataAdapter(), $curlDataLoader);
$lottoDataScraper->setUrl('https://www.lotto.pl/lotto/wyniki-i-wygrane');
$lottoDataScraper->scrap();
$data['lotto'] = $lottoDataScraper->getExtractedData();


//* SCRAP DATA FOR EUROJACKPOT */
$eurojackpotDataScraper = new DataScraper(new EurojackpotDataAdapter(), $curlDataLoader);
$eurojackpotDataScraper->setUrl('https://www.lotto.pl/eurojackpot/wyniki-i-wygrane');
$eurojackpotDataScraper->scrap();
$data['eurojackpot'] = $eurojackpotDataScraper->getExtractedData();



/*WRITE TO FILE*/
$jsonFactory = new JsonDataFactory();
$jsonFactory->writeDataToFile($data, 'DataOutput/data.json');














