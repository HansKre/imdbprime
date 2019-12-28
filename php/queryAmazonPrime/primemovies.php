<?php
ini_set('max_execution_time', 3600);
require_once(realpath(dirname(__FILE__)).'/../commons.php');

class PrimeMovies {
    private $i;
    private $executionId;
    private $startTime;

    private function log($message) {
        myLog($this->executionId . " " . $message);
    }

    private function getMoviesFromUrl($url, &$reachedEnd) {
        $movies = array();

        // Download the HTML page
        // Loops to deal with Error from Apache-Logs:
        // PHP Warning:  file_get_contents(<url>): failed to open stream: HTTP request failed! HTTP/1.0 503 Service Unavailable
        do {
            //Load the HTML page
            //the @ lets ignore errors (since we are retrieving HTTP status code anyway)
            @$html = file_get_contents($url);// After gile_get_contents is executed,

            // @var array $http_response_header is created
            $httpCode = getHttpCode($http_response_header);

            if ($httpCode !== '200') {
                myLog("Received HTTP Code " . $httpCode . " when trying to request " .
                    $url . " in PrimeMovies::getMoviesFromUrl. Sleeping for 10 seconds.");
                usleep(ONESECOND * 10);
            }
        } while ($httpCode !== '200');

        //Create a new DOM document
        $dom = new DOMDocument;

        //Parse the HTML. The @ is used to suppress any queryAmazonPrime errors
        //that will be thrown if the $html string isn't valid XHTML.
        if (@$dom->loadHTML($html)) {
            $xpath = new DOMXPath($dom);

            /*
             * xpath to movie title1:   '//*[@id="search"]/div[1]/div[2]/div/span[3]/div[1]/div[1]  /div/div/div/div[2]/div[2]/div/div[1]/div/div/div[1]/h2/a/span'
             * xpath to movie title2:   '//*[@id="search"]/div[1]/div[2]/div/span[3]/div[1]/div[2]  /div/div/div/div[2]/div[2]/div/div[1]/div/div/div[1]/h2/a/span'
             * xpath to director1:      '//*[@id="search"]/div[1]/div[2]/div/span[3]/div[1]/div[1]  /div/div/div/div[2]/div[2]/div/div[2]/div[2]/div/div/ul/li[2]/span/a'
             * xpath to director2:      '//*[@id="search"]/div[1]/div[2]/div/span[3]/div[1]/div[2]  /div/div/div/div[2]/div[2]/div/div[2]/div[2]/div/div/ul/li[2]/span/a'
             *                          '//*[@id="search"]/div[1]/div[2]/div/span[3]/div[1]/div[12] /div/div/div/div[2]/div[2]/div/div[2]/div[2]/div/div/ul/li/span/a'
             *
             * xpath to actors:         '//*[@id="search"]/div[1]/div[2]/div/span[3]/div[1]/div[1]  /div/div/div/div[2]/div[2]/div/div[2]/div[2]/div/div/ul/li[1]/span'
             *                          '//*[@id="search"]/div[1]/div[2]/div/span[3]/div[1]/div[1]  /div/div/div/div[2]/div[2]/div/div[2]/div[2]/div/div/ul/li[1]/span/a[1]'
             */

            /* cleaner approach: use the context node instead of lastMovieOnPage: https://www.php.net/manual/de/domxpath.query.php
            $searchResultsElem = $xpath->query($baseQuery);
            // our xpath query now must be only relative to the root node of the search results (omit the baseQuery)
            $someQuerySuffix = '/div/div/div/div[2]/div[2]/div/div[1]/div/div/div[1]/h2/a/span';
            $entries = $xpath->query($someQuerySuffix, $searchResultsElem);
            foreach ($entries as $entry) {
                // do something with the $entry
            }
            */

            $titleQuerySuffix = '/div/div/div/div[2]/div[2]/div/div[1]/div/div/div[1]/h2/a/span';
            $directorQuerySuffix = '/div/div/div/div[2]/div[2]/div/div[2]/div[2]/div/div/ul/li[2]/span/a';
            $actorsQuerySuffix = '/div/div/div/div[2]/div[2]/div/div[2]/div[2]/div/div/ul/li[1]/span/a';

            //in case there are no actors or no director, the suffix is differen
            $directorActorsFallbackQuerySuffix = '/div/div/div/div[2]/div[2]/div/div[2]/div[2]/div/div/ul/li/span/a';

            $movieCountOnPage = 1;
            $lastMovieOnPage = false;

            // iterate over the movies on current search page
            while (!$lastMovieOnPage) {
                $baseQuery = '//*[@id="search"]/div[1]/div[2]/div/span[3]/div[1]/div[' . $movieCountOnPage . ']';

                $movieTitleElem = $xpath->query($baseQuery . $titleQuerySuffix);
                $directorElem = $xpath->query($baseQuery . $directorQuerySuffix);
                $actorsElem = $xpath->query($baseQuery . $actorsQuerySuffix);

                if (!$directorElem[0] || !$actorsElem[0]) {
                    // if we are here, there is either no actors or no director
                    // and we need to find out whether we get the one or the other
                    $labelQuerySuffix = '/div/div/div/div[2]/div[2]/div/div[2]/div[2]/div/div/ul/li/span/span';

                    $labelElem = $xpath->query($baseQuery . $labelQuerySuffix);
                    if ($labelElem[0] && $labelElem[0]->nodeValue) {
                        if (contains($labelElem[0]->nodeValue, 'Regie')) {
                            $directorElem = $xpath->query($baseQuery . $directorActorsFallbackQuerySuffix);
                            $actorsElem = null;
                        } else if (contains($labelElem[0]->nodeValue, 'In der Hauptrolle')) {
                            $actorsElem = $xpath->query($baseQuery . $directorActorsFallbackQuerySuffix);
                            $directorElem = null;
                        }
                    }
                }

                // Validate
                if ($movieTitleElem[0] && $movieTitleElem[0]->nodeValue) {
                    $movieTitle = $movieTitleElem[0]->nodeValue;
                    // take the first director only
                    $director = '';
                    if ($directorElem[0] && $directorElem[0]->nodeValue) {
                        $director = trim($directorElem[0]->nodeValue);
                    }
                    $actors = array();
                    if ($actorsElem) {
                        foreach ($actorsElem as $actorElem) {
                            if (trim($actorElem->nodeValue)) {
                                $actors[] = trim($actorElem->nodeValue);
                            }
                        }
                    }
                    $year = 0;
                    $movies[] = array('year'=>$year, 'movie'=>$movieTitle, 'director'=>$director, 'actors'=>$actors, 'searchPage'=>$this->i);
                } else if (!$movieTitleElem[0]) {
                    $lastMovieOnPage = true;
                } else {
                    echo "not valid " . $movieCountOnPage . " on page: " . $this->i;
                }
                $movieCountOnPage++;
            }

            $reachedEnd = $this->isLastResultPage($dom);
            return $movies;
        }
        return null;
    }

    private function startTime() {
        $this->startTime = microtime(true);
    }

    private function stopTime() {
        $executionTime = (microtime(true) - $this->startTime) / 60;
        myLog($this->executionId . ' Primemovies.php finished. Total Execution Time: ' . $executionTime);
    }

    public function startQuery($startAt) {
        $this->setCounter($startAt);

        $this->log("Starting primemovies.php script with i = " . $this->i);
        $this->startTime();

        $reachedEnd = false;

        $sleepTime = ONESECOND;
        while ($reachedEnd == false) {
            myLog($this->executionId . " Parsing page " . $this->i);
            $newMovies = $this -> getMoviesFromUrl($this->getSearchUrl(), $reachedEnd);
            if (!empty($newMovies)) {
                DataOperations::storeFoundAmazonPrimeMovies($newMovies);
                $this->i++;
                if ($sleepTime > 2 * ONESECOND) {
                    $sleepTime -= 500000;
                }
            } else {
                $sleepTime += 500000;
                myLog($this->executionId . " , " . $sleepTime/1000000);
            }
            usleep($sleepTime);
        }

        $this->stopTime();

        return true;
    }

    public function continueQuery() {
        return $this->startQuery(DataOperations::whereToContinueAmazonQuery());
    }

    public function __construct($executionId) {
        if ($executionId) {
            $this->executionId = $executionId;
        } else {
            $this->executionId = rand();
        }
    }

    private function setCounter($startAt)
    {
        if ($startAt) {
            $this->i = $startAt;
        } else {
            $this->i = 1;
        }
    }

    private function getSearchUrl() {
        // all categories + rated & unrated:        https://www.amazon.de/s/ref=sr_pg_399?fst=as%3Aoff&rh=n%3A3279204031%2Cp_n_ways_to_watch%3A7448695031%2Cn%3A%213010076031%2Cn%3A3015915031&page=399&bbn=3279204031&ie=UTF8
        // category: movies + rated with >3 stars:  https://www.amazon.de/s?i=prime-instant-video&bbn=3279204031&rh=n%3A3279204031%2Cn%3A3010076031%2Cn%3A3015915031%2Cp_n_ways_to_watch%3A7448695031%2Cp_72%3A3289799031%2Cp_n_entity_type%3A9739119031&lo=list&dc&page=5&fst=as%3Aoff&qid=1564341535&rnid=9739118031&ref=sr_pg_4

        return 'https://www.amazon.de/s?i=prime-instant-video&bbn=3279204031&rh=n%3A3279204031%2Cn%3A3010076031%2Cn%3A3015915031%2Cp_n_ways_to_watch%3A7448695031%2Cp_72%3A3289799031%2Cp_n_entity_type%3A9739119031&lo=list&dc&page='
            . $this->i
            . '&fst=as%3Aoff&qid=1564341535&rnid=9739118031&ref=sr_pg_4';
    }

    private function isLastResultPage($dom) {
    //<li class="a-disabled a-last">Weiter<span class="a-letter-space"></span><span class="a-letter-space"></span>→</li>
        $elems = getElementsByClass($dom, 'li', 'a-disabled a-last');
        if ($elems) {
            foreach ($elems as $elem) {
                if (strpos($elem->nodeValue, 'Weiter') !== false) {
                    return true;
                }
            }
        }
        return false;
    }
}