<?php
/**
 * Created by PhpStorm.
 * User: Praca
 * Date: 8/25/2018
 * Time: 1:35 PM
 */

class LottoDataAdapter implements DataAdapterInterface
{





    public function extractDataFromResponse(DOMDocument $response): ?Array
    {
        $data = [];
        $data['date'] = $this->findMostRecentDate($response);
        $data['numbers'] = $this->getMostRecentNumbers($response,6);
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

                        if ($j == 2) {
                            return strip_tags($child->textContent);
                        }
                        $j++;
                    }
                    //$tab[]=($tableRow->textContent);
                }
                echo 'i:' . $i . '<br>';
                $i++;
            }
        }
        return -1;
    }


    public function getMostRecentNumbers(DOMDocument $response, $limit): ?Array
    {
        $tab = [];
        $tableRows = $response->getElementsByTagName('tr');
        foreach ($tableRows as $key => $tableRow) {
            $i = 0;
            foreach ($tableRow->attributes as $attr) {
                if ($attr->name == 'class' && $attr->value == 'wynik') {
                    $j = 0;
                    foreach ($tableRow->childNodes as $child) {
                        if ($j == 3) {
                            foreach ($child->getElementsByTagName('span') as $numberNode) {
                                //echo $numberNode->textContent.'<br>';
                                if (count($tab) >= $limit) {
                                    return $tab;
                                }
                                $tab[] = $numberNode->textContent;
                            }
                            break;
                        }
                        // echo 'j:'.$j.'<br>';
                        $j++;
                    }
                    //$tab[]=($tableRow->textContent);
                }
                //echo 'i:'.$i.'<br>';
                $i++;
            }
        }
        return $tab;
    }


}