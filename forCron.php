<?php
require_once ('FileOperations.php');

if (file_exists(FileNames::imdbQueryMoviesWithRatingsName())) {
    $videos = unserialize(file_get_contents( FileNames::imdbQueryMoviesWithRatingsName()));
    if (count($videos) < 100) {
        //empty reply
    } else {
        echo "200 OK";
    }
} else {
    //empty reply
}