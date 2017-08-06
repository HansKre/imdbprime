<?php
require_once ('FileOperations.php');

if (file_exists(FileNames::imdbQueryMoviesWithRatingsName())) {
    $videos = unserialize(file_get_contents( FileNames::imdbQueryMoviesWithRatingsName()));
    echo date ("d. F Y, H:i:s", filemtime(FileNames::imdbQueryMoviesWithRatingsName()) . ", (" . count($videos) . " Videos)");
}