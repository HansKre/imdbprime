<?php
require_once ('../Legacy/FileOperations.php');

if (file_exists(FileNames::imdbQueryMoviesWithRatingsName())) {
    $videos = unserialize(file_get_contents( FileNames::imdbQueryMoviesWithRatingsName()));
    echo date ("d. F Y, H:i:s", filemtime(FileNames::imdbQueryMoviesWithRatingsName())) . ", Total Movies with Ratings: " . count($videos);
}