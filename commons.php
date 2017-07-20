<?php
    const ONESECOND = 1000000;
    function nowFormat () {
        $now = new DateTime("", new DateTimeZone('Europe/Berlin'));
        return $now->format('Y-m-d H:i:s');
    }

    function myLog($message) {
        $logFileName = "global.log";
        file_put_contents( $logFileName, nowformat() . " " . $message . "\n", FILE_APPEND | LOCK_EX);
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
?>