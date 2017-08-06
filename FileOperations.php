<?php

class FileNames {
    const OPENSHIFT_DATA_DIR = 'OPENSHIFT_DATA_DIR';
    const LOCAL_OUT = './output/';

    private static $primeOutputName = 'movies.txt';
    private static $imdbQueryFromFileName = 'moviesForImdbQuery.txt';
    private static $imdbQuerySkippedMoviesName = 'skippedMovies.txt';
    private static $imdbQueryMoviesWithRatingsName = 'moviesWithRatings.txt';
    private static $imdbQuerySkippedMoviesName_temp = '_skippedMovies.txt';
    private static $imdbQueryMoviesWithRatingsName_temp = '_moviesWithRatings.txt';
    private static $markExecutionOfPrimeMoviesFileName = 'markExecutionOfPrimeMovies.txt';

    // Datadir: /var/lib/openshift/59712c0489f5cf1e5000005a/app-root/data/
    privat static $dataDir;

    private static function getOpenshiftDataDir() {
        if (self::$dataDir) {
            return self::$dataDir;
        } else if (isset($_ENV[self::OPENSHIFT_DATA_DIR])) {
            self::$dataDir = $_ENV[self::OPENSHIFT_DATA_DIR];
            return self::$dataDir;
        } else {
            return "";
        }
    }

    private static function getFileNameFor($fileName) {
        $dataDir = self::getOpenshiftDataDir();
        if ($dataDir) {
            // /var/lib/openshift/59712c0489f5cf1e5000005a/app-root/data//output/movies.txt
            return $dataDir . $fileName;
        } else {
            // ./output/movies.txt
            return LOCAL_OUT . $fileName;
        }
    }

    public static function primeOutputMovies() {
        return self::getFileNameFor(self::$primeOutputName);
    }

    public static function imdbQueryFromFileName() {
        return self::getFileNameFor(self::$imdbQueryFromFileName);
    }

    public static function imdbQuerySkippedMoviesName() {
        return self::getFileNameFor(self::$imdbQuerySkippedMoviesName);
    }

    public static function imdbQueryMoviesWithRatingsName() {
        return self::getFileNameFor(self::$imdbQueryMoviesWithRatingsName);
    }

    public static function imdbQuerySkippedMoviesName_temp() {
        return self::getFileNameFor(self::$imdbQuerySkippedMoviesName_temp);
    }

    public static function imdbQueryMoviesWithRatingsName_temp() {
        return self::getFileNameFor(self::$imdbQueryMoviesWithRatingsName_temp);
    }

    public static function markExecutionOfPrimeMoviesFileName() {
        return self::getFileNameFor(self::$markExecutionOfPrimeMoviesFileName);
    }
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
        if (file_exists(FileNames::imdbQueryFromFileName())) {
            $file = fopen(FileNames::imdbQueryFromFileName(),"c+");
            if ($file !== false) {
                // wait till we have the exclusive lock
                flock($file,LOCK_EX);

                // read file content
                $movies = unserialize( file_get_contents(FileNames::imdbQueryFromFileName()) );

                // store and remove last entry
                $lastLine = 0;
                if ($movies) {
                    $lastLine = $movies[count($movies) - 1];
                    array_pop($movies);
                }

                // delete if empty
                if (count($movies) == 0) {
                    unlink(FileNames::imdbQueryFromFileName());
                } else {
                    file_put_contents(FileNames::imdbQueryFromFileName(), serialize($movies));
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
        copy(FileNames::primeOutputMovies(), FileNames::imdbQueryFromFileName());
    }

    public static function markExecutionAs($markString) {
        $file = null;
        $fileName = FileNames::markExecutionOfPrimeMoviesFileName();
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
        $fileName = FileNames::markExecutionOfPrimeMoviesFileName();
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
        if (file_exists(FileNames::primeOutputMovies())) {
            return unlink(FileNames::primeOutputMovies());
        } else {
            return true;
        }
    }

    public static function replaceOldImdbQueryResults() {
        $canRename = false;
        if (file_exists(FileNames::imdbQueryMoviesWithRatingsName())) {
            if (unlink(FileNames::imdbQueryMoviesWithRatingsName())) {
                $canRename = true;
            }  else {
                myLog("Could not delete " . FileNames::imdbQueryMoviesWithRatingsName());
            }
        } else {
            $canRename = true;
        }

        if ($canRename) {
            rename(FileNames::imdbQueryMoviesWithRatingsName()_temp, FileNames::imdbQueryMoviesWithRatingsName());
        } else {
            myLog("Could not replace " . FileNames::imdbQueryMoviesWithRatingsName());
        }

        $canRename = false;
        if (file_exists(FileNames::imdbQuerySkippedMoviesName())) {
            if (unlink(FileNames::imdbQuerySkippedMoviesName())) {
                $canRename = true;
                rename(FileNames::imdbQuerySkippedMoviesName()_temp, FileNames::imdbQuerySkippedMoviesName());
            } else {
                myLog("Could not delete " . FileNames::imdbQuerySkippedMoviesName());
            }
        } else {
            $canRename = true;
        }

        if ($canRename) {
            rename(FileNames::imdbQuerySkippedMoviesName()_temp, FileNames::imdbQuerySkippedMoviesName());
        } else {
            myLog("Could not replace " . FileNames::imdbQuerySkippedMoviesName());
        }
    }

    public static function whereToContinueAmazonQuery() {
        if (file_exists(FileNames::primeOutputMovies())) {
            $file = fopen(FileNames::primeOutputMovies(),"c+");
            if ($file !== false) {
                // wait till we have the exclusive lock
                flock($file,LOCK_EX);

                // read file content
                $movies = unserialize( file_get_contents(FileNames::primeOutputMovies()) );

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

    public static function removePrimeOutput() {
        $newFileName = str_replace('.txt', '', FileNames::primeOutputMovies()) . '_processed_' . nowAsStringWithFormat('Y-m-d-H-i-s') . '.txt';
        rename(FileNames::primeOutputMovies(), $newFileName);
    }
}

