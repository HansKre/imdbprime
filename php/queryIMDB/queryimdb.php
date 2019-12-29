<?php
const IMDBURL = 'https://www.imdb.com';
const AMAZON_SEARCH_URL = 'https://www.amazon.de/s/ref=sr_pg_399?fst=as%3Aoff&rh=n%3A3279204031%2Cp_n_ways_to_watch%3A7448695031%2Cn%3A%213010076031%2Cn%3A3015915031&page=399&bbn=3279204031&ie=UTF8';
require_once(realpath(dirname(__FILE__)).'/../commons.php');
require_once (realpath(dirname(__FILE__)).'/../DataService/DataOperations.php');
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

    private function hasTime() {
        return true;
        $now = time();
        $elapsedMinutes = ($now - $this->startTime) / 60;
        if ($elapsedMinutes > (CRON_JOB_MAX_EXECUTION_TIME - 2)) {
            echo "Running out of time. Stopping execution. \n";
            return false;
        }
        return true;
    }

    private function storeAndReset() {
        if ($this->movieWithRating) {
            DataOperations::storeMatchedMovie($this->movieWithRating);
        }
        if ($this->skippedMovie) {
            DataOperations::storeSkippedMovie($this->skippedMovie);
        }
        $this->movieWithRating = null;
        $this->skippedMovie = null;
        $this->movie = null;
    }

    private function getMovieDetails() {
        $imdbMovieRatingsRetriever = new ImdbMovieRatingsRetriever($this->movie, $this->myExecutionId);
        $movieWithRating = $imdbMovieRatingsRetriever->getImdbMovieDetails();

        if ($movieWithRating) {
            $this->movieWithRating = $movieWithRating;
        } else {
            // add details why the movie was skipped to facilitate later debugging
            $skippedMovie = $this->movie;
            $skippedMovie['getUrlForImdbSearch'] = $imdbMovieRatingsRetriever->getUrlForImdbSearch();
            $skippedMovie['getUrlImdbMovie'] = $imdbMovieRatingsRetriever->getUrlImdbMovie();

            $this->skippedMovie = $skippedMovie;
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

    public function doQuery($logString) {
        $this->log($logString);
        $hasMoviesToProcess = true;
        while ($hasMoviesToProcess && $this->hasTime()) {
            $this->movie = DataOperations::getNextMovieAndRemoveItFromDB();
            //$this->movie = array('year' => "0", 'movie' => "Monster und Aliens dt.OV", 'director' => "Rob Letterman", 'actors' => array("Paul Rudd"));
            //$this->movie = array('year' => "2015", 'movie' => "The Lady In Black [OV]", 'director' => "Steve Spel");
            //$this->movie = array('year' => "2016", 'movie' => "Borderline - 1950 [OV]", 'director' => "Unavailable", 'actors' => array("Fred MacMurray", "Claire Trevor"));
            $this->log('Looking up: ' . $this->movie['movie']);
            //$hasMoviesToProcess = false;
            if ($this->movie) { // not empty & not null
                $this->getMovieDetails();
            } else {
                $hasMoviesToProcess = false;
            }
            $this->storeAndReset();
        }

        if (!$hasMoviesToProcess) {
            DataOperations::replaceOldImdbQueryResults();
            $this->log("IMDB Query finished successfully");
            return true;
        }
        $this->log("IMDB Query terminated due to some error and will be restarted with next cron job");
        return false;
    }
}