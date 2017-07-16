<?php
ini_set('max_execution_time', 36000);
require_once ('commons.php');

// https://ageek.de/6/php-scripte-im-hintergrund-ausfuhren/
// https://entwickler.de/online/asynchronous-io-in-php-oder-doch-lieber-threads-137913.html

myLog("=====Starte=====");
$baseURL = "http://imdbprime-snah.rhcloud.com";
$cmd = "curl -s " . $baseURL . "\"/primevideos.php?internal=true&i=1\"";
$outputPrimeV = shell_exec($cmd);
/*if ($outputPrimeV) {
    echo "2";
    if ($outputPrimeV !== "Status 200") {
        echo "prime videos nicht erfolgreich.";
        return;
    }*/
    myLog("Starte IMDB Query.");
    $cmd = "curl -s " . $baseURL . "/queryimdb.php?internal=true";
    $outputQueryI = shell_exec($cmd);
    /*if ($outputQueryI) {
        if ($outputQueryI !== "Status 200") {
            echo "IMDB Query nicht erfolgreich.";
            return;
        }
    } else {
        // fehlerfall
    }
} else {
    // fehlerfall
}*/
    myLog("=====Fertig=====");
?>