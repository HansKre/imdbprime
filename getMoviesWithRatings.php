<?php
require_once ('FileOperations.php');

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

    $data = file_get_contents( FileNames::imdbQueryMoviesWithRatingsName());
    $movies = unserialize( $data );

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

    // return
    if (empty($moviesWithMinRatingCount)) {
        echo json_encode($movies);
    } else {
        echo json_encode($moviesWithMinRatingCount);
    }
