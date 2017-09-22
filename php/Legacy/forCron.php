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
$headers = get_headers('http://imdbprime-snah.rhcloud.com/forCron.php');

if ($headers) {
    if (substr($headers[0], 9, 3) == 200) {
        http_response_code(200);
        echo "200";
    } else {
        http_response_code(400);
        echo "400";
    }
}
*/