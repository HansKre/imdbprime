<?php
const STATUS_200 = "Status 200";
const BASEURL = "http://imdbprime-snah.rhcloud.com";
ini_set('max_execution_time', 36000);
require_once ('commons.php');

// https://ageek.de/6/php-scripte-im-hintergrund-ausfuhren/
// https://entwickler.de/online/asynchronous-io-in-php-oder-doch-lieber-threads-137913.html


function isRunningOnMBA() {
    if (contains(__DIR__, "hanskrebs")) {
        return true;
    }
    return false;
}

function executePrimeVideos() {
    // allow starting from shell with: php -f execute.php 123
    // $arg[1] didn't work ...
    $i = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : 399;

    $serverCmd = "curl -s " . BASEURL . "\"/primevideos.php?internal=true&i=1\"";
    $localCmd = "php -f  primevideos.php true $i";
    $out = "";
    if (isRunningOnMBA()) {
        $out = shell_exec($localCmd);
    } else {
        $out = shell_exec($serverCmd);
    }
    if (contains($out, STATUS_200)) {
        return true;
    } else {
        return false;
    }
}

function executeQueryImdb() {
    $serverCmd = "curl -s " . BASEURL . "/queryimdb.php?internal=true";
    $localCmd = "php -f  queryimdb.php true";
    $out = "";
    if (isRunningOnMBA()) {
        $out = shell_exec($localCmd);
    } else {
        $out = shell_exec($serverCmd);
    }
    if (contains($out, STATUS_200)) {
        return true;
    } else {
        return false;
    }
}

/*============EXECUTION LOGIC==============*/

myLog("=====Starte=====");
//if (executePrimeVideos()) {
if (true) {
    myLog("=====Starte IMDB Query.=====");
    if (executeQueryImdb()) {
        myLog("=====Fertig=====");
        echo "Status 200";
    } else {
        myLog("=====Abbruch: queryimdb.php nicht erfolgreich.=====");
        echo "Status 900";
    }
} else {
    myLog("=====Abbruch: primevideos.php nicht erfolgreich.=====");
    echo "Status 900";
}
?>