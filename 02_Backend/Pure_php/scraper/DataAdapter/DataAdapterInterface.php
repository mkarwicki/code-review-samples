<?php
/**
 * Created by PhpStorm.
 * User: Praca
 * Date: 8/25/2018
 * Time: 12:54 PM
 */


/**
 * Interface DataAdapterInterface
 *
 * Thanks to this interface if we would like to have another
 * adapter, like for example german lottery then we will
 * know what methods to implement becouse of this interface methods.
 *
 */
interface DataAdapterInterface
{


    public function extractDataFromResponse(DOMDocument $response);


}