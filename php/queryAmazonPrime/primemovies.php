<?php
ini_set('max_execution_time', 3600);
require_once(realpath(dirname(__FILE__)).'/../commons.php');

const IS_DEBUG = false;

class PrimeMovies {
    private $currentAmazonPageNumber;
    private $executionId;
    private $startTime;

    private function log($message) {
        myLog($this->executionId . " " . $message);
    }

    private function getMoviesFromUrl($url, &$isLastResultsPage) {
        $movies = array();
        $html = null;

        // Download the HTML page
        // Loops to deal with Error from Apache-Logs:
        // PHP Warning:  file_get_contents(<url>): failed to open stream: HTTP request failed! HTTP/1.0 503 Service Unavailable
        $aging = 1;
        do {
            //Load the HTML page
            //the @ lets ignore errors (since we are retrieving HTTP status code anyway)
            //@$html = file_get_contents($url);

            // set request headers to prevent amazon from assuming that we are a bot
            $options = array(
                'http'=>array(
                    'method'=>"GET",
                    'header'=>
                        "Accept: text/html\r\n" .
                        "Accept-language: de-DE,de;q=0.9,en-US;q=0.8,en;q=0.7\r\n" .
                        "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36\r\n"
                )
            );

            $context = stream_context_create($options);
            @$html = file_get_contents($url, false, $context);

            // After gile_get_contents is executed,
            // @var array $http_response_header is created
            $httpCode = getHttpCode($http_response_header);

            if ($httpCode !== 200) {
                myLog("HTTP Code " . strval($httpCode) . " for Amazon Page " .
                    $this->currentAmazonPageNumber . " @ PrimeMovies::getMoviesFromUrl. Sleeping for " . strval($aging * 10) . "s.");
                myLog($html);
                usleep(ONESECOND * 10 * $aging);
                $aging = $aging + 1;
            }
        } while ($httpCode !== 200);

        if (IS_DEBUG) $this->log("Received 200. Continue with extracting the details.");
        if (IS_DEBUG) saveHtmlAndXmlToFile($html, $this->currentAmazonPageNumber);

        //Create a new DOM document
        $dom = new DOMDocument;

        //Parse the HTML. The @ is used to suppress any queryAmazonPrime errors
        //that will be thrown if the $html string isn't valid XHTML.
        if ($html && @$dom->loadHTML($html)) {
            if (IS_DEBUG) $this->log("dom->loadHTML succeeded.");

            $isLastResultsPage = $this->isLastResultPage($dom);

            $xpath = new DOMXPath($dom);

            /*
             * xpath to movie title1:   '//*[@id="search"]/div[1]/div/div[1]/div/span[3]/div[2]/div[1]  /div/span/div/div/div[2]/div[2]/div/div[1]/div/div/div[1]/h2/a/span'
             * xpath to movie title2:   '//*[@id="search"]/div[1]/div/div[1]/div/span[3]/div[2]/div[2]  /div/span/div/div/div[2]/div[2]/div/div[1]/div/div/div[1]/h2/a/span'
             *  alternative query       '//*[@id="search"]/div[1]/div[2]/div/span[3]/div[2]/div[1]      /div/span/div/div/div[2]/div[2]/div/div[1]/div/div/div[1]/h2/a/span
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

            $titleQuerySuffix = '/div/span/div/div/div[2]/div[2]/div/div[1]/div/div/div[1]/h2/a/span';
            // this Suffix retrieves the >first< director only: /div/span/div/div/div[2]/div[2]/div/div[2]/div[2]/div/div/ul/li[2]/span/a[1]
            // if we remove the [1] at the end, we can cycle through all the directors
            $directorQuerySuffix = '/div/span/div/div/div[2]/div[2]/div/div[2]/div[2]/div/div/ul/li[2]/span/a';

            $actorsQuerySuffix = '/div/span/div/div/div[2]/div[2]/div/div[2]/div[2]/div/div/ul/li[1]/span/a';

            //in case there are no actors or no director, the suffix is different
            $directorActorsFallbackQuerySuffix = '/div/span/div/div/div[2]/div[2]/div/div[2]/div[2]/div/div/ul/li/span/a';

            $movieCountOnPage = 1;
            $lastMovieOnPage = false;

            // iterate over the movies on current search page
            while (!$lastMovieOnPage) {
                $baseQuery = '//*[@id="search"]/div[1]/div/div[1]/div/span[3]/div[2]/div[' . $movieCountOnPage . ']';

                $movieTitleElem = $xpath->query($baseQuery . $titleQuerySuffix);
                if (!$movieTitleElem[0]) {
                    // retry with slightly different xpath-basheQuery
                    $baseQuery = '//*[@id="search"]/div[1]/div[2]/div/span[3]/div[2]/div[' . $movieCountOnPage . ']';
                    $movieTitleElem = $xpath->query($baseQuery . $titleQuerySuffix);
                    if (!$movieTitleElem[0]) {
                        $this->log("IMPORTANT: alternative query does not work!!!");
                    }
                }

                $directorElem = $xpath->query($baseQuery . $directorQuerySuffix);
                $actorsElem = $xpath->query($baseQuery . $actorsQuerySuffix);

                if (!$directorElem[0] || !$actorsElem[0]) {
                    // if we are here, there is either no actors or no director
                    // and we need to find out whether we get the one or the other
                    $labelQuerySuffix = '/div/span/div/div/div[2]/div[2]/div/div[2]/div[2]/div/div/ul/li/span/span';

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

                    // debugOut not before here to avoid the false triggers for when end of page is reached
                    if (IS_DEBUG) {
                        $this->debugOut("movieTitleElem", $movieTitleElem);
                        $this->debugOut("directorElem", $directorElem);
                        $this->debugOut("actorsElem", $actorsElem);
                    }

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
                    $year = 0; // ignore the year for now
                    $movies[] = array(
                        'year'=>$year,
                        'movie'=>$movieTitle,
                        'director'=>$director,
                        'actors'=>$actors,
                        'searchPage'=>$this->currentAmazonPageNumber
                    );
                } else if ($movieCountOnPage <= 16 && !$isLastResultsPage && !$movieTitleElem[0]) {
                    $this->log("Failed to parse movie " . $movieCountOnPage . " on current page.");
                    $this->debugOut("movieTitleElem", $movieTitleElem);
                    $this->debugOut("directorElem", $directorElem);
                    $this->debugOut("actorsElem", $actorsElem);
                } else if (!$movieTitleElem[0]) {
                    $lastMovieOnPage = true;
                } else {
                    // handle edge case... just in case...
                    $this->log("Failed to parse movie " . $movieCountOnPage . " on current page.");
                }
                $movieCountOnPage++;
            }
            if (IS_DEBUG && empty($movies)) saveHtmlAndXmlToFile($html, $this->currentAmazonPageNumber);
            return $movies;
        } else {
            // either $html was null or couldn't initate $dom from $html
            // let's assume the end is reached, otherwise this case is unhandled
            $this->log("$ html was null or $ dom->loadHTML($ html) returned null");
            $isLastResultsPage = true;
        }
        if (IS_DEBUG) $this->log("dom->loadHTML did NOT succeeded. Saving to Amazon page to file & Retruning null");
        if (IS_DEBUG) saveHtmlAndXmlToFile($html, $this->currentAmazonPageNumber);
        $this->log("Returning movies = null");
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

        $this->log("Starting primemovies.php script with i = " . $this->currentAmazonPageNumber);
        $this->startTime();

        $reachedEnd = false;

        $sleepTime = ONESECOND;
        while ($reachedEnd == false) {
            myLog($this->executionId . " Parsing page " . $this->currentAmazonPageNumber);
            $newMovies = $this -> getMoviesFromUrl($this->getSearchUrl(), $reachedEnd);
            if (!empty($newMovies)) {
                DataOperations::storeFoundAmazonPrimeMovies($newMovies);
                $this->currentAmazonPageNumber++;
            } else {
                // we assume that there should not be amazon pages with zero movies if we everything works correctly
                myLog($this->executionId . ' newMovies was empty. Trying again.');
                usleep(ONESECOND * 10);
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
            $this->currentAmazonPageNumber = $startAt;
        } else {
            $this->currentAmazonPageNumber = 1;
        }
    }

    private function getSearchUrl() {
        // all categories + rated & unrated:        https://www.amazon.de/s/ref=sr_pg_399?fst=as%3Aoff&rh=n%3A3279204031%2Cp_n_ways_to_watch%3A7448695031%2Cn%3A%213010076031%2Cn%3A3015915031&page=399&bbn=3279204031&ie=UTF8
        // category: movies + rated with >3 stars:  https://www.amazon.de/s?i=prime-instant-video&bbn=3279204031&rh=n%3A3279204031%2Cn%3A3010076031%2Cn%3A3015915031%2Cp_n_ways_to_watch%3A7448695031%2Cp_72%3A3289799031%2Cp_n_entity_type%3A9739119031&lo=list&dc&page=5&fst=as%3Aoff&qid=1564341535&rnid=9739118031&ref=sr_pg_4

        return 'https://www.amazon.de/s?i=prime-instant-video&bbn=3279204031&rh=n%3A3279204031%2Cn%3A3010076031%2Cn%3A3015915031%2Cp_n_ways_to_watch%3A7448695031%2Cp_72%3A3289799031%2Cp_n_entity_type%3A9739119031&lo=list&dc&page='
            . $this->currentAmazonPageNumber
            . '&fst=as%3Aoff&qid=1564341535&rnid=9739118031&ref=sr_pg_4';
    }

    private function isLastResultPage($dom) {

        /* if we are here, we can assume http_status_code == 200
         * in other words: we don't have to make sure that we received a proper response page from amazon
        */

        $xpath = null;
        if ($dom) {
            $xpath = new DOMXPath($dom);
        } else {
            return true;
        }

        // look for "Benötigen Sie Hilfe?"
        // if found, then return istLastResultPage == true
        /*$needHelpStr = "Benötigen Sie Hilfe?";
        $needHelpXPath = '//*[@id="search"]/div[1]/div[2]/div/span[10]/div/div/span/span/div/div/div/div/h2/span';
        $elem = $xpath->query($needHelpXPath);

        if ($elem[0] && $elem[0]->nodeValue) {
            $value = trim($elem[0]->nodeValue);
            if (contains($value, $needHelpStr)) {
                return true;
            }
        }*/

        // look for the 'Weiter' button

        $enabledWeiter =    '/html/body/div[1]/div[2]/div[1]/div/div[1]/div/span[3]/div[2]/div[17]/span/div/div/ul/li[7]/a';
        $isWeiterEnabled = !empty($xpath->query($enabledWeiter));

        if ($isWeiterEnabled) {
            // we are not on the last page, since we can click 'Weiter'
            return false;
        } else {
            // check if we are on the last page
            // we check if the 'Weiter' button is present but disabled on the last state
            /*$disabledWeiter =   '/html/body/div[1]/div[2]/div[1]/div[2]/div/span[8]/div/div/span/div/div/ul/li[7]';
            $isWeiterDisabled = empty($xpath->query($disabledWeiter));
            if ($isWeiterDisabled) {
                return true;
            }*/
            return true;
        }
    }

    private function debugOut($name, $elem) {
            if ($elem[0] && $elem[0]->nodeValue) {
                $this->log($name . ": " . trim($elem[0]->nodeValue));
            } else {
                $this->log("PrimeMovies::debugOut failed to print the nodeValue for " . $name);
            }
    }
}