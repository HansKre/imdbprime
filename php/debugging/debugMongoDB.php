<?php
require_once ('../commons.php');
require_once ('../DataService/MongoDBService.php');
require_once ('../DataService/DataOperations.php');

require '../../vendor/autoload.php'; // include Composer's autoloader

/*

Prerequisite:

1. Get the value for MONGODB_URI from:
heroku config -a imdbprime

2. set MONGODB_URI env variable from Run > Edit Configurations...

*/

if (MONGODB_URI) {

    DataOperations::addSuccessTimeStamp();
    $result = DataOperations::getAllSuccessTimeStamps();
    echo "Number of entries: " . strval(count($result));
    foreach ($result as $item) {
        // nl2br — Fügt vor allen Zeilenumbrüchen eines Strings HTML-Zeilenumbrüche ein
        echo nl2br($item['finished_successfully_at'] . "\n");
    }

}