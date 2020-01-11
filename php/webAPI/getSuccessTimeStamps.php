<?php
require_once (realpath(dirname(__FILE__)).'/../DataService/DataOperations.php');

/* Usage:

https://imdbprime.herokuapp.com/php/webAPI/getSuccessTimeStamps.php

*/

$result = DataOperations::getAllSuccessTimeStamps();

header('Content-Type: application/HTML');

echo "Number of entries: " . strval(count($result));

foreach ($result as $item) {
    // nl2br — Fügt vor allen Zeilenumbrüchen eines Strings HTML-Zeilenumbrüche ein
    echo nl2br($item['finished_successfully_at'] . "\n");
}
