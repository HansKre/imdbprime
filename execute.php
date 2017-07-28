<?php
require_once ('commons.php');
require_once ('queryimdb.php');
require_once('primemovies.php');
require_once ('getMovieFromFile.php');
const STATUS_200 = "Status 200";
const BASEURL = "http://imdbprime-snah.rhcloud.com";
ini_set('max_execution_time', '36000');
ini_set('max_input_time', '36000');

// https://ageek.de/6/php-scripte-im-hintergrund-ausfuhren/
// https://entwickler.de/online/asynchronous-io-in-php-oder-doch-lieber-threads-137913.html

function setEnvVar() {
    //todo: connect to SQL DB
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
$myPrimeMovies = new PrimeMovies($myExecutionId);
if ($myPrimeMovies->start(400)) {
    echo "done";
    return;
}
if (file_exists(fromFileName)){
    myLog("=====Starte IMDB Query.=====");
    $imdbQuery = new ImdbQuery($myExecutionId);
    if ($imdbQuery->doQuery()) {
        myLog("=====Fertig=====");
        //todo: replace "old" result with "new", so that we always have something
        echo "Status 200";
    } else {
        myLog("=====Abbruch: queryimdb.php nicht erfolgreich.=====");
        echo "Status 900";
    }
} else {
    myLog("movies.txt does not exist");
    echo "movies.txt does not exist \n";
    echo "Status 900";
}
?>