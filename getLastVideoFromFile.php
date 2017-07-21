<?php
function getLastVideoFromFile() {
    $fileName = "videos2.txt";
    if (file_exists($fileName)) {
        $file = fopen($fileName,"c+");
        if ($file !== false) {
            // wait till we have the exclusive lock
            flock($file,LOCK_EX);

            // read file content
            $videos = unserialize( file_get_contents($fileName) );

            // store and remove last entry
            $lastLine;
            if ($videos) {
                $lastLine = $videos[count($videos) - 1];
                array_pop($videos);
            }

            // delete if empty
            if (count($videos) == 0) {
                unlink($fileName);
            } else {
                file_put_contents($fileName, serialize($videos));
            }
            // release lock
            flock($file,LOCK_UN);
            fclose($file);

            if ($lastLine) {
                return $lastLine;
            } else {
                return false;
            }
        }
    } else {
        return false;
    }
}

