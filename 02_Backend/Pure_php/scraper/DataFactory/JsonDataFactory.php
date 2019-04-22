<?php
/**
 * Created by PhpStorm.
 * User: Praca
 * Date: 8/25/2018
 * Time: 1:07 PM
 */

class JsonDataFactory implements DataFactoryInterface
{


    public function writeDataToFile(Array $data, $path): void
    {
        file_put_contents($path, json_encode($data));
        echo "DATA SAVED";
    }


    public function printData(Array $data): void
    {

        echo json_encode($data);

    }


}