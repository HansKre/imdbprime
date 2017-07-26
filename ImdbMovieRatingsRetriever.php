<?php
const EXACT = 1;
const POPULAR = 2;

class ImdbMovieRatingsRetriever {
    private $movie = null;
    private $lastParsedDom;
    private $lastLoadedHtml;

    private function removeSquareBracketFromTitle($title) {
        $cleanMovieTitle = $title;
        $position = strpos($title, " [");
        if ($position !== false) {
            $cleanMovieTitle = substr($cleanMovieTitle, 0, $position);
        }
        return $cleanMovieTitle;
    }

    private function handleFailedImdbSearch($movieTitle) {
        //todo: implement error handling
        // h1 class="findHeader">No results found for <span
        if (hasElementByAndSearchString($this->lastParsedDom, "h1", "class", "findHeader", "No results found for")) {
            return null;
        } else {
            myLog("Unhandled case for $movieTitle. class 'result_text' missing");
        }
        // if we get here, then it is a sign that an unknown html document was parsed
        file_put_contents("null_$movieTitle.html", $this->lastLoadedHtml);
        return null;
    }

    private function sortOutResultsWithBadKeywords($resultTdElems, $year) {
        $promisingResultTdElems = array();
        foreach ($resultTdElems as $resultTdElem) {
            if (contains($resultTdElem->nodeValue, "(TV Series)") ||
                contains($resultTdElem->nodeValue, "(TV Episode)") ||
                contains($resultTdElem->nodeValue, "(TV Mini-Series)") ||
                contains($resultTdElem->nodeValue, "(Short)") ||
                contains($resultTdElem->nodeValue, "(TV Movie)") ||
                contains($resultTdElem->nodeValue, "(in development)") ||
                contains($resultTdElem->nodeValue, "(Video)") ||
                contains($resultTdElem->nodeValue, "(Video Game)")
            ) {
               //skip element
            } else if (is_numeric($year) && containsYear($resultTdElem->nodeValue, $year)) {
                // jahr vergleichen, manchmal weicht das Amazon-Jahr vom IMDB-Jahr um +/- 1 ab
                $promisingResultTdElems[] = $resultTdElem;
            } else if (!is_numeric($year)) {
                $promisingResultTdElems[] = $resultTdElem;
            }
        }
        return $promisingResultTdElems;
    }

    private function areStringsEqual($str1, $str2) {
        $_str1 = strtolower(rtrim(ltrim($str1," ")," "));
        $_str1 = str_replace(".", "", $_str1);
        $_str2 = strtolower(rtrim(ltrim($str2," ")," "));
        $_str2 = str_replace(".", "", $_str2);
        if ($_str1 == $_str2) {
            return true;
        } else {
            return false;
        }
    }

    private function isSameDirector($imdbMovieDetailsDom, $director) {
        if ($this->areStringsEqual($director, "unavailable")) {
            return true;
        }
        /*<div class="credit_summary_item">
            <h4 class="inline">Director:</h4>
                <span itemprop="director" itemscope itemtype="http://schema.org/Person">
            <a href="/name/nm2225959?ref_=tt_ov_dr"
            itemprop='url'><span class="itemprop" itemprop="name">Steve Spel</span></a>            </span>
        </div>*/

        $elemsArray = getElementsBy($imdbMovieDetailsDom, "h4", "class", "inline");

        foreach ($elemsArray as $i => $elem) {
            if (contains($elem->nodeValue, 'Director:')) {
                $directorsArray = getElementsBy($imdbMovieDetailsDom, "span", "itemprop", "name");
                $foundDirector = $directorsArray[0]->nodeValue;
                return $this->areStringsEqual($director, $foundDirector);
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

        $ratingValue;
        if (empty($ratingValueElemsArray)) {
            $ratingValue = "0.0";
        } else {
            $ratingValue = $ratingValueElemsArray[0]->nodeValue;
        }

        return $ratingValue;
    }

    private function getRatingCount($imdbMovieDetailsDom) {
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

        $ratingCount;
        if (empty($ratingCountElemsArray)) {
            $ratingCount = "0";
        } else {
            $ratingCount = $ratingCountElemsArray[0]->nodeValue;
        }

        return $ratingCount;
    }

    private function findBestMatchAndExtractMovieDetailsFromPromisingResults($promisingResultTdElems, $year, $movieTitle, $director) {
        $possibleMatches = array();
        foreach ($promisingResultTdElems as $resultTdElem) {
            //get deep link
            $deepLink = $this->getDeepLink($resultTdElem);

            //Load the HTML page
            $imdbMovieDetailsHtml = file_get_contents($deepLink);

            //Create a new DOM document
            $imdbMovieDetailsDom = new DOMDocument;

            //Parse the HTML. The @ is used to suppress any parsing errors
            //that will be thrown if the $html string isn't valid XHTML.
            @$imdbMovieDetailsDom->loadHTML($imdbMovieDetailsHtml);

            //todo: Abgleich mit Schauspielern
            if ($this->isSameDirector($imdbMovieDetailsDom, $director)) {
                $ratingValue = $this->getRatingValue($imdbMovieDetailsDom);
                $ratingCount = $this->getRatingCount($imdbMovieDetailsDom);
                $possibleMatches[] = array('movie'=>$movieTitle,'director'=>$director,'year'=>$year,'ratingValue'=>$ratingValue,'ratingCount'=>$ratingCount);
            }
            if (count($possibleMatches) > 1) {
                myLog("Found more than one match: " . print_r($possibleMatches));
            }
        }
        return $possibleMatches[0];
        //if (!contains($nodeValue, "aka \"") && !multipleOccuranceOf($nodeValue, "(") && containsYear($nodeValue, $year)) {
    }

    private function extractMovieDetailsFromSearchHtml($year, $movieTitle, $director, $resultTdElems) {
        $promisingResultTdElems = $this->sortOutResultsWithBadKeywords($resultTdElems, $year);

        if ($promisingResultTdElems) {
            return $this->findBestMatchAndExtractMovieDetailsFromPromisingResults($promisingResultTdElems, $year, $movieTitle, $director);
        } else {
            return null;
        }
    }

    private function getSearchUrl ($searchType, $movieTitle) {
        if ($searchType == EXACT) {
            //http://www.imdb.com/find?q=Die+Unfassbaren+2+-+Now+You+See+Me+2&s=tt&exact=true&ref_=fn_tt_ex
            $part1 = '/find?q=';
            $part2 = '&s=tt&exact=true&ref_=fn_tt_ex';
            $urlForExactSearch = IMDBURL . $part1 . urlencode($movieTitle) . $part2;
            return $urlForExactSearch;
        } else if ($searchType == POPULAR) {
            //http://www.imdb.com/find?ref_=nv_sr_fn&q=Die+Unfassbaren+2+-+Now+You+See+Me+2&s=tt
            $part1 = '/find?ref_=nv_sr_fn&q=';
            $part2 = '&s=tt';
            $urlForPopularSearch = IMDBURL . $part1 . urlencode($movieTitle) . $part2;
            return $urlForPopularSearch;
        }
        return "";
    }

    private function doImdbSearchAndGetResultTdElems ($searchType, $movieTitle) {
        $urlForSearch = $this->getSearchUrl($searchType, $movieTitle);

        //Do the search by loading the HTML page
        $this->lastLoadedHtml = file_get_contents($urlForSearch);

        //Create a new DOM document
        $this->lastParsedDom = new DOMDocument;

        //Parse the HTML. The @ is used to suppress any parsing errors
        //that will be thrown if the $html string isn't valid XHTML.
        @$this->lastParsedDom->loadHTML($this->lastLoadedHtml);

        $resultTdElems = getElementsByClass($this->lastParsedDom, 'td', 'result_text');
        return $resultTdElems;
    }

    private function getMovieDetailsFor ($movieTitle, $year, $director) {
        $movieDetails = null;
        if (strlen($movieTitle) == 0) {
            return $movieDetails;
        }

        /*<table class="findList">
    <tr class="findResult odd"> <td class="primary_photo">
        <a href="/title/tt1718835/?ref_=fn_tt_tt_1" >
        <img src="https://images-na.ssl-images-amazon.com/images/M/MV5BMzA2OTYzODgxNV5BMl5BanBnXkFtZTcwMzA3MTY0OA@@._V1_UX32_CR0,0,32,44_AL_.jpg" /></a> </td>
        <td class="result_text"> <a href="/title/tt1718835/?ref_=fn_tt_tt_1" >Mein liebster Alptraum</a> (2011) </td> </tr></table>
        */
        $resultTdElems = $this->doImdbSearchAndGetResultTdElems(EXACT, $movieTitle);

        if (!$resultTdElems) {//empty or null, retry with other url
            $resultTdElems = $this->doImdbSearchAndGetResultTdElems(POPULAR, $movieTitle);
        }

        // not null, not empty
        if ($resultTdElems) {//results are there
            return $this->extractMovieDetailsFromSearchHtml($year, $movieTitle, $director, $resultTdElems);
        } else {//either no results or we cannot parse because we received some unknown html page
            return $this->handleFailedImdbSearch($movieTitle);

        }
    }

    public function __construct($movie) {
        $this->movie = $movie;
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

        $imdbMovieDetails = $this->getMovieDetailsFor($cleanMovieTitle, $this->movie['year'], $this->movie['director']);

        if (is_null($imdbMovieDetails) && (contains($cleanMovieTitle, ":"))) {
            $cleanMovieTitle = $this->cleanAfterColon($cleanMovieTitle);
            $imdbMovieDetails = $this->getMovieDetailsFor($cleanMovieTitle, $this->movie['year'], $this->movie['director']);
        }

        if (is_null($imdbMovieDetails) && (contains($cleanMovieTitle, "-"))) {
            $cleanMovieTitle = $this->cleanAfterDash($cleanMovieTitle);
            $imdbMovieDetails = $this->getMovieDetailsFor($cleanMovieTitle, $this->movie['year'], $this->movie['director']);
        }

        if (is_null($imdbMovieDetails) && (contains($cleanMovieTitle, "&"))) {
            str_replace("&","and",$cleanMovieTitle);
            $imdbMovieDetails = $this->getMovieDetailsFor($cleanMovieTitle, $this->movie['year'], $this->movie['director']);
        }

        if (is_null($imdbMovieDetails) && ((contains($cleanMovieTitle, "(")) || (contains($cleanMovieTitle, ")")))) {
            str_replace("(", "", $cleanMovieTitle);
            str_replace(")", "", $cleanMovieTitle);
            $imdbMovieDetails = $this->getMovieDetailsFor($cleanMovieTitle, $this->movie['year'], $this->movie['director']);
        }

        return $imdbMovieDetails;
    }

    private function getDeepLink($resultTdElem) {
        $aElems = $resultTdElem->getElementsByTagName('a');
        $linksArray = array();
        foreach ($aElems as $aElem) {
            $linksArray[] = $aElem->getAttribute('href');
        }
        if (count($linksArray) > 1) {
            //todo: handle if multiple links are found
            myLog("Found more than one link: " . print_r($linksArray));
        }
        return  IMDBURL . $linksArray[0];
    }
}