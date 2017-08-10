<?php
require_once ('FileOperations.php');

if (file_exists(FileNames::imdbQueryMoviesWithRatingsName())) {
    $videos = unserialize(file_get_contents( FileNames::imdbQueryMoviesWithRatingsName()));
    if (count($videos) < 100) {
        echo "Status 400";
    } else {
        echo "Status 200";
    }
} else {
    echo "Status 400";
}