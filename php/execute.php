<?php
require_once ('commons.php');
require_once ('queryimdb.php');
require_once('primemovies.php');
require_once ('FileOperations.php');
require_once ('MongoDBService.php');
const STATUS_200 = "Status 200";
const STATUS_400 = "Status 400";
ini_set('max_execution_time', '36000');
ini_set('max_input_time', '36000');

/*
 * This file controls the data gathering.
 *
 * 1. Openshift tries to start execute.php every MINUTE
 * 2. IMPORTANT: scripts are aborted if they run more than 20minutes
 * 3. Sometimes, Amazon does bring Captcha pages and then the Amazon
 *    query lasts more than 20minutes.
 * 4. The Amazon Query must be restartable.
 *
 * 5. First, the execute script checks, whether it needs to restart
 *    and continue the Amazon PRIME script or whether it needs a fresh start
 *
 * 6. If Amazon script was successful, the IMDB query needs to be executed
 *    (again, it must be restartable. Therefore, the IMDB query takes only
 *    one movie out of the Amazon result file at a time.)
 *
 * 7. No scripts get executed if:
 *          -> Amazon query does not restart if Amazon query was successful in the last 24h
 *          -> IMDB query does not start if the Amazon-result has been processed already.
 *
 * To RESTART all queries:
 *
 * 1. Login to Openshift: rhc ssh -a imdbprime
 * 2. go to DATA DIR: cd $OEPNSHIFT_DATA_DIR
 * 3. delete markExecutionOfPrimeMovies.txt: rm markExecutionOfPrimeMovies.txt
 * 4. wait ... (after some seconds the very next cron job starts the Amazon query)
 *
 * To RESTART only IMDB quiry:
 *
 * 1. Login to Openshift: rhc ssh -a imdbprime
 * 2. go to DATA DIR: cd $OEPNSHIFT_DATA_DIR
 * 3. rename one of the last movie_processed_*.txt files:
 *    mv movies_processed_2017-08-13-20-12-12.txt  primeOutputMovies.txt
 * 4. wait ... (after some seconds the very next cron job starts the IMDB query)
 *
 */



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