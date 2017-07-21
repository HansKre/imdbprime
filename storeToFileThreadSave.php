<?php
function storeToFileThreadSave($fileName, $video) {
    $file;
    $videos = null;
    if (!file_exists($fileName)) {
        // create
        $file = fopen($fileName, "w");
        $videos = array();
    } else {
        // open in r/w but without making any changes to the file
        $file = fopen($fileName,"c+");
    }
    if ($file !== false && !is_null($file)) {
        // wait till we have the exclusive lock
        flock($file,LOCK_EX);

        // read file content, but only if file has not been created by us
        if (is_null($videos)){
            $videos = unserialize( file_get_contents($fileName) );
        }

        // add video as last array entry if it is not there
        if (!in_array($video, $videos, true)) {
            $videos[] = $video;
        }

        // write
        file_put_contents($fileName, serialize($videos));

        // release lock
        flock($file,LOCK_UN);
        fclose($file);

        return true;
    }
    return false;
}

