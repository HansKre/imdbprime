<?php

class FileNames {
    public static $PRIME_OUTPUT_MOVIES_TXT = './output/movies.txt';
    public static $imdbQueryFromFileName = './output/moviesForImdbQuery.txt';
    public static $imdbQuerySkippedMoviesName = './output/skippedMovies.txt';
    public static $imdbQueryMoviesWithRatingsName = './output/moviesWithRatings.txt';
    public static $imdbQuerySkippedMoviesName_temp = './output/_skippedMovies.txt';
    public static $imdbQueryMoviesWithRatingsName_temp = './output/_moviesWithRatings.txt';
    public static $markExecutionOfPrimeMoviesFileName = './output/markExecutionOfPrimeMovies.txt';
}

class ReturnValues {
    public static $shouldStart = "SHOULD_START";
    public static $shouldContinue = "SHOULD_CONTINUE";
    public static $inProgress = "IN_PROGRESS";
    public static $succeeded = "SUCCEEDED";
}

class ExecutionMarks {
    public static $started = "STARTED";
    public static $succeeded = "SUCCEEDED";
}

class FileOperations {
    public static function storeToFileThreadSave($fileName, $movie) {
        if (empty($movie)) {
            return true;
        }

        $file = null;
        $movies = null;
        if (!file_exists($fileName)) {
            // create
            $file = fopen($fileName, "w");
            $movies = array();
        } else {
            // open in r/w but without making any changes to the file
            $file = fopen($fileName,"c+");
        }
        if ($file !== false && !is_null($file)) {
            // wait till we have the exclusive lock
            flock($file,LOCK_EX);

            // read file content, but only if file has not been created by us
            if (is_null($movies)){
                $movies = unserialize( file_get_contents($fileName) );
            }

            // add movie/movies as last array entry/entries
            if (isset($movie['movie'])) {
                $movies[] = $movie;
            } else {
                foreach ($movie as $movieEntry) {
                    $movies[] = $movieEntry;
                }
            }

            // write
            file_put_contents($fileName, serialize($movies));

            // release lock
            flock($file,LOCK_UN);
            fclose($file);

            return true;
        }
        return false;
    }

    public static function getNextMovieAndRemoveItFromFile() {
        if (file_exists(FileNames::$imdbQueryFromFileName)) {
            $file = fopen(FileNames::$imdbQueryFromFileName,"c+");
            if ($file !== false) {
                // wait till we have the exclusive lock
                flock($file,LOCK_EX);

                // read file content
                $movies = unserialize( file_get_contents(FileNames::$imdbQueryFromFileName) );

                // store and remove last entry
                $lastLine = 0;
                if ($movies) {
                    $lastLine = $movies[count($movies) - 1];
                    array_pop($movies);
                }

                // delete if empty
                if (count($movies) == 0) {
                    unlink(FileNames::$imdbQueryFromFileName);
                } else {
                    file_put_contents(FileNames::$imdbQueryFromFileName, serialize($movies));
                }
                // release lock
                flock($file,LOCK_UN);
                fclose($file);

                if ($lastLine) {
                    return $lastLine;
                } else {
                    return null;
                }
            }
        } else {
            return null;
        }
        return null;
    }

    public static function duplicatePrimeMoviesOutputForImdbQuery() {
        copy(FileNames::$PRIME_OUTPUT_MOVIES_TXT, FileNames::$imdbQueryFromFileName);
    }

    public static function markExecutionAs($markString) {
        $file = null;
        $fileName = FileNames::$markExecutionOfPrimeMoviesFileName;
        $data = null;
        if (!file_exists($fileName)) {
            // create
            $file = fopen($fileName, "w");
            if (!$file) {
                myLog("Error. Could not open $fileName");
                return false;
            }
        } else {
            // open in r/w but without making any changes to the file
            $file = fopen($fileName,"c+");
        }
        if ($file !== false && !is_null($file)) {
            // wait till we have the exclusive lock
            flock($file,LOCK_EX);

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

    public static function didRunPrimeMoviesToday() {
        $file = null;
        $fileName = FileNames::$markExecutionOfPrimeMoviesFileName;
        $data = null;
        $returnValue = null;
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
                    if ($timeDiffMinutes > 20) {
                        // cron jobs get aborted after 20 minutes, therefore previous execution did not come to an end
                        $returnValue = ReturnValues::$shouldContinue;
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

    public static function deletePrimeMoviesOutputFile() {
        return unlink(FileNames::$PRIME_OUTPUT_MOVIES_TXT);
    }

    public static function replaceOldImdbQueryResults() {
        if (file_exists(FileNames::$imdbQueryMoviesWithRatingsName_temp) || file_exists(FileNames::$imdbQuerySkippedMoviesName_temp)) {
            if (unlink(FileNames::$imdbQueryMoviesWithRatingsName) || unlink(FileNames::$imdbQuerySkippedMoviesName)) {
                rename(FileNames::$imdbQueryMoviesWithRatingsName_temp, FileNames::$imdbQueryMoviesWithRatingsName);
                rename(FileNames::$imdbQuerySkippedMoviesName_temp, FileNames::$imdbQuerySkippedMoviesName);
            } else {
                myLog("Could not delete " . FileNames::$imdbQueryMoviesWithRatingsName . " or " . FileNames::$imdbQuerySkippedMoviesName);
            }
        } else {
            rename(FileNames::$imdbQueryMoviesWithRatingsName_temp, FileNames::$imdbQueryMoviesWithRatingsName);
            rename(FileNames::$imdbQuerySkippedMoviesName_temp, FileNames::$imdbQuerySkippedMoviesName);
        }
    }

    public static function whereToContinueAmazonQuery() {
        if (file_exists(FileNames::$PRIME_OUTPUT_MOVIES_TXT)) {
            $file = fopen(FileNames::$PRIME_OUTPUT_MOVIES_TXT,"c+");
            if ($file !== false) {
                // wait till we have the exclusive lock
                flock($file,LOCK_EX);

                // read file content
                $movies = unserialize( file_get_contents(FileNames::$PRIME_OUTPUT_MOVIES_TXT) );

                // store and remove last entry
                $lastPage = 0;
                if ($movies) {
                    $lastMovie = $movies[count($movies) - 1];
                    $lastPage = $lastMovie['searchPage'];
                }

                // release lock
                flock($file,LOCK_UN);
                fclose($file);

                if ($lastPage) {
                    return ($lastPage + 1);
                } else {
                    return 1;
                }
            }
        } else {
            return 1;
        }
    }
}

