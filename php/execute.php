<?php

/* IMPORTS */
require_once ('commons.php');
require_once('DataService/DataOperations.php');
require_once('queryAmazonPrime/primemovies.php');
require_once('queryIMDB/queryimdb.php');

/* Try to change PHP Server Settings (may be not possible on hosted PaaS environments) */
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

//TODO: read the following
// https://ageek.de/6/php-scripte-im-hintergrund-ausfuhren/
// https://entwickler.de/online/asynchronous-io-in-php-oder-doch-lieber-threads-137913.html


/*============EXECUTION LOGIC==============*/
$myExecutionId = rand();

$didRunPrimeMoviesToday = DataOperations::didRunPrimeMoviesToday();
myLog("=====Starting Execution.php with: $didRunPrimeMoviesToday =====");

if ($didRunPrimeMoviesToday === ReturnValues::$shouldStart) {
    if (DataOperations::markExecutionAs(ExecutionMarks::$started)) {
        DataOperations::dropPrimeMoviesCollection();
        $myPrimeMovies = new PrimeMovies($myExecutionId);
        if ($myPrimeMovies->startQuery(1)) {
            myLog("PrimeMovies Successful");
        }
        DataOperations::markExecutionAs(ExecutionMarks::$amazonQuerySucceeded);
    }
} else if ($didRunPrimeMoviesToday == ReturnValues::$shouldContinue) {
    if (DataOperations::markExecutionAs(ExecutionMarks::$started)) {
        $myPrimeMovies = new PrimeMovies($myExecutionId);
        if ($myPrimeMovies->continueQuery()) {
            myLog("PrimeMovies Successful");
        }
        DataOperations::markExecutionAs(ExecutionMarks::$amazonQuerySucceeded);
    }
}

$didRunPrimeMoviesToday = DataOperations::didRunPrimeMoviesToday();
myLog("===== Execution Decision for IMDB Query is: $didRunPrimeMoviesToday =====");

if ($didRunPrimeMoviesToday === ReturnValues::$amazonQuerySucceeded) {
    myLog("=====Starte IMDB Query.=====");
    $imdbQuery = new ImdbQuery($myExecutionId);
    if ($imdbQuery->doQuery()) {
        DataOperations::markExecutionAs(ExecutionMarks::$imdbQuerySucceeded);
        myLog("=====Fertig=====");
    } else {
        myLog("=====Abbruch: queryimdb.php nicht erfolgreich.=====");
    }
}