<?php
require_once ('commons.php');
require_once ('queryimdb.php');
require_once('primemovies.php');
require_once ('FileOperations.php');
const STATUS_200 = "Status 200";
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

$didRunPrimeMoviesToday = FileOperations::didRunPrimeMoviesToday();
myLog("=====Starting Execution.php with: $didRunPrimeMoviesToday =====");

if ($didRunPrimeMoviesToday === ReturnValues::$shouldStart) {
    if (FileOperations::markExecutionAs(ExecutionMarks::$started)) {
        if (!FileOperations::deletePrimeMoviesOutputFile()) {
            myLog("Could not delete " . FileNames::primeOutputMovies());
            return;
        }
        $myPrimeMovies = new PrimeMovies($myExecutionId);
        if ($myPrimeMovies->startQuery(1)) {
            myLog("PrimeMovies Successful");
        }
        FileOperations::markExecutionAs(ExecutionMarks::$succeeded);
    }
} else if ($didRunPrimeMoviesToday == ReturnValues::$shouldContinue) {
    if (FileOperations::markExecutionAs(ExecutionMarks::$started)) {
        $myPrimeMovies = new PrimeMovies($myExecutionId);
        if ($myPrimeMovies->continueQuery()) {
            myLog("PrimeMovies Successful");
        }
        FileOperations::markExecutionAs(ExecutionMarks::$succeeded);
    }
}

$didRunPrimeMoviesToday = FileOperations::didRunPrimeMoviesToday();
myLog("===== Execution Decision for IMDB Query is: $didRunPrimeMoviesToday =====");

if ($didRunPrimeMoviesToday === ReturnValues::$succeeded) {
    if (!file_exists(FileNames::imdbQueryFromFileName())) {
        if (!file_exists(FileNames::primeOutputMovies())) {
            myLog(FileNames::primeOutputMovies() . " does not exist");
            echo "Status 900";
            return;
        }
        FileOperations::duplicatePrimeMoviesOutputForImdbQuery();
    }
    myLog("=====Starte IMDB Query.=====");
    $imdbQuery = new ImdbQuery($myExecutionId);
    if ($imdbQuery->doQuery()) {
        // avoid running imdb query again
        FileOperations::removePrimeOutput();
        myLog("=====Fertig=====");
        echo "Status 200";
    } else {
        myLog("=====Abbruch: queryimdb.php nicht erfolgreich.=====");
        echo "Status 900";
    }
}