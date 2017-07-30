<?php

    $sortBy = null;
    $order = null;
    if (isset($_GET['sortBy'])) {
        $sortBy = $_GET['sortBy'];
    }

    if (isset($_GET['order'])) {
        $order = $_GET['order'];
    }

    $data = file_get_contents( './output/moviesWithRatings.txt' );
    $videos = unserialize( $data );

    if ($sortBy && $order) {
        if ($sortBy === 'ratingValue' && $order === 'descending') {
            usort($videos, function($a, $b) {
                //sort descending by ratingValue
                return (floatval($b['ratingValue']) * 10) - (floatval($a['ratingValue']) * 10);
            });

        }
    }

    // return
    echo json_encode($videos);