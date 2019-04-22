<?php
/**
 * Created by PhpStorm.
 * User: Praca
 * Date: 8/25/2018
 * Time: 12:57 PM
 */

class DataScraper
{
    private $adapter;
    private $dataLoader;
    private $url;
    private $rawData;
    private $extractedData;


    /**
     * DataScraper constructor.
     */
    public function __construct(DataAdapterInterface $adapter, DataLoaderInterface $dataLoader)
    {
        $this->adapter = $adapter;
        $this->dataLoader = $dataLoader;

    }


    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url): void
    {
        $this->url = $url;
    }


    public function scrap(){
        $this->rawData=$this->dataLoader->getDataFromURL($this->url);
        $this->extractedData = $this->adapter->extractDataFromResponse($this->rawData);
    }


    /**
     * @return mixed
     */
    public function getExtractedData()
    {
        return $this->extractedData;
    }

    /**
     * @param mixed $extractedData
     */
    public function setExtractedData($extractedData): void
    {
        $this->extractedData = $extractedData;
    }




}