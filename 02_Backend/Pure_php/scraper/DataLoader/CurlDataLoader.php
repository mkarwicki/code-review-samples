<?php
/**
 * Created by PhpStorm.
 * User: Praca
 * Date: 8/25/2018
 * Time: 12:41 PM
 */

class CurlDataLoader implements DataLoaderInterface
{


    public function getDataFromURL(String $url): DOMDocument
    {
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($handle);
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML($response);
        libxml_clear_errors();
        return $doc;
    }

}