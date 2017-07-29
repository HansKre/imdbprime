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

    private function sortOutResultsWithBadKeywords($resultTdElems, $movieTitle) {
        $year = $this->movie['year'];
        $this->resetYearIfDoesNotMatchWithYearInTitle($year, $movieTitle);

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
               //skip element
            } else if (is_numeric($year) && matchesYear($resultTdElem->nodeValue, $year)) {
                // jahr vergleichen, manchmal weicht das Amazon-Jahr vom IMDB-Jahr um +/- 1 ab
                $promisingResultTdElems[] = $resultTdElem;
            } else if (!is_numeric($year)) {
                $promisingResultTdElems[] = $resultTdElem;
            }
        }
        return $promisingResultTdElems;
    }

    private function areStringsEqual($str1, $str2) {
        // $_str1 = str_replace(" ", "-", $str1);
        $_str1 = preg_replace('/[^A-Za-z0-9\-]/', '', $str1);
        $_str1 = trim($_str1);
        $_str1 = strtolower($_str1);

        //$_str2 = str_replace(" ", "-", $str2);
        $_str2 = preg_replace('/[^A-Za-z0-9\-]/', '', $str2);
        $_str2 = trim($_str2);
        $_str2 = strtolower($_str2);

        if (strcmp($_str1,$_str2) == 0) {
            return true;
        } else {
            return false;
        }
    }

    private function isSameDirector($imdbMovieDetailsDom, $directors) {
        if (!is_array($directors)) {
            if ($this->areStringsEqual($directors, "unavailable")) {
                return true;
            }
        }
        /*<div class="credit_summary_item">
            <h4 class="inline">Director:</h4>
                <span itemprop="director" itemscope itemtype="http://schema.org/Person">
            <a href="/name/nm2225959?ref_=tt_ov_dr"
            itemprop='url'><span class="itemprop" itemprop="name">Steve Spel</span></a>            </span>
        </div>*/

        $elemsArray = getElementsBy($imdbMovieDetailsDom, "h4", "class", "inline");

        $foundDirectorElems = array();
        foreach ($elemsArray as $i => $elem) {
            if (contains($elem->nodeValue, 'Director:')) {
                $foundDirectorElems = getElementsBy($imdbMovieDetailsDom, "span", "itemprop", "name");
            }
        }

        // before we get here, the movie title and director matches already
        // thus, we return true already, if at least one actor matches
        foreach ($foundDirectorElems as $foundDirectorElem) {
            $foundDirector = $foundDirectorElem->nodeValue;
            if (is_array($directors)) {
                foreach ($directors as $director) {
                    if ($this->areStringsEqual($director, $foundDirector)) {
                        return true;
                    }
                }
            } else {
                return $this->areStringsEqual($directors, $foundDirector);
            }
        }

        return false;

    }

    private function hasSameActors($imdbMovieDetailsDom, $actors) {
        /*
         * <div class="credit_summary_item">
                <h4 class="inline">Writer:</h4>
                    <span itemprop="creator" itemscope itemtype="http://schema.org/Person">
        <a href="/name/nm0293366?ref_=tt_ov_wr"
        itemprop='url'><span class="itemprop" itemprop="name">Devery Freeman</span></a> (story and screenplay)            </span>
            </div>
            <div class="credit_summary_item">
                <h4 class="inline">Stars:</h4>
                    <span itemprop="actors" itemscope itemtype="http://schema.org/Person">
        <a href="/name/nm0534045?ref_=tt_ov_st_sm"
        itemprop='url'><span class="itemprop" itemprop="name">Fred MacMurray</span></a>,             </span>
                    <span itemprop="actors" itemscope itemtype="http://schema.org/Person">
        <a href="/name/nm0872456?ref_=tt_ov_st_sm"
        itemprop='url'><span class="itemprop" itemprop="name">Claire Trevor</span></a>,             </span>
                    <span itemprop="actors" itemscope itemtype="http://schema.org/Person">
        <a href="/name/nm0000994?ref_=tt_ov_st_sm"
        itemprop='url'><span class="itemprop" itemprop="name">Raymond Burr</span></a>            </span>
                    <span class="ghost">|</span>
        <a href="fullcredits?ref_=tt_ov_st_sm"
        >See full cast & crew</a>&nbsp;&raquo;
            </div>*/

        $elemsArray = getElementsBy($imdbMovieDetailsDom, "h4", "class", "inline");

        $foundActorElems = array();
        foreach ($elemsArray as $i => $elem) {
            if (contains($elem->nodeValue, 'Writer:')) {
                $foundActorElems = getElementsBy($imdbMovieDetailsDom, "span", "itemprop", "name");
            }
        }

        // before we get here, the movie title and director matches already
        // thus, we return true already, if at least one actor matches
        foreach ($foundActorElems as $foundActorElem) {
            $foundActor = $foundActorElem->nodeValue;
            if (is_array($actors)) {
                foreach ($actors as $actor) {
                    if ($this->areStringsEqual($actor, $foundActor)) {
                        return true;
                    }
                }
            } else {
                if ($this->areStringsEqual($actors, $foundActor)) {
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

    private function findBestMatchAndExtractMovieDetailsFromPromisingResults($promisingResultTdElems, $movieTitle) {
        $year = $this->movie['year'];
        $directors = $this->movie['director'];
        $actors = $this->movie['actors'];

        $possibleMatches = array();
        //myLog($movieTitle . " has promising results: " . count($promisingResultTdElems));
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


            $isSameDirector = $this->isSameDirector($imdbMovieDetailsDom, $directors);
            $hasSameActors = $this->hasSameActors($imdbMovieDetailsDom, $actors);

            //myLog("isSamedirector: " . var_export($isSameDirector, true) . " hasSameActors: " . var_export($hasSameActors, true));

            if ($isSameDirector || $hasSameActors) {
                $ratingValue = $this->getRatingValue($imdbMovieDetailsDom);
                $ratingCount = $this->getRatingCount($imdbMovieDetailsDom);
                $possibleMatches[] = array('movie'=>$movieTitle,'director'=>$directors,'year'=>$year,'ratingValue'=>$ratingValue,'ratingCount'=>$ratingCount, 'searchUrl'=>$deepLink);
            }
        }
        if (count($possibleMatches) > 1) {
            myLog("Found more than one match:  print_r($possibleMatches)");
        } else if (count($possibleMatches) == 1) {
            return $possibleMatches[0];
        } else {
            return null;
        }
        //if (!contains($nodeValue, "aka \"") && !multipleOccuranceOf($nodeValue, "(") && containsYear($nodeValue, $year)) {
    }

    private function extractMovieDetailsFromSearchHtml($movieTitle, $resultTdElems) {
        $promisingResultTdElems = $this->sortOutResultsWithBadKeywords($resultTdElems, $movieTitle);

        if ($promisingResultTdElems) {
            return $this->findBestMatchAndExtractMovieDetailsFromPromisingResults($promisingResultTdElems, $movieTitle);
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

    private function getMovieDetailsFor ($movieTitle) {
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
            return $this->extractMovieDetailsFromSearchHtml($movieTitle, $resultTdElems);
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

        $imdbMovieDetails = $this->getMovieDetailsFor($cleanMovieTitle);

        if (is_null($imdbMovieDetails) && (contains($cleanMovieTitle, ":"))) {
            $cleanMovieTitle = $this->cleanAfterColon($cleanMovieTitle);
            $imdbMovieDetails = $this->getMovieDetailsFor($cleanMovieTitle);
        }

        if (is_null($imdbMovieDetails) && (contains($cleanMovieTitle, "-"))) {
            $cleanMovieTitle = $this->cleanAfterDash($cleanMovieTitle);
            $imdbMovieDetails = $this->getMovieDetailsFor($cleanMovieTitle);
        }

        if (is_null($imdbMovieDetails) && (contains($cleanMovieTitle, "&"))) {
            str_replace("&","and",$cleanMovieTitle);
            $imdbMovieDetails = $this->getMovieDetailsFor($cleanMovieTitle);
        }

        if (is_null($imdbMovieDetails) && ((contains($cleanMovieTitle, "(")) || (contains($cleanMovieTitle, ")")))) {
            str_replace("(", "", $cleanMovieTitle);
            str_replace(")", "", $cleanMovieTitle);
            $imdbMovieDetails = $this->getMovieDetailsFor($cleanMovieTitle);
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
            myLog("Found more than one link: print_r($linksArray)");
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
}