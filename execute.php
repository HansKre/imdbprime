<?php
ini_set('max_execution_time', 36000);

// https://ageek.de/6/php-scripte-im-hintergrund-ausfuhren/
// https://entwickler.de/online/asynchronous-io-in-php-oder-doch-lieber-threads-137913.html

$baseURL = "http://imdbprime-snah.rhcloud.com";
$cmd = "curl -s " . $baseURL . "/primevideos.php?i=399&internal=true";
$outputPrimeV = exec($cmd);
if ($outputPrimeV) {
    if ($outputPrimeV !== "Status 200") {
        echo "prime videos nicht erfolgreich.";
        return;
    }
    echo "Starte IMDB Query.";
    $cmd = "curl -s " . $baseURL . "/queryimdb.php?internal=true";
    $outputQueryI = shell($cmd);
    if ($outputQueryI) {
        if ($outputQueryI !== "Status 200") {
            echo "IMDB Query nicht erfolgreich.";
            return;
        }
    } else {
        // fehlerfall
    }
} else {
    // fehlerfall
}
?>