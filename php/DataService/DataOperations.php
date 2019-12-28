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
const ONE_DAY_IN_MINUTES = 24 * 60;
const WEEK_IN_MINUTES = ONE_DAY_IN_MINUTES * 7;

class DataOperations {
    public static function howToExecute() {
        $data = null;
        $returnValue = null;

        $controlDoc = MongoDBService::findOne(MongoDBCollections::$control, [CONFIG_FOR_KEY => APP_NAME]);
        if (!$controlDoc) {
            // there no "lastExecution" entry at all
            return ReturnValues::$AMAZON_QUERY_SHOULD_START;
        } else {
            $timeDiffMinutes = self::calcTimeDiffMinutes($controlDoc);

            // ===== SWITCH THROUGH ALL THE 4 EXECUTION MARKS ===== //
            if (contains($controlDoc[MARK_EXECUTION_KEY], ExecutionMarks::$AMAZON_QUERY_STARTED)) {
                if ($timeDiffMinutes > WEEK_IN_MINUTES) {
                    $returnValue = ReturnValues::$AMAZON_QUERY_SHOULD_START;
                } else if ($timeDiffMinutes > CRON_JOB_MAX_EXECUTION_TIME) {
                    // cron jobs get aborted after CRON_JOB_MAX_EXECUTION_TIME,
                    // if we are here, the previous execution did not come to an end
                    $returnValue = ReturnValues::$AMAZON_QUERY_SHOULD_CONTINUE;
                } else {
                    // in production, we should never reach this scode
                    // if we are here, it may be due to concurrency and another script may be still running
                    // or we got here because we are debugging and deliberately aborted the previous execution
                    // or for some reason the script aborted before the CRON_JOB_MAX_EXECUTION_TIME
                    $returnValue = ReturnValues::$AMAZON_WAIT_FOR_PREVIOUS_QUERY_TO_FINISH;
                }
            } else if (contains($controlDoc[MARK_EXECUTION_KEY], ExecutionMarks::$AMAZON_QUERY_SUCCEEDED)) {
                // amazon query may have succeeded, but it may be too old
                if ($timeDiffMinutes > WEEK_IN_MINUTES) {
                    $returnValue = ReturnValues::$AMAZON_QUERY_SHOULD_START;
                    // it is not too old, hence we can start the IMDB qury
                } else {
                    $returnValue = ReturnValues::$IMDB_QUERY_SHOULD_START;
                }
            } else if (contains($controlDoc[MARK_EXECUTION_KEY], ExecutionMarks::$IMDB_QUERY_STARTED)) {
                // data is too old, we rerun the whole thing instead of starting the IMDB Query with old data
                if ($timeDiffMinutes > WEEK_IN_MINUTES) {
                    $returnValue = ReturnValues::$AMAZON_QUERY_SHOULD_START;
                } else {
                    $returnValue = ReturnValues::$IMDB_QUERY_SHOULD_CONTINUE;
                }
            } else if (contains($controlDoc[MARK_EXECUTION_KEY], ExecutionMarks::$IMDB_QUERY_SUCCEEDED)) {
                // imdb query may have succeeded, but it may be too old
                if ($timeDiffMinutes > WEEK_IN_MINUTES) {
                    $returnValue = ReturnValues::$AMAZON_QUERY_SHOULD_START;
                // it is not too old, hence we don't need to do anything
                } else {
                    $returnValue = ReturnValues::$ALL_UP_TO_DATE;
                }
            } else {
                // undefined case
                $returnValue = ReturnValues::$UNDEFINED;
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
                myLog("ERROR in DataOperations::storeAmazonPrimeMovies() while storing " .
                    $movie['movie']);
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
        myLog("Remaining in " . $colName . " : "
            . MongoDBService::count($colName, $filter));
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