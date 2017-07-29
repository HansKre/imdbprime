<?php
require_once ('commons.php');
require_once ('queryimdb.php');
require_once('primemovies.php');
require_once('getNextMovieAndRemoveItFromFile.php');
const STATUS_200 = "Status 200";
const BASEURL = "http://imdbprime-snah.rhcloud.com";
const markExecutionOfPrimeMoviesFileName = './output/markExecutionOfPrimeMovies.txt';
ini_set('max_execution_time', '36000');
ini_set('max_input_time', '36000');

// https://ageek.de/6/php-scripte-im-hintergrund-ausfuhren/
// https://entwickler.de/online/asynchronous-io-in-php-oder-doch-lieber-threads-137913.html

class ReturnValues {
    public static $shouldStart = "SHOULD_START";
    public static $inProgress = "IN_PROGRESS";
    public static $succeeded = "SUCCEEDED";
}

class ExecutionMarks {
    public static $started = "STARTED";
    public static $succeeded = "SUCCEEDED";
}

function setEnvVar() {
    //todo: connect to SQL DB
    $openshiftsocket = getenv('OPENSHIFT_MYSQL_DB_SOCKET');
//echo "OpenShift socket is [$openshiftsocket]";

    if (isset($openshiftsocket)) {
        echo 'foo';
        ini_set('mysql.default_socket', $openshiftsocket);
    }
}

function didRunPrimeMoviesToday() {
    $file;
    $fileName = markExecutionOfPrimeMoviesFileName;
    $data = null;
    $returnValue;
    if (!file_exists($fileName)) {
        return ReturnValues::$shouldStart;
    } else {
        // open in r/w but without making any changes to the file
        $file = fopen($fileName,"c+");
    }
    if ($file !== false && !is_null($file)) {
        // wait till we have the exclusive lock
        flock($file,LOCK_EX);

        $data = file_get_contents($fileName);

        if ($data === "") {
            $returnValue = ReturnValues::$shouldStart;
        } else {
            //2017-07-29 12:05:12 STARTED
            $splitStrings = explode(" ", $data);

            $timeZone = new DateTimeZone('Europe/Berlin');
            $now = new DateTime("", $timeZone);
            $dateFromFile = DateTime::createFromFormat('Y-m-d H:i:s', $splitStrings[0] . " " . $splitStrings[1], $timeZone);
            $timeDiff = $now->diff($dateFromFile);
            $timeDiffMinutes = ($timeDiff->y * 365 * 24 * 60) + ($timeDiff->m * 30.5 * 24 * 60) + ($timeDiff->days * 24 * 60) + ($timeDiff->h * 60) + ($timeDiff->i);

            if (contains($splitStrings[2], ExecutionMarks::$started)) {
                if ($timeDiffMinutes > 25) {
                    // cron jobs get aborted after 20 minutes, therefore previous execution did not come to an end
                    $returnValue = ReturnValues::$shouldStart;
                } else {
                    // still running
                    $returnValue = ReturnValues::$inProgress;
                }
            } else if (contains($splitStrings[2], ExecutionMarks::$succeeded)) {
                $oneDayInMinutes = 24 * 60;
                if ($timeDiffMinutes > $oneDayInMinutes) {
                    $returnValue = ReturnValues::$shouldStart;
                } else {
                    $returnValue = ReturnValues::$succeeded;
                }
            }
        }
        // release lock
        flock($file,LOCK_UN);
        fclose($file);
    }
    return $returnValue;
}

function markExecutionAs($markString) {
    $file;
    $fileName = markExecutionOfPrimeMoviesFileName;
    $data = null;
    if (!file_exists($fileName)) {
        // create
        $file = fopen($fileName, "w");
    } else {
        // open in r/w but without making any changes to the file
        $file = fopen($fileName,"c+");
    }
    if ($file !== false && !is_null($file)) {
        // wait till we have the exclusive lock
        flock($file,LOCK_EX);

        $data = file_get_contents($fileName);

        $data =  nowAsString() . " " . $markString;

        // write
        file_put_contents($fileName, $data);

        // release lock
        flock($file,LOCK_UN);
        fclose($file);

        return true;
    }
    return false;
}

/*============EXECUTION LOGIC==============*/
$myExecutionId = rand();
myLog("=====Starte=====");

if (didRunPrimeMoviesToday() === ReturnValues::$shouldStart) {
    markExecutionAs(ExecutionMarks::$started);
    $myPrimeMovies = new PrimeMovies($myExecutionId);
    if ($myPrimeMovies->start(1)) {
        myLog("PrimeMovies Successful");
    }
    markEndOfExecution(ExecutionMarks::$succeeded);
}

if (didRunPrimeMoviesToday() === ReturnValues::$succeeded) {
    if (!file_exists(fromFileName)) {
        if (!file_exists(PrimeMovies::PRIME_OUTPUT_MOVIES_TXT)) {
            myLog(PrimeMovies::PRIME_OUTPUT_MOVIES_TXT . " does not exist");
            echo "Status 900";
            return;
        }
        copy(PrimeMovies::PRIME_OUTPUT_MOVIES_TXT, fromFileName);
    }
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
}