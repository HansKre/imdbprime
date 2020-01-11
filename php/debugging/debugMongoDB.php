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
    echo nl2br("Number of entries: " . strval(count($result)) . "\n");

    foreach ($result as $item) {
        $withRating = isset($item['numberOfMoviesWithRating']) ? $item['numberOfMoviesWithRating'] : '?';
        $skipped = isset($item['numberOfSkippedMovies']) ? $item['numberOfSkippedMovies'] : '?';

        echo nl2br($item['finished_successfully_at']
            . ": " . $withRating . " with ratings and "
            . $skipped . " skipped." . "\n");
    }
}