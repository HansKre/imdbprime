<?php
require_once (realpath(dirname(__FILE__)).'/../commons.php');
require_once ('ReturnValues.php');
require_once ('MongoDBCollections.php');
require_once ('ExecutionMarks.php');
require_once ('MongoDBService.php');

const LAST_EXECUTION_KEY = "lastExecution";
const MARK_EXECUTION_KEY = "executionMark";
const CONFIG_FOR_KEY = "configFor";
const APP_NAME = "imdbprime";

class DataOperations {
    public static function didRunPrimeMoviesToday() {
        $data = null;
        $returnValue = null;

        $controlDoc = MongoDBService::findOne(MongoDBCollections::$control, [CONFIG_FOR_KEY => APP_NAME]);
        if (!$controlDoc) {
            // there no "lastExecution" entry at all
            return ReturnValues::$shouldStart;
        } else {
            $timeDiffMinutes = self::calcTimeDiffMinutes($controlDoc);


            if (contains($controlDoc[MARK_EXECUTION_KEY], ExecutionMarks::$started)) {
                if ($timeDiffMinutes > CRON_JOB_MAX_EXECUTION_TIME) {
                    // cron jobs get aborted after CRON_JOB_MAX_EXECUTION_TIME,
                    // if we are here, the previous execution did not come to an end
                    $returnValue = ReturnValues::$shouldContinue;
                } else {
                    // still running
                    $returnValue = ReturnValues::$inProgress;
                }
            } else if (contains($controlDoc[MARK_EXECUTION_KEY], ExecutionMarks::$succeeded)) {
                // succeeded, but maybe we need to restart
                $oneDayInMinutes = 24 * 60;
                if ($timeDiffMinutes > $oneDayInMinutes) {
                    $returnValue = ReturnValues::$shouldStart;
                } else {
                    $returnValue = ReturnValues::$amazonQuerySucceeded;
                }
            } else if (contains($controlDoc[MARK_EXECUTION_KEY], ExecutionMarks::$finished)) {
                $returnValue = ReturnValues::$imdbQuerySucceeded;
            } else {
                // undefined case
                $returnValue = ReturnValues::$shouldStart;
            }
        }
        return $returnValue;
    }

    public static function markExecutionAs($markString) {
        $filter = [
            CONFIG_FOR_KEY => APP_NAME,
        ];
        $doc = [
            LAST_EXECUTION_KEY => nowAsString(),
            MARK_EXECUTION_KEY => $markString,
        ];
        return MongoDBService::updateMany(MongoDBCollections::$control, $filter, $doc);
    }

    //TODO: not needed anymore
    /*public static function storeMovies($movie) {
        // add movie/displayedMovies as last array entry/entries
        if (isset($movie['movie'])) {
            self::insertOne(self::$collectionNameAmazonPrime, $movie);
        } else {
            foreach ($movie as $movieEntry) {
                self::insertOne(self::$collectionNameAmazonPrime, $movieEntry);
            }
        }
    }*/

    private static function calcTimeDiffMinutes($controlDoc): int {
        //2017-07-29 12:05:12
        $timeZone = new DateTimeZone('Europe/Berlin');
        $now = new DateTime("", $timeZone);
        $stringAsDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $controlDoc[LAST_EXECUTION_KEY], $timeZone);
        $timeDiff = $now->diff($stringAsDateTime);
        $timeDiffMinutes = ($timeDiff->y * 365 * 24 * 60) + ($timeDiff->m * 30.5 * 24 * 60) + ($timeDiff->days * 24 * 60) + ($timeDiff->h * 60) + ($timeDiff->i);
        return $timeDiffMinutes;
    }

    public static function dropPrimeMoviesCollection() {
        MongoDBService::drop(MongoDBCollections::$foundOnAmazonPrime);
    }

    public static function storeFoundAmazonPrimeMovies(array $movies) {
        foreach ($movies as $movie) {
            if (!MongoDBService::insertOneUnique(MongoDBCollections::$foundOnAmazonPrime, $movie)) {
                echo "ERROR in DataOperations::storeAmazonPrimeMovies() while storing " .
                    $movie['movie']."\n";
            }
        }
    }

    public static function whereToContinueAmazonQuery() {
        // since we are writing all the movies of an Amazon Search Page as a bulk to the DB,
        // we can assume, that the Search Page has been processed as a whole
        $lastPage = MongoDBService::findOneMax(MongoDBCollections::$foundOnAmazonPrime, 'searchPage');
        if ($lastPage) {
            return ($lastPage + 1);
        } else {
            return 1;
        }
    }

    public static function getNextMovieAndRemoveItFromDB() {
        $filter = [];
        self::echoCountOfColByFilter(MongoDBCollections::$foundOnAmazonPrime, $filter);
        return MongoDBService::findOneAndDelete(MongoDBCollections::$foundOnAmazonPrime, $filter);
    }

    private static function echoCountOfColByFilter($colName, $filter = []) {
        echo "Remaining in " . $colName . " : "
            . MongoDBService::count($colName, $filter)."\n";
    }

    public static function storeMatchedMovie($movie) {
        if (empty($movie)) {
            return true;
        }
        return MongoDBService::insertOneUnique(MongoDBCollections::$moviesWithRatingInProgress, $movie);
    }

    public static function storeSkippedMovie($movie) {
        if (empty($movie)) {
            return true;
        }
        return MongoDBService::insertOneUnique(MongoDBCollections::$skippedMoviesInProgress, $movie);
    }

    public static function replaceOldImdbQueryResults() {
        $canReplace = MongoDBService::hasCollection(MongoDBCollections::$moviesWithRatingInProgress);
        if ($canReplace) {
            MongoDBService::drop(MongoDBCollections::$moviesWithRating);
            MongoDBService::renameCollection(MongoDBCollections::$moviesWithRatingInProgress,
                MongoDBCollections::$moviesWithRating);
        } else {
               myLog("Did not find collection, cannot replace ".MongoDBCollections::$moviesWithRatingInProgress);
        }

        $canReplace = MongoDBService::hasCollection(MongoDBCollections::$skippedMoviesInProgress);
        if ($canReplace) {
            MongoDBService::drop(MongoDBCollections::$skippedMovies);
            MongoDBService::renameCollection(MongoDBCollections::$skippedMoviesInProgress,
                MongoDBCollections::$skippedMovies);
        } else {
            myLog("Did not find collection, cannot replace ".MongoDBCollections::$skippedMoviesInProgress);
        }
    }

    public static function getAllMoviesWithRatings() : array {
        return MongoDBService::findAll(MongoDBCollections::$moviesWithRating);
    }
}