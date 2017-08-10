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

/*
 * Use following code to query on external webserver

<?php
file_get_contents('http://imdbprime-snah.rhcloud.com/forCron.php');

if ($http_response_header) {
    if (substr($http_response_header[0], 9, 3) == 200) {
        http_response_code(200);
    } else {
        http_response_code(400);
    }
}
*/