<?php
const IMDBURL = 'http://www.imdb.com';
const AMAZON_SEARCH_URL = 'https://www.amazon.de/s/ref=sr_pg_399?fst=as%3Aoff&rh=n%3A3279204031%2Cp_n_ways_to_watch%3A7448695031%2Cn%3A%213010076031%2Cn%3A3015915031&page=399&bbn=3279204031&ie=UTF8';
require_once ('commons.php');
require_once('FileOperations.php');
require_once('ImdbMovieRatingsRetriever.php');

class ImdbQuery {
    private $myExecutionId;
    private $startTime;
    private $skippedMovie = null;
    private $movieWithRating = null;
    private $movie = null;

    private function log($message) {
        myLog($this->myExecutionId . " " . $message);
    }

    /*
     * On Openshift, scripts get aborted after 20mins, thus to avoid data loss,
     * no further calculation happens after 19 minutes.
     */
    private function hasTime() {
        $now = time();
        $elapsedMinutes = ($now - $this->startTime) / 60;
        if ($elapsedMinutes > 18) {
            return false;
        }
        return true;
    }

    private function storeAndReset() {
        if ($this->movieWithRating) {
            FileOperations::storeToFileThreadSave(FileNames::$imdbQueryMoviesWithRatingsName_temp, $this->movieWithRating);
        }
        if ($this->skippedMovie) {
            FileOperations::storeToFileThreadSave(FileNames::$imdbQuerySkippedMoviesName_temp, $this->skippedMovie);
        }
        $this->movieWithRating = null;
        $this->skippedMovie = null;
        $this->movie = null;
    }

    private function getMovieRating() {
        $imdbMovieRatingsRetriever = new ImdbMovieRatingsRetriever($this->movie);
        $movieWithRating = $imdbMovieRatingsRetriever->getImdbMovieDetails();

        if ($movieWithRating) {
            $this->movieWithRating = $movieWithRating;
        } else {
            $this->skippedMovie = $this->movie;
        }
    }

    public function __construct($myExecutionId) {
        if ($myExecutionId) {
            $this->myExecutionId = $myExecutionId;
        } else {
            $this->myExecutionId = "no execution ID set";
        }
        $this->startTime = time();
    }

    public function doQuery() {
        $this->log("Starting IMDB Query");
        $hasMoviesToProcess = true;
        while ($hasMoviesToProcess && $this->hasTime()) {
            $this->movie = FileOperations::getNextMovieAndRemoveItFromFile();
            //$this->movie = array('year' => "2015", 'movie' => "The Lady In Black [OV]", 'director' => "Steve Spel");
            //$this->movie = array('year' => "2016", 'movie' => "Borderline - 1950 [OV]", 'director' => "Unavailable", 'actors' => array("Fred MacMurray", "Claire Trevor"));
            //$hasMoviesToProcess = false;
            if ($this->movie) { // not empty & not null
                $this->getMovieRating();
            } else {
                $hasMoviesToProcess = false;
            }
            $this->storeAndReset();
        }

        if (!$hasMoviesToProcess) {
            FileOperations::replaceOldImdbQueryResults();
        }

        return true;
    }
}