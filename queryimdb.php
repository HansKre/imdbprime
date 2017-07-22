<?php
const IMDBURL = 'http://www.imdb.com';
require_once ('commons.php');
require_once ('storeToFileThreadSave.php');

function containsYear($string, $year) {
    if (is_numeric($year)) {
        if (contains($string, "(".$year.")") ||
            contains($string, "(". ($year + 1) .")") ||
            contains($string, "(". ($year - 1) .")")
        ) {
            return true;
        }
    }
    return false;
}

function _getMovieUrl ($movieTitle, $year) {
    //http://www.imdb.com/find?q=Die+Unfassbaren+2+-+Now+You+See+Me+2&s=tt&exact=true&ref_=fn_tt_ex
    $part1 = '/find?q=';
    $part2 = '&s=tt&exact=true&ref_=fn_tt_ex';
    $urlForExactSearch = IMDBURL . $part1 . urlencode($movieTitle) . $part2;

    //http://www.imdb.com/find?ref_=nv_sr_fn&q=Die+Unfassbaren+2+-+Now+You+See+Me+2&s=tt
    $part1 = '/find?ref_=nv_sr_fn&q=';
    $part2 = '&s=tt';
    $urlForPopularSearch = IMDBURL . $part1 . urlencode($movieTitle) . $part2;

    //Load the HTML page
    $html = file_get_contents($urlForExactSearch);

    //Create a new DOM document
    $dom = new DOMDocument;

    //Parse the HTML. The @ is used to suppress any parsing errors
    //that will be thrown if the $html string isn't valid XHTML.
    @$dom->loadHTML($html);

    /*<table class="findList">
<tr class="findResult odd"> <td class="primary_photo">
    <a href="/title/tt1718835/?ref_=fn_tt_tt_1" >
    <img src="https://images-na.ssl-images-amazon.com/images/M/MV5BMzA2OTYzODgxNV5BMl5BanBnXkFtZTcwMzA3MTY0OA@@._V1_UX32_CR0,0,32,44_AL_.jpg" /></a> </td>
    <td class="result_text"> <a href="/title/tt1718835/?ref_=fn_tt_tt_1" >Mein liebster Alptraum</a> (2011) </td> </tr></table>
    */
    $linksArray = array();
    $resultTdElems = getElementsByClass($dom, 'td', 'result_text');
    if (!$resultTdElems) {
        //retry with other url
        $html = file_get_contents($urlForPopularSearch);
        $dom = new DOMDocument;
        @$dom->loadHTML($html);
        $resultTdElems = getElementsByClass($dom, 'td', 'result_text');
    }
    // not null, not empty
    if ($resultTdElems) {
        $linksArray = extractMovieUrlsFromHtml($year, $movieTitle, $resultTdElems);
    } else {
        // h1 class="findHeader">No results found for <span
        if (hasElementByAndSearchString($dom, "h1", "class", "findHeader", "No results found for")) {
            return "no results";
        } else {
            myLog("Unhandled case for $movieTitle and $urlForPopularSearch or $urlForExactSearch. class 'result_text' missing");
        }
        // if we get here, then it is a sign that an unknown html document was parsed
        file_put_contents("null_$movieTitle.html", $html);
        return null;
    }
    if (empty($linksArray)) {
        return "no results";
    }
    if (count($linksArray) > 1) {
        myLog("Multiple entries in linksArray for searchurl: " . $searchUrl . " \n" . print_r($linksArray));
        return "too many results";
    }
    //http://www.imdb.com/title/tt1718835/?ref_=fn_tt_tt_1
    return (IMDBURL . $linksArray[0]);
}

/**
 * @param $year
 * @param $resultTdElems
 * @param $linksArray
 * @return array
 */
function extractMovieUrlsFromHtml($year, $movieTitle, $resultTdElems) {
    $correctTdElem = null;
    //if (count($resultTdElems) > 1) {
        // mehrdeutigkeit behandeln
        $temporaryArray = array();
        $eindeutig = true;
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
                //echo "skipping: " . $resultTdElem->nodeValue . "\n";
                $eindeutig = false;
            }
            if ($eindeutig) {
                // jahr vergleichen, manchmal weicht das Amazon-Jahr vom IMDB-Jahr um +/- 1 ab
                if (containsYear($resultTdElem->nodeValue, $year)) {
                    $temporaryArray[] = $resultTdElem;
                }
            }
        } // ende foreach
        if (count($temporaryArray) > 1) {
            // immer noch nicht eindeutig
            // wir sind nun in der engeren Auswahl
            $preferred = 0;
            //im Array sind die Einträge bereits absteigend nach ihrer Trefferwahrscheinlichkeit auf Basis der IMDB-Einschätzung sortiert
            //deshalb tasten wir uns von hinten nach vorn
            for ($i = count($temporaryArray) - 1; $i = 0; $i--) {
                $nodeValue = $resultTdElem->nodeValue;
                if (!contains($nodeValue, "aka \"") && !multipleOccuranceOf($nodeValue, "(") && containsYear($nodeValue, $year)) {
                    $preferred = $i;
                }
            }
            $correctTdElem = $temporaryArray[$preferred];
        } elseif (count($temporaryArray) == 1) {
            $correctTdElem = $temporaryArray[0];
        } else {
            myLog("not a movie: $movieTitle");
            return array();
        }
    /*} else {
        // exactly 1 result
        $correctTdElem = $resultTdElems[0];
    }*/
    if ($correctTdElem) {
        //get deep link
        $aElems = $correctTdElem->getElementsByTagName('a');
        foreach ($aElems as $aElem) {
            $linksArray[] = $aElem->getAttribute('href');
        }
    } else {
        myLog("Unknown case 2");
        return array();
    }
    return $linksArray;
}

function getMovieUrl($movieTitle, $year) {
    /*$searchUrl = null;
    $sleepTimer =ONESECOND;
    while (is_null($searchUrl)) {
        $searchUrl = _getMovieUrl($movieTitle, $year);
        usleep($sleepTimer);
        $sleepTimer += ONESECOND / 2;
    }*/

    $searchUrl = _getMovieUrl($movieTitle, $year);

    $cleanMovieTitle = $movieTitle;
    if ($searchUrl == "no results") {
        //try again without substring after : and -
        $positionColon = strpos($cleanMovieTitle, ":");
        if ($positionColon !== false) {
            $cleanMovieTitle = substr($cleanMovieTitle, 0, $positionColon);
        }
        $positionMinus = strpos($cleanMovieTitle, "-");
        if ($positionMinus !== false) {
            $cleanMovieTitle = substr($cleanMovieTitle, 0, $positionMinus);
        }
        // todo: replace als change interpretieren
        // todo: remove brackets ( )
        str_replace("&","and",$cleanMovieTitle);
        //try again only if movie title changed after cleaning
        if ((($positionColon !== false) || ($positionMinus !== false)) && (strlen($cleanMovieTitle) !== 0)) {
            echo "Trying $year , $movieTitle again with: $cleanMovieTitle \n";
            // selbstaufruf
            $searchUrl = getMovieUrl($cleanMovieTitle, $year);
        }
    }
    if ($searchUrl == "no results") {
        return "";
    } elseif (is_null($searchUrl)) {
        myLog("null: Null URL bei: $movieTitle");
        return "";
    } elseif (!contains($searchUrl, IMDBURL)) {
        return "";
    }
    else {
        return $searchUrl;
    }
}

/**
 * @param $url
 */
function getMovieRating($url)
{
    //Load the HTML page
    $html = file_get_contents($url);

    //Create a new DOM document
    $dom = new DOMDocument;

    //Parse the HTML. The @ is used to suppress any parsing errors
    //that will be thrown if the $html string isn't valid XHTML.
    @$dom->loadHTML($html);

    /*<div class="ratings_wrapper">
        <div class="imdbRating" itemtype="http://schema.org/AggregateRating" itemscope="" itemprop="aggregateRating">
                <div class="ratingValue">
<strong title="7,6 based on 285.844 user ratings"><span itemprop="ratingValue">7,6</span></strong><span class="grey">/</span><span class="grey" itemprop="bestRating">10</span>                </div>
                <a href="/title/tt0096895/ratings?ref_=tt_ov_rt"
><span class="small" itemprop="ratingCount">285.844</span></a>*/
    $ratingElemsArray = getElementsBy($dom, "span", "itemprop", "ratingValue");
    $ratingCountElemsArray = getElementsBy($dom, "span", "itemprop", "ratingCount");


    /*array may be empty, if not enough ratings:
    <div class="ratings_wrapper">
        <div class="imdbRating" itemtype="http://schema.org/AggregateRating" itemscope="" itemprop="aggregateRating">
                <div class="notEnoughRatings">Needs 5 Ratings</div>
        </div>*/

    $ratingValue;
    $ratingCount;
    if (empty($ratingElemsArray)) {
        $ratingValue = "0.0";
    } else {
        $ratingValue = $ratingElemsArray[0]->nodeValue;
    }
    if (empty($ratingCountElemsArray)) {
        $ratingCount = "0";
    } else {
        $ratingCount = $ratingCountElemsArray[0]->nodeValue;
    }

    return array('ratingValue' => $ratingValue, 'ratingCount' => $ratingCount);
}

function hasTime($startTime) {
    $now = microtime(true);
    $elapsedMinutes = ($now - $startTime) / 60;
    if ($elapsedMinutes > 18) {
        return false;
    }
    return true;
}
/*********Execution Flow*************/
function doQueryImdb($randomNumber) {

    myLog($randomNumber . " Starting queryimdb.php script ");
    $startTime = microtime(true);


// https://www.amazon.de/s/ref=sr_pg_399?fst=as%3Aoff&rh=n%3A3279204031%2Cp_n_ways_to_watch%3A7448695031%2Cn%3A%213010076031%2Cn%3A3015915031&page=399&bbn=3279204031&ie=UTF8

    $data = file_get_contents( 'videos.txt' );
    $videos = unserialize( $data );
    $skippedVideo = null;
    $videoWithRating = null;
    /*$videos = array();
    $videos[] = array("2014", "The Expendables 3 - A Man's Job (ungeschnittene Kinofassung)");*/

    $hasVideos = true;
    while ($hasVideos && hasTime($startTime)) {
        $video = getLastVideoFromFile();
        // not empty & not null
        if ($video) {
            $cleanMovieTitle = $video[1];
            $position = strpos($cleanMovieTitle, " [");
            if ($position !== false) {
                $cleanMovieTitle = substr($cleanMovieTitle, 0, $position);
            }
            $searchUrl = getMovieUrl($cleanMovieTitle, $video[0]);
            //todo: warum kommt hier ein $searchUrl = null an?
            if (!is_null($searchUrl) && ($searchUrl !== "")) {
                //echo $searchUrl . "\n";
                $imdbValues = getMovieRating($searchUrl);
                if ($imdbValues['ratingValue'] !== "0.0") {
                    $videoWithRating = array('movie' => $video[1], 'year' => $video[0], 'ratingValue' => $imdbValues['ratingValue'], 'ratingCount' => $imdbValues['ratingCount'], 'searchUrl' => $searchUrl);
                    $string_data = $imdbValues['ratingValue'] . " from " . $imdbValues['ratingCount'] . " users for " . $video[0] . " , " . $video[1];
                    myLog($randomNumber . " adding: " . $string_data);
                } else {
                    $string_data = $video[0] . " , " . $video[1];
                    myLog($randomNumber . " skipping: " . $string_data);
                    $skippedVideo = $video;
                }
            } else {
                $skippedVideo = $video;
            }

        } else {
            $hasVideos = false;
        }

        $videosWithRatingsName = "videosWithRatings.txt";
        $skippedVideosName = "skippedVideos.txt";
        if ($videoWithRating) {
            print_r($videoWithRating);
            var_dump($videoWithRating);
            storeToFileThreadSave($videosWithRatingsName, $videoWithRating);
        }
        if ($skippedVideo) {
            storeToFileThreadSave($skippedVideosName, $skippedVideo);
        }
    }

    return true;
}
?>