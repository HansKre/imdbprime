<?php
require_once ('FileOperations.php');

if (file_exists(FileNames::imdbQueryMoviesWithRatingsName())) {
    $videos = unserialize(file_get_contents( FileNames::imdbQueryMoviesWithRatingsName()));
    if (count($videos) < 100) {
        http_response_code(400);
    } else {
        http_response_code(200);
    }
} else {
    http_response_code(400);
}