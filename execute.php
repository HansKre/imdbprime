<?php
require_once ('commons.php');
require_once ('queryimdb.php');
require_once ('primevideos.php');
const STATUS_200 = "Status 200";
const BASEURL = "http://imdbprime-snah.rhcloud.com";
ini_set('max_execution_time', '36000');
ini_set('max_input_time', '36000');

// https://ageek.de/6/php-scripte-im-hintergrund-ausfuhren/
// https://entwickler.de/online/asynchronous-io-in-php-oder-doch-lieber-threads-137913.html


function isRunningOnMBA() {
    if (contains(__DIR__, "hanskrebs")) {
        return true;
    }
    return false;
}

function executeQueryImdb($id) {
    return doQueryImdb($id);
}

function setEnvVar() {
    $openshiftsocket = getenv('OPENSHIFT_MYSQL_DB_SOCKET');
//echo "OpenShift socket is [$openshiftsocket]";

    if (isset($openshiftsocket)) {
        echo 'foo';
        ini_set('mysql.default_socket', $openshiftsocket);
    }
}

/*============EXECUTION LOGIC==============*/
$myExecutionId = rand();
myLog("=====Starte=====");
//if (executePrimeVideos($myExecutionId, 400)) {
/*executePrimeVideos($myExecutionId, 399);
if (false) {*/
if (true){
    myLog("=====Starte IMDB Query.=====");
    if (executeQueryImdb($myExecutionId)) {
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