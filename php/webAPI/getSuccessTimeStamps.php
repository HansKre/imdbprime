<?php
require_once (realpath(dirname(__FILE__)).'/../DataService/DataOperations.php');

/* Usage:

https://imdbprime.herokuapp.com/php/webAPI/getSuccessTimeStamps.php

*/

$result = DataOperations::getAllSuccessTimeStamps();

header('Content-Type: text/html');

// nl2br — Fügt vor allen Zeilenumbrüchen eines Strings HTML-Zeilenumbrüche ein
echo nl2br("Number of entries: " . strval(count($result)) . "\n");

foreach ($result as $item) {
    $withRating = isset($item['numberOfMoviesWithRating']) ? $item['numberOfMoviesWithRating'] : '?';
    $skipped = isset($item['numberOfSkippedMovies']) ? $item['numberOfSkippedMovies'] : '?';

    echo nl2br($item['finished_successfully_at']
        . ": " . $withRating . " with ratings and "
        . $skipped . " skipped." . "\n");
}