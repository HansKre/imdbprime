<?php
function getMovieFromFile() {
    $fileName = "movies.txt";
    if (file_exists($fileName)) {
        $file = fopen($fileName,"c+");
        if ($file !== false) {
            // wait till we have the exclusive lock
            flock($file,LOCK_EX);

            // read file content
            $movies = unserialize( file_get_contents($fileName) );

            // store and remove last entry
            $lastLine = 0;
            if ($movies) {
                $lastLine = $movies[count($movies) - 1];
                array_pop($movies);
            }

            // delete if empty
            if (count($movies) == 0) {
                unlink($fileName);
            } else {
                file_put_contents($fileName, serialize($movies));
            }
            // release lock
            flock($file,LOCK_UN);
            fclose($file);

            if ($lastLine) {
                return $lastLine;
            } else {
                return null;
            }
        }
    } else {
        return null;
    }
    return null;
}

