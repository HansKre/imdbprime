<?php
function storeToFileThreadSave($fileName, $movie) {
    $file;
    $movies = null;
    if (!file_exists($fileName)) {
        // create
        $file = fopen($fileName, "w");
        $movies = array();
    } else {
        // open in r/w but without making any changes to the file
        $file = fopen($fileName,"c+");
    }
    if ($file !== false && !is_null($file)) {
        // wait till we have the exclusive lock
        flock($file,LOCK_EX);

        // read file content, but only if file has not been created by us
        if (is_null($movies)){
            $movies = unserialize( file_get_contents($fileName) );
        }

        // add movie as last array entry if it is not there
        if (!in_array($movie, $movies, true)) {
            $movies[] = $movie;
        }

        // write
        file_put_contents($fileName, serialize($movies));

        // release lock
        flock($file,LOCK_UN);
        fclose($file);

        return true;
    }
    return false;
}

