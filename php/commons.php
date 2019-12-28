<?php

/*
* CRON_JOB_MAX_EXECUTION_TIME is very platform dependend.
* On the free version of heroku, a scheduler can be used to trigger jobs.
* The execution time for these kind of jobs is limited to the scheduled frequency.
* Example: if the scheduler is set to run a job every 10 minutes, than a running job will be
* terminated after 10 minutes to allow starting of a new one.
*
* We set the scheduler interval to 10 minutes. Therefore, CRON_JOB_MAX_EXECUTION_TIME is set
* to 10 minutes as well.
*/
const CRON_JOB_MAX_EXECUTION_TIME = 10;

const ONESECOND = 1000000;
function nowAsString () {
    return nowAsStringWithFormat(null);
}

function nowAsStringWithFormat ($format) {
    $now = new DateTime("", new DateTimeZone('Europe/Berlin'));
    if ($format) {
        return $now->format($format);
    } else {
        return $now->format('Y-m-d H:i:s');
    }
}

function myLog($message) {
    $logMessageString =  nowAsString() . " " . $message . "\n";
    //TODO: log to DB
    //$logFileName = "global.log";
    //file_put_contents( $logFileName, $logMessageString, FILE_APPEND | LOCK_EX);
    echo $logMessageString;
}

function getElementsBy($parentNode, $tagName, $attributeName, $attributeValue) {
    $nodes=array();

    $childNodeList = $parentNode->getElementsByTagName($tagName);
    for ($i = 0; $i < $childNodeList->length; $i++) {
        $temp = $childNodeList->item($i);
        if (stripos($temp->getAttribute($attributeName), $attributeValue) !== false) {
            $nodes[]=$temp;
        }
    }
    return $nodes;
}

function getAttributeValueBy($parentNode, $tagName, $attributeName) {
    $nodes=array();

    $childNodeList = $parentNode->getElementsByTagName($tagName);
    for ($i = 0; $i < $childNodeList->length; $i++) {
        $temp = $childNodeList->item($i);
        if ($temp->getAttribute($attributeName) !== "") {
            $nodes[]=$temp->getAttribute($attributeName);
        }
    }
    return $nodes;
}

function getElementsByClass($parentNode, $tagName, $className) {
    return getElementsBy($parentNode, $tagName, "class", $className);
}

function contains ($string, $substring) {
    if (stripos($string, $substring) !== false) {
        return true;
    }
    return false;
}

function multipleOccuranceOf ($string, $substring) {
    if (substr_count($string, $substring) > 1) {
        return true;
    }
    return false;
}

function hasElementByAndSearchString($parentNode, $tagName, $attributeName, $attributeValue, $searchString) {
    $returnArr = getElementsBy($parentNode, $tagName, $attributeName, $attributeValue);
    foreach ($returnArr as $value) {
        $stringValue = $value->nodeValue;
        if (contains($stringValue, $searchString)) {
            return true;
        }
    }
    return false;
}

function matchesYear($string, $year) {
    if (is_numeric($year)) {
        if (contains($string, "(".$year.")") ||
            contains($string, "(". ($year + 1) .")") ||
            contains($string, "(". ($year + 2) .")") ||
            contains($string, "(". ($year - 1) .")") ||
            contains($string, "(". ($year - 2) .")")
        ) {
            return true;
        }
    }
    return false;
}

function containsYear($string) {
    $matchesArray = Array();
    if (preg_match('/\b\d{4}\b/', $string, $matchesArray) == 1) {
        //return the found year
        return $matchesArray[0];
    }
    return false;
}

function loadAndParseHtmlFrom($deepLink) {
    //Load the HTML page
    $imdbMovieDetailsHtml = file_get_contents($deepLink);

    //Create a new DOM document
    $imdbMovieDetailsDom = new DOMDocument;

    //Parse the HTML. The @ is used to suppress any queryAmazonPrime errors
    //that will be thrown if the $html string isn't valid XHTML.
    @$imdbMovieDetailsDom->loadHTML($imdbMovieDetailsHtml);
    return $imdbMovieDetailsDom;
}

/*
 * DOMElement appears to be empty for var_dump() or when debugging it.
 * Workaround:
 */
function debugGetDomElemContent($domElement) {
    return $xml = $domElement->ownerDocument->saveXML($domElement);
}

function getHttpCode($http_response_header) {
    if(is_array($http_response_header))
    {
        $parts=explode(' ',$http_response_header[0]);
        if(count($parts)>1) //HTTP/1.0 <code> <text>
            return intval($parts[1]); //Get code
    }
    return 0;
}