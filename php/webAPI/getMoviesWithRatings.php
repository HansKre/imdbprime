<?php
require_once (realpath(dirname(__FILE__)).'/../DataService/DataOperations.php');

/* Usage:
http://imdbprime-snah.rhcloud.com
/getMoviesWithRatings.php?sortBy=ratingValue&order=descending&ratingCountMin=10000
*/

    $sortBy = null;
    $order = null;
    $ratingCountMin = null;
    if (isset($_GET['sortBy'])) {
        $sortBy = $_GET['sortBy'];
    }

    if (isset($_GET['order'])) {
        $order = $_GET['order'];
    }

    if (isset($_GET['ratingCountMin'])) {
        $ratingCountMin = $_GET['ratingCountMin'];
    }

    $movies = DataOperations::getAllMoviesWithRatings();

    if ($sortBy && $order) {
        if ($sortBy === 'ratingValue' && $order === 'descending') {
            usort($movies, function($a, $b) {
                //sort descending by ratingValue
                return (floatval($b['ratingValue']) * 10) - (floatval($a['ratingValue']) * 10);
            });

        }
    }

    $moviesWithMinRatingCount = array();
    if ($ratingCountMin) {
        foreach ($movies as $movie) {
            $ratingCountString = $movie['ratingCount'];
            $ratingCountString = str_replace("," , "", $ratingCountString);
            if (intval($ratingCountString) >= intval($ratingCountMin)) {
                $moviesWithMinRatingCount[] = $movie;
            }
        }
    }
    
    // Set Last Modified header
    header('Last-Modified: '.gmdate('D, d M Y H:i:s \G\M\T', time()));
    
    // Set Expires header to 1 hour
    header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 1)));

    // return
    if (empty($moviesWithMinRatingCount)) {
        echo json_encode($movies);
    } else {
        echo json_encode($moviesWithMinRatingCount);
    }
