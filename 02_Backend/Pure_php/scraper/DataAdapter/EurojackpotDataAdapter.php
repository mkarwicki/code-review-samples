<?php
/**
 * Created by PhpStorm.
 * User: Praca
 * Date: 8/25/2018
 * Time: 2:22 PM
 */

class EurojackpotDataAdapter extends LottoDataAdapter
{



    public function extractDataFromResponse(DOMDocument $response): ?Array
    {
        $data = [];
        $data['date'] = $this->findMostRecentDate($response);
        $numbers=$this->getMostRecentNumbers($response,8);
        $data['numbers'] =  array_slice($numbers, 0, 5);
        $data['additionalNumbers'] =  array_slice($numbers, 6, 2);
        return $data;
    }

    public function findMostRecentDate(DOMDocument $response): ?String
    {
        $tableRows = $response->getElementsByTagName('tr');
        foreach ($tableRows as $key => $tableRow) {
            $i = 0;
            foreach ($tableRow->attributes as $attr) {
                if ($attr->name == 'class' && $attr->value == 'wynik') {
                    $j = 0;
                    foreach ($tableRow->childNodes as $child) {

                        if ($j == 1) {
                            return  $child->textContent;
                        }
                        $j++;
                    }
                    //$tab[]=($tableRow->textContent);
                }
               // echo 'i:' . $i . '<br>';
                $i++;
            }
        }
        return -1;
    }




}