<?php
ini_set('max_execution_time', 18000);
require_once ('commons.php');

function containsYear($string, $year) {
    if (contains($string, "(".$year.")") ||
        contains($string, "(". ($year + 1) .")") ||
        contains($string, "(". ($year - 1) .")")
    ) {
        return true;
    }
    return false;
}

function getMovieUrl ($movieTitle, $year) {
    //http://www.imdb.com/find?q=Star%20Wars%3A%20The%20last%20Jedi&s=tt&exact=true&ref_=fn_tt_ex
    $baseUrl = 'http://www.imdb.com';
    $part1 = '/find?q=';
    $part2 = '&s=tt&exact=true&ref_=fn_tt_ex';
    $searchUrl = $baseUrl . $part1 . urlencode($movieTitle) . $part2;

    //Load the HTML page
    $html = file_get_contents($searchUrl);

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
    if ($resultTdElems) {
        $correctTdElem = null;
        if (count($resultTdElems) > 1) {
            // mehrdeutigkeit behandeln
            $temporaryArray = array();
            $eindeutig = true;
            foreach ($resultTdElems as $resultTdElem) {
                echo $movieTitle . " ist mehrdeutig: " . $resultTdElem->nodeValue . "\n";
                if (contains($resultTdElem->nodeValue, "(TV Series)") ||
                    contains($resultTdElem->nodeValue, "(TV Episode)") ||
                    contains($resultTdElem->nodeValue, "(TV Mini-Series)") ||
                    contains($resultTdElem->nodeValue, "(Short)") ||
                    contains($resultTdElem->nodeValue, "(TV Movie)") ||
                    contains($resultTdElem->nodeValue, "(in development)") ||
                    contains($resultTdElem->nodeValue, "(Video)") ||
                    contains($resultTdElem->nodeValue, "(Video Game)")
                ) {
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
                for ($i = count($temporaryArray) - 1; $i=0; $i--) {
                    $nodeValue = $resultTdElem->nodeValue;
                    if (!contains($nodeValue, "aka \"") && !multipleOccuranceOf($nodeValue, "(") && containsYear($nodeValue, $year)) {
                        $preferred = $i;
                    }
                }
                $correctTdElem = $temporaryArray[$preferred];
            } elseif (count($temporaryArray) == 1) {
                $correctTdElem = $temporaryArray[0];
            } else {
                echo "Unknown case 1 \n";
                return "";
            }
        } else {
            // exactly 1 result
            $correctTdElem = $resultTdElems[0];
        }
        if ($correctTdElem) {
            //get deep link
            $aElems = $correctTdElem->getElementsByTagName('a');
            foreach ($aElems as $aElem) {
                $linksArray[] = $aElem->getAttribute('href');
            }
        } else {
            echo "Unknown case 2 \n";
        }
    } else {
        //echo "No matching titles found \n";
        return "";
    }

    if (count($linksArray) > 1) {
        echo $searchUrl . "\n";
        print_r($linksArray);
        return "";
    }

    //http://www.imdb.com/title/tt1718835/?ref_=fn_tt_tt_1
    return ($baseUrl . $linksArray[0]);
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

    /*div class="ratings_wrapper">
        <div class="imdbRating" itemtype="http://schema.org/AggregateRating" itemscope="" itemprop="aggregateRating">
                <div class="ratingValue">
<strong title="6,0 based on 2.224 user ratings"><span itemprop="ratingValue">6,0</span></strong>*/
    $ratingElemsArray = getElementsBy($dom, "span", "itemprop", "ratingValue");


    /*array may be empty, if not enough ratings:
    <div class="ratings_wrapper">
        <div class="imdbRating" itemtype="http://schema.org/AggregateRating" itemscope="" itemprop="aggregateRating">
                <div class="notEnoughRatings">Needs 5 Ratings</div>
        </div>*/
    if (empty($ratingElemsArray)) {
        return "0.0";
    } else {
        $ratingValue = $ratingElemsArray[0]->nodeValue;
        return $ratingValue;
    }
}
/*********Execution Flow*************/

$randomNumber = rand();
myLog("Starting queryimdb.php script " . $randomNumber);
$startTime = microtime(true);


// https://www.amazon.de/s/ref=sr_pg_399?fst=as%3Aoff&rh=n%3A3279204031%2Cp_n_ways_to_watch%3A7448695031%2Cn%3A%213010076031%2Cn%3A3015915031&page=399&bbn=3279204031&ie=UTF8

$data = file_get_contents( 'videos.txt' );
$videos = unserialize( $data );
$skippedVideos = array();
$videosWithRatings = array();
//$videos = array("Star Trek Beyond");

$appendFileName = "videosWithRatingsContinousWrite.txt";
unlink($appendFileName);

foreach ($videos as $video) {
    $cleanMovieTitle = $video[1];
    $position = strpos($cleanMovieTitle, " [");
    if ($position !== false) {
        $cleanMovieTitle = substr($cleanMovieTitle, 0, $position);
    }
    $searchUrl = getMovieUrl($cleanMovieTitle, $video[0]);
    if ($searchUrl == "") {
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
        str_replace("&","and",$cleanMovieTitle);
        //try again only if movie title changed after cleaning
        if ((($positionColon !== false) || ($positionMinus !== false)) && (strlen($cleanMovieTitle) !== 0)) {
            echo "Trying $video[1] again with: $cleanMovieTitle \n";
            $searchUrl = getMovieUrl($cleanMovieTitle, $video[0]);
        }
    }
    if ($searchUrl !== "") {
        //echo $searchUrl . "\n";
        $theRating = getMovieRating($searchUrl);
        if ($theRating !== "0.0") {
            $videosWithRatings[] = array($theRating, $video[0], $video[1]);
            $string_data = nowFormat() . " , " . $theRating . " , " . $video[0] . " , " . $video[1] . "\n";
            file_put_contents($appendFileName, $string_data, FILE_APPEND);
        } else {
            $skippedVideos[] = $video;
        }
    } else {
        $skippedVideos[] = $video;
    }
}
rsort($videosWithRatings);
print_r($videosWithRatings);

//write to text file
$videosWithRatingsName = "videosWithRatings.txt";
$skippedVideosName = "skippedVideos.txt";
unlink($videosWithRatingsName);
unlink($skippedVideosName);
$string_data = serialize($videosWithRatings);
file_put_contents($videosWithRatingsName, $string_data);
$string_data = serialize($skippedVideos);
file_put_contents($skippedVideosName, $string_data);

//print total execution time
myLog ("queryimdb.php Execution time: " . $executionTime = (microtime(true) - $startTime) / 60);
echo "queryimdb.php Execution time: " . $executionTime = (microtime(true) - $startTime) / 60 . "\n";

myLog("queryimdb.php finished. " . $randomNumber);
echo "status 200";
?>