<?php
require_once(realpath(dirname(__FILE__)).'/../commons.php');
const EXACT = 1;
const POPULAR = 2;

class ImdbMovieRatingsRetriever {
    private $myExecutionId;
    private $movie = null;
    private $lastParsedDom;
    private $urlForImdbSearch;
    private $urlForImdbSearchArray = array();
    private $urlImdbMovie;
    private $urlImdbMovieArray = array();

    // for later debugging purposes, we want to preserve all search URLs
    private function setUrlForImdbSearch($searchUrl) {
        array_push($this->urlForImdbSearchArray, $searchUrl);
        $this->urlForImdbSearch = $searchUrl;
    }

    public function getUrlForImdbSearch() {
        return $this->urlForImdbSearchArray;
    }

    private function setUrlImdbMovie($urlImdbMovie) {
        array_push($this->urlImdbMovieArray, $urlImdbMovie);
        $this->urlImdbMovie = $urlImdbMovie;
    }

    public function getUrlImdbMovie() {
        return $this->urlImdbMovieArray;
    }

    private function log($message) {
        myLog($this->myExecutionId . " " . $message);
    }

    private function removeSquareBracketFromTitle($title) {
        $cleanMovieTitle = $title;
        // remove everything after the bracket
        $position = strpos($title, " [");
        if ($position !== false) {
            $cleanMovieTitle = substr($cleanMovieTitle, 0, $position);
        }
        // in case the brackets are missing
        $position = strpos($title, "dt.OV");
        if ($position !== false) {
            $cleanMovieTitle = substr($cleanMovieTitle, 0, $position);
        }
        // in case the brackets are missing
        $position = strpos($title, "OV");
        if ($position !== false) {
            $cleanMovieTitle = substr($cleanMovieTitle, 0, $position);
        }
        return trim($cleanMovieTitle);
    }

    private function handleFailedImdbSearch($movieTitle) {
        //todo: implement error handling
        // h1 class="findHeader">No results found for <span
        if (hasElementByAndSearchString($this->lastParsedDom, "h1", "class", "findHeader", "No results found for")) {
            $this->log('IMDB search problem: No results found for ' . $movieTitle);
            return null;
        } else {
            $this->log("Unhandled case for $movieTitle. class 'result_text' missing. saving HTML to file.");
            // if we get here, then it is a sign that an unknown html document was parsed
            file_put_contents("null_$movieTitle.html", $this->lastParsedDom->saveHTML());
            return null;
        }
    }

    private function sortOutResultsWithBadKeywords($resultTdElems, $movieTitle) {
        //$year = $this->movie['year'];
        //$this->resetYearIfDoesNotMatchWithYearInTitle($year, $movieTitle);

        $promisingResultTdElems = array();
        foreach ($resultTdElems as $resultTdElem) {
            if (contains($resultTdElem->nodeValue, "(TV Series)") ||
                contains($resultTdElem->nodeValue, "(TV Episode)") ||
                contains($resultTdElem->nodeValue, "(TV Mini-Series)") ||
                contains($resultTdElem->nodeValue, "(Short)") ||
                contains($resultTdElem->nodeValue, "(TV Movie)") ||
                contains($resultTdElem->nodeValue, "(in development)") ||
                contains($resultTdElem->nodeValue, "(Video)") ||
                contains($resultTdElem->nodeValue, "(Short)") ||
                contains($resultTdElem->nodeValue, "(Video Game)")
            ) {
               // do nothing & skip element
            /*} else if (is_numeric($year) && matchesYear($resultTdElem->nodeValue, $year)) {
                // jahr vergleichen, manchmal weicht das Amazon-Jahr vom IMDB-Jahr um +/- 2 ab
                $promisingResultTdElems[] = $resultTdElem;
            } else if (!is_numeric($year)) {
                $promisingResultTdElems[] = $resultTdElem;*/
            } else {
                $promisingResultTdElems[] = $resultTdElem;
            }
        }
        return $promisingResultTdElems;
    }

    private function areNamesEqual($str1, $str2) {
        //str1 comes from amazon
        //if (contains($str1, "Ã¶")) { --> Falsche Umlaute sind kein Thema, da Sonderzeichen komplett rausgefiltert werden

        if (contains($str1, "--")) {
            return true;
        }

        $_str1 = preg_replace('/[^A-Za-z0-9\-]/', '', $str1);
        $_str1 = trim($_str1);
        $_str1 = strtolower($_str1);

        $_str2 = preg_replace('/[^A-Za-z0-9\-]/', '', $str2);
        $_str2 = trim($_str2);
        $_str2 = strtolower($_str2);

        if (strcmp($_str1,$_str2) == 0) {
            return true;
        } else {
            // Remove abbreviated middle names e.g. Michael C. Williams
            $alternateStr1 = "";
            $alternateStr2 = "";
            $containsDot1 = contains($str1, ".");
            if ($containsDot1) {
                $str1Expl = explode(" ", $str1);
                foreach ($str1Expl as $str) {
                    if (!contains($str, ".") && !strlen($str) < 4) {
                        $alternateStr1 = $alternateStr1 . " " . $str;
                    }
                }
            } else {
                $alternateStr1 = $str1;
            }
            $containsDot2 = contains($str2, ".");
            if ($containsDot2) {
                $str2Expl = explode(" ", $str2);
                foreach ($str2Expl as $str) {
                    if (!contains($str, ".") && !strlen($str) < 4) {
                        $alternateStr2 = $alternateStr2 . " " . $str;
                    }
                }
            } else {
                $alternateStr2 = $str2;
            }
            // ... and try again
            if ($containsDot1 || $containsDot2) {
                return $this->areNamesEqual($alternateStr1, $alternateStr2);
            }

            return $this->compareByCharacter($_str1, $_str2);
        }
    }

    private function compareByCharacter($str1, $str2) {
        // falls ein Sonderzeichen z.B. bei Amazon ist und bei IMDB nicht, dann sind die Namen bis
        // auf das ausgefilterte Sonderzeichen gleich.
        // Eine "intelligente" Zeichen-basierte Suche kann helfen
        $i1 = 0;
        $i2 = 0;
        for ($i = 0; $i <= min(strlen($str1), strlen($str2)); $i++) {
            if (mb_substr($str1, $i1, 1) == mb_substr($str2, $i2, 1)) {
                $i1++;
                $i2++;
            } else if (mb_substr($str1, $i1 + 1, 1) == mb_substr($str2, $i2, 1)) {
                $i1 = $i1 + 2;
                $i2++;
            } else if (mb_substr($str1, $i1, 1) == mb_substr($str2, $i2 + 1, 1)) {
                $i1++;
                $i2 = $i2 + 2;
            } else {
                return false;
            }

        }
        return true;
    }

    private function isSameDirector($imdbMovieDetailsDom, $directors) {
        // early exit if we don't have a name
        if (!is_array($directors)) {
            if ($this->areNamesEqual($directors, "unavailable")) {
                return true;
            } else if (empty($directors)) {
                return true;
            }
        }

        $xpath = new DOMXPath($imdbMovieDetailsDom);

        //decide which director query to take
        $divElems = $xpath->query('//*[@id="title-overview-widget"]/div[2]/div');
        $directorQuery = '';
        if ($divElems->length === 2) {
            $directorQuery = '//*[@id="title-overview-widget"]/div[2]/div[2]/div[1]/div[2]/a';
        } else if (($divElems->length === 3) || ($divElems->length === 4)) {
            $directorQuery = '//*[@id="title-overview-widget"]/div[2]/div[1]/div[2]/a';
        } else {
            $this->log('Unhandled number of divElems in IMDB Movie Details Page.');
            return false;
        }
        $directorsElem = $xpath->query($directorQuery);

        foreach ($directorsElem as $directorElem) {
            $foundDirector = $directorElem->nodeValue;
            if (is_array($directors)) {
                foreach ($directors as $director) {
                    if ($this->areNamesEqual($director, $foundDirector)) {
                        return true;
                    }
                }
            } else {
                if ($this->areNamesEqual($directors, $foundDirector)) {
                    return true;
                }
            }
        }

        return false;

    }

    private function hasSameActors($imdbMovieDetailsDom, $actors) {
        $xpath = new DOMXPath($imdbMovieDetailsDom);

        //decide which stars query to take
        $divElems = $xpath->query('//*[@id="title-overview-widget"]/div[2]/div');
        $starsQuery = '';
        if ($divElems->length === 2) {
            $starsQuery = '//*[@id="title-overview-widget"]/div[2]/div[2]/div[1]/div[4]/a';
        } else if (($divElems->length === 3) || ($divElems->length === 4)) {
            $starsQuery = '//*[@id="title-overview-widget"]/div[2]/div[1]/div[4]/a';
        } else {
            $this->log('Unhandled number of divElems in IMDB Movie Details Page. ABORTING.');
            return false;
        }
        $starsElem = $xpath->query($starsQuery);

        foreach ($starsElem as $starElem) {
            $foundActor = $starElem->nodeValue;
            if (is_array($actors)) {
                foreach ($actors as $actor) {
                    if ($this->areNamesEqual($actor, $foundActor)) {
                        return true;
                    }
                }
            } else {
                if ($this->areNamesEqual($actors, $foundActor)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function getRatingValue($imdbMovieDetailsDom) {
        /*<div class="ratings_wrapper">
            <div class="imdbRating" itemtype="http://schema.org/AggregateRating" itemscope="" itemprop="aggregateRating">
                    <div class="ratingValue">
    <strong title="7,6 based on 285.844 user ratings"><span itemprop="ratingValue">7,6</span></strong><span class="grey">/</span><span class="grey" itemprop="bestRating">10</span>                </div>
                    <a href="/title/tt0096895/ratings?ref_=tt_ov_rt"
    ><span class="small" itemprop="ratingCount">285.844</span></a>*/
        $ratingValueElemsArray = getElementsBy($imdbMovieDetailsDom, "span", "itemprop", "ratingValue");


        /*array may be empty, if not enough ratings:
        <div class="ratings_wrapper">
            <div class="imdbRating" itemtype="http://schema.org/AggregateRating" itemscope="" itemprop="aggregateRating">
                    <div class="notEnoughRatings">Needs 5 Ratings</div>
            </div>*/

        $ratingValue = null;
        if (empty($ratingValueElemsArray)) {
            $ratingValue = "0.0";
        } else {
            $ratingValue = $ratingValueElemsArray[0]->nodeValue;
        }

        return $ratingValue;
    }

    private function getRatingCountString($imdbMovieDetailsDom) {
        /*<div class="ratings_wrapper">
            <div class="imdbRating" itemtype="http://schema.org/AggregateRating" itemscope="" itemprop="aggregateRating">
                    <div class="ratingValue">
    <strong title="7,6 based on 285.844 user ratings"><span itemprop="ratingValue">7,6</span></strong><span class="grey">/</span><span class="grey" itemprop="bestRating">10</span>                </div>
                    <a href="/title/tt0096895/ratings?ref_=tt_ov_rt"
    ><span class="small" itemprop="ratingCount">285.844</span></a>*/
        $ratingCountElemsArray = getElementsBy($imdbMovieDetailsDom, "span", "itemprop", "ratingCount");


        /*array may be empty, if not enough ratings:
        <div class="ratings_wrapper">
            <div class="imdbRating" itemtype="http://schema.org/AggregateRating" itemscope="" itemprop="aggregateRating">
                    <div class="notEnoughRatings">Needs 5 Ratings</div>
            </div>*/

        $ratingCount = null;
        if (empty($ratingCountElemsArray)) {
            $ratingCount = "0";
        } else {
            $ratingCount = $ratingCountElemsArray[0]->nodeValue;
        }

        return $ratingCount;
    }

    private function getYear($imdbMovieDetailsDom) {
        $xpath = new DOMXPath($imdbMovieDetailsDom);
        $yearQuery = '//*[@id="titleYear"]/a';
        $yearElem = ($xpath->query($yearQuery))[0];
        if ($yearElem) {
            return $yearElem->nodeValue;
        } else {
            return "1900";
        }
    }

    private function findBestMatchAndExtractMovieDetailsFromPromisingResults($promisingResultTdElems, $movieTitle) {
        //$year = $this->movie['year'];
        $directors = $this->movie['director'];
        $actors = $this->movie['actors'];

        $possibleMatches = array();
        //$this->log($movieTitle . " has promising results: " . count($promisingResultTdElems));
        foreach ($promisingResultTdElems as $resultTdElem) {
            //get deep link
            $this->setUrlImdbMovie($this->buildImdbMovieUrl($resultTdElem));
            $imdbMovieDetailsDom = loadAndParseHtmlFrom($this->urlImdbMovie);


            $isSameDirector = $this->isSameDirector($imdbMovieDetailsDom, $directors);
            $hasSameActors = $this->hasSameActors($imdbMovieDetailsDom, $actors);

            //$this->log("isSamedirector: " . var_export($isSameDirector, true) . " hasSameActors: " . var_export($hasSameActors, true));

            if ($isSameDirector || $hasSameActors) {
                $ratingValue = $this->getRatingValue($imdbMovieDetailsDom);
                $ratingCountString = $this->getRatingCountString($imdbMovieDetailsDom);

                // we recieve the ratingCount as a string with commas: "123,456,789"
                $ratingCount = intval(str_replace("," ,"", $ratingCountString));

                $year = $this->getYear($imdbMovieDetailsDom);
                $possibleMatches[] = array(
                    'movie'=>$this->movie['movie'],
                    'director'=>$directors,
                    'year'=>intval($year),
                    'ratingValue'=>$ratingValue,
                    'ratingCount'=>$ratingCount,
                    'ratingCountString'=>$ratingCountString,
                    'imdbMovieUrl'=>$this->urlImdbMovie
                );
            } else {
                $this->log('Actors AND Directors do not match.');
            }
        }
        if (count($possibleMatches) > 1) {
            $this->log("Found more than one match:");
            foreach ($possibleMatches as $possibleMatch) {
                $this->log("     " . $possibleMatch['movie'] . " " . $possibleMatch['imdbMovieUrl']);
            }
            $this->log("     Saving only the first.");
            return $possibleMatches[0];
        } else if (count($possibleMatches) == 1) {
            return $possibleMatches[0];
        } else {
            $this->log("Returning empty possibleMatches.");
            return null;
        }
        //if (!contains($nodeValue, "aka \"") && !multipleOccuranceOf($nodeValue, "(") && containsYear($nodeValue, $year)) {
    }

    private function extractMovieDetailsFromSearchHtml($movieTitle, $resultTdElems) {
        $promisingResultTdElems = $this->sortOutResultsWithBadKeywords($resultTdElems, $movieTitle);

        if ($promisingResultTdElems) {
            return $this->findBestMatchAndExtractMovieDetailsFromPromisingResults($promisingResultTdElems, $movieTitle);
        } else {
            $this->log('$promisingResultTdElems empty. Abort.');
            return null;
        }
    }

    private function buildSearchUrl ($searchType, $movieTitle) {
        //exact:    https://www.imdb.com/find?q=Mission%3A%20Impossible%20-%20Fallout&s=tt&ttype=ft&exact=true&ref_=fn_tt_ex
        //popular:  https://www.imdb.com/find?q=Mission%3A%20Impossible%20-%20Fallout&s=tt&ttype=ft&ref_=fn_ft
        $part1 = '/find?q=';
        if ($searchType == EXACT) {
            $part2 = '&s=tt&ttype=ft&exact=true&ref_=fn_tt_ex';
            $urlForExactSearch = IMDBURL . $part1 . urlencode($movieTitle) . $part2;
            return $urlForExactSearch;
        } else if ($searchType == POPULAR) {
            $part2 = '&s=tt&ttype=ft&ref_=fn_ft';
            $urlForPopularSearch = IMDBURL . $part1 . urlencode($movieTitle) . $part2;
            return $urlForPopularSearch;
        }
        return "";
    }

    private function doImdbSearchAndGetResultTdElems ($searchType, $movieTitle) {
        $this->setUrlForImdbSearch($this->buildSearchUrl($searchType, $movieTitle));
        $this->lastParsedDom = loadAndParseHtmlFrom($this->urlForImdbSearch);

        $resultTdElems = getElementsByClass($this->lastParsedDom, 'td', 'result_text');
        return $resultTdElems;
    }

    private function getMovieDetailsFor ($movieTitle) {
        $movieDetails = null;
        if (strlen($movieTitle) == 0) {
            $this->log('movieTitle empty, aborting.');
            return $movieDetails;
        }

        $resultTdElems = $this->doImdbSearchAndGetResultTdElems(EXACT, $movieTitle);

        if (!$resultTdElems) {//empty or null, retry with other url
            $resultTdElems = $this->doImdbSearchAndGetResultTdElems(POPULAR, $movieTitle);
        }

        // not null, not empty
        if ($resultTdElems) {//results are there
            return $this->extractMovieDetailsFromSearchHtml($movieTitle, $resultTdElems);
        } else {//either no results or we cannot parse because we received some unknown html page
            return $this->handleFailedImdbSearch($movieTitle);

        }
    }

    public function __construct($movie, $myExecutionId) {
        $this->movie = $movie;
        if ($myExecutionId) {
            $this->myExecutionId = $myExecutionId;
        } else {
            $this->myExecutionId = "no execution ID set";
        }
    }

    private function cleanAfterColon($movieTitle) {
        $cleanMovieTitle = null;
        $position = strpos($movieTitle, ":");
        if ($position !== false) {
            $cleanMovieTitle = substr($movieTitle, 0, $position);
        }
        return $cleanMovieTitle;
    }

    private function cleanAfterDash($movieTitle) {
        $cleanMovieTitle = null;
        $position = strpos($movieTitle, "-");
        if ($position !== false) {
            $cleanMovieTitle = substr($movieTitle, 0, $position);
        }
        return $cleanMovieTitle;
    }

    public function getImdbMovieDetails() {
        $cleanMovieTitle = $this->removeSquareBracketFromTitle($this->movie['movie']);

        $imdbMovieDetails = $this->getMovieDetailsFor($cleanMovieTitle);

        if (is_null($imdbMovieDetails) && (contains($cleanMovieTitle, ":"))) {
            $this->log('Unable to get MovieDetailsFor: ' . $cleanMovieTitle . ' retrying after -');
            $cleanMovieTitle = $this->cleanAfterColon($cleanMovieTitle);
            $imdbMovieDetails = $this->getMovieDetailsFor($cleanMovieTitle);
        }

        if (is_null($imdbMovieDetails) && (contains($cleanMovieTitle, "-"))) {
            $this->log('Unable to get MovieDetailsFor: ' . $cleanMovieTitle . ' retrying after -');
            $cleanMovieTitle = $this->cleanAfterDash($cleanMovieTitle);
            $imdbMovieDetails = $this->getMovieDetailsFor($cleanMovieTitle);
        }

        if (is_null($imdbMovieDetails) && (contains($cleanMovieTitle, "&"))) {
            $this->log('Unable to get MovieDetailsFor: ' . $cleanMovieTitle . ' retrying by replacing & with and');
            $cleanMovieTitle = str_replace("&","and",$cleanMovieTitle);
            $imdbMovieDetails = $this->getMovieDetailsFor($cleanMovieTitle);
            if (is_null($imdbMovieDetails)) {
                $cleanMovieTitle = str_replace("and","und",$cleanMovieTitle);
                $imdbMovieDetails = $this->getMovieDetailsFor($cleanMovieTitle);
            }
        } else if (is_null($imdbMovieDetails) && (contains($cleanMovieTitle, "und"))) {
            // "Stolz und Vorurteil" liefert falsche Ergebnisse, aber "Stolz & Vorurteil" funktioniert
            $this->log('Unable to get MovieDetailsFor: ' . $cleanMovieTitle . ' retrying by replacing und with &');
            $cleanMovieTitle = str_replace("und","&", $cleanMovieTitle);
            $imdbMovieDetails = $this->getMovieDetailsFor($cleanMovieTitle);
        }

        if (is_null($imdbMovieDetails) && ((contains($cleanMovieTitle, "(")) || (contains($cleanMovieTitle, ")")))) {
            $this->log('Unable to get MovieDetailsFor: ' . $cleanMovieTitle . ' retrying after without ()');
            $cleanMovieTitle = str_replace("(", "", $cleanMovieTitle);
            $cleanMovieTitle = str_replace(")", "", $cleanMovieTitle);
            $imdbMovieDetails = $this->getMovieDetailsFor($cleanMovieTitle);
        }

        if (is_null($imdbMovieDetails) && ($this->containsRomanNumber())) {
            $this->log('Unable to get MovieDetailsFor: ' . $cleanMovieTitle . ' retrying after without roman number');
            $cleanMovieTitle = $this->replaceRomanNumber();
            $imdbMovieDetails = $this->getMovieDetailsFor($cleanMovieTitle);
        }

        return $imdbMovieDetails;
    }

    private function buildImdbMovieUrl($resultTdElem) {
        $aElems = $resultTdElem->getElementsByTagName('a');
        $linksArray = array();
        foreach ($aElems as $aElem) {
            $linksArray[] = $aElem->getAttribute('href');
        }
        if (count($linksArray) > 1) {
            //todo: handle if multiple links are found
            $this->log("Found more than one link: print_r($linksArray)");
        }
        return  IMDBURL . $linksArray[0];
    }

    private function resetYearIfDoesNotMatchWithYearInTitle(&$year, $movieTitle)
    {
        if (containsYear($movieTitle)) {
            if ($year != containsYear($movieTitle)) {
                $year = null;
            }
        }
    }

    private function containsRomanNumber() {
        // 1 to 5
        return contains($this->movie['movie'], " I ") ||
            contains($this->movie['movie'], " II ") ||
            contains($this->movie['movie'], " III ") ||
            contains($this->movie['movie'], " IV ") ||
            contains($this->movie['movie'], " V ");
    }

    private function replaceRomanNumber() {
        if (contains($this->movie['movie'], " I ")) {
            return $this->removeSquareBracketFromTitle(str_replace(" I ", " 1 ", $this->movie['movie']));
        }

        if (contains($this->movie['movie'], " II ")) {
            return $this->removeSquareBracketFromTitle(str_replace(" II ", " 2 ", $this->movie['movie']));
        }

        if (contains($this->movie['movie'], " III ")) {
            return $this->removeSquareBracketFromTitle(str_replace(" III ", " 3 ", $this->movie['movie']));
        }

        if (contains($this->movie['movie'], " IV ")) {
            return $this->removeSquareBracketFromTitle(str_replace(" IV ", " 4 ", $this->movie['movie']));
        }

        if (contains($this->movie['movie'], " V ")) {
            return $this->removeSquareBracketFromTitle(str_replace(" V ", " 5 ", $this->movie['movie']));
        }
    }
}