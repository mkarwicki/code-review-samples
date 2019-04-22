<?php
/**
 * Created by PhpStorm.
 * User: Praca
 * Date: 8/25/2018
 * Time: 12:56 PM
 */

class EuromillonariaenDataAdapter implements DataAdapterInterface
{


    public function extractDataFromResponse(DOMDocument $response): ?Array
    {
        $data = [];
        $center = $response->getElementById('center');
        $data['date'] = $this->findCurrentDate($center);
        $data['numbers'] = $this->getNumbers($center);
        return $data;
    }


    public function findCurrentDate(DOMElement $center): ?String
    {
        $divs = $center->getElementsByTagName('div');
        $tab = [];
        foreach ($divs as $key => $div) {
            foreach ($div->attributes as $attr) {
                if ($attr->name == 'class' && $attr->value == 'c') {


                    $tab[] = ($div->textContent);


                }
            }

        }
        return $tab[1];
    }


    public function getNumbers(DOMElement $center): ?Array
    {
        $divs = $center->getElementsByTagName('span');
        $tab = [];
        foreach ($divs as $key => $div) {
            foreach ($div->attributes as $attr) {
                if ($attr->name == 'class' && $attr->value == 'int-num') {
                    $tab[] = ($div->textContent);
                }
            }

        }
        return $tab;
    }


}