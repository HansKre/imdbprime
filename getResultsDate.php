<?php
require_once ('FileOperations.php');

$videos = "";
if (file_exists(FileNames::$imdbQueryMoviesWithRatingsName)) {
    $videos = unserialize(file_get_contents( FileNames::$imdbQueryMoviesWithRatingsName ));
}

if (file_exists(FileNames::$imdbQueryMoviesWithRatingsName)) {
    echo date ("d. F Y, H:i:s", filemtime(FileNames::$imdbQueryMoviesWithRatingsName)) . ", (" . count($videos) . " Videos)";
}
