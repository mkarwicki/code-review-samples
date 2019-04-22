<?php
/**
 * Created by PhpStorm.
 * User: Praca
 * Date: 8/25/2018
 * Time: 12:37 PM
 */


/**
 * Interface DataLoaderInterface
 *
 * Now we are loading data by Curl but in future
 * with this interface we can load data form
 * file, database or any other source
 *
 */
interface DataLoaderInterface
{


    public function getDataFromURL(String $url): DOMDocument;


}