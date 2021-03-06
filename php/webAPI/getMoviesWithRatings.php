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
    
    // the following approach does not work: 
    // If both Expires and max-age are set max-age will take precedence.
    // therefore, we would need to reset the high max-age value, which is set
    // in .htaccess
    // Unfortunately, this is not possible here, because the 'Cache-Control' header
    // from .htaccess is set AFTER this php file is executed.
    
    // If both Expires and max-age are set max-age will take precedence.
    // therefore, we need to replace any max-age on this response
    //header_remove('Cache-Control');
    //header('Cache-Control: private');
    
    // Set Last Modified header
    //header('Last-Modified: '.gmdate('D, d M Y H:i:s \G\M\T', time()));
    
    // Set Expires header to 1 hour
    //header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 1)));
    
    
    
    // Better approach: set proper MIME type and then set ExpiresByType in .htaccess
    header('Content-Type: application/json');

    // return
    if (empty($moviesWithMinRatingCount)) {
        echo json_encode($movies);
    } else {
        echo json_encode($moviesWithMinRatingCount);
    }
