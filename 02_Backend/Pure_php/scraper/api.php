<?php
header('Access-Control-Allow-Origin: *');
$data=file_get_contents('DataOutput/data.json');
echo $data;