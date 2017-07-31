<?php
require_once ('FileOperations.php');

    $videos = unserialize(file_get_contents( FileNames::$imdbQuerySkippedMoviesName ));

    usort($videos, function($a, $b) {
        //sort descending by ratingValue
        return $b['movie'] - $a['movie'];
    });

    // return
    echo "Skipped videos: " . count($videos) . "<br>";
    foreach ($videos as $video) {
        echo $video['movie'] . "<br>";
    }