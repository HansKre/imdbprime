<?php
require_once ('FileOperations.php');

if (file_exists(FileNames::$imdbQueryMoviesWithRatingsName)) {
    echo date ("d. F Y, H:i:s", filemtime(FileNames::$imdbQueryMoviesWithRatingsName));
}
