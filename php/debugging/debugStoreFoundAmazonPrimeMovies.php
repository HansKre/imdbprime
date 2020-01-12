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

    $movies = array();

    // fill the movies array with 2 movies
    for ($i = 0; $i < 2; $i++) {
        $year = 0;
        $movieTitle = 'My Title' . strval($i);
        $director = 'My Director' . strval($i);
        $actors = array();
        $actors[] = 'My Actor1' . strval($i);
        $actors[] = 'My Actor2' . strval($i);
        $currentAmazonPageNumber = 42;

        $movies[] = array(
            'year'=>$year,
            'movie'=>$movieTitle,
            'director'=>$director,
            'actors'=>$actors,
            'searchPage'=>$currentAmazonPageNumber
        );
    }

    DataOperations::storeFoundAmazonPrimeMovies($movies);

}