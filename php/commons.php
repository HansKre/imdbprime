<?php
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
        $logFileName = "global.log";
        $logMessageString =  nowAsString() . " " . $message . "\n";
        file_put_contents( $logFileName, $logMessageString, FILE_APPEND | LOCK_EX);
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
        $matchesArray;
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

        //Parse the HTML. The @ is used to suppress any parsing errors
        //that will be thrown if the $html string isn't valid XHTML.
        @$imdbMovieDetailsDom->loadHTML($imdbMovieDetailsHtml);
        return $imdbMovieDetailsDom;
    }