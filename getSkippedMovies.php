<?php
require_once ('FileOperations.php');

    $data = file_get_contents( FileNames::$imdbQuerySkippedMoviesName );
    $videos = unserialize( $data );

    usort($videos, function($a, $b) {
        //sort descending by ratingValue
        return $b['movie'] - $a['movie'];
    });

    // return
    foreach ($videos as $video) {
        echo $video['movie'] . "<br>";
    }