<?php
require_once('FileOperations.php');

/*
 *
 * This file is used only to find movies for debug purposes
 *
 * */

    $videos = unserialize(file_get_contents( FileNames::primeOutputMovies() ));

    $videosOfInterest = array("Twilight - Biss zum Morgengrauen [dt./OV]", "Escape Plan - Entkommen oder Sterben [dt./OV]", "Ich - Einfach unverbesserlich 2 [dt./OV]", "Teenage Mutant Ninja Turtles: Out Of The Shadows [dt./OV]", "Ich - Einfach unverbesserlich [dt./OV]", "Everest [dt./OV]", "Stolz und Vorurteil [dt./OV]", "Ong Bak 2", "No Country For Old Men", "Blair Witch Project", "Ong Bak 3", "Berlin Calling", "Ab durch die Hecke [dt./OV]", "The Expendables - Director's Cut", "Ziemlich Beste Freunde [dt./OV]", "Ip Man", "Schulmaedchen-Report 3. Teil - Was Eltern Nicht Mal Ahnen", "Riddick - Chroniken eines Kriegers", "Crank", "Saw IV", "Der Pate II [dt./OV]", "Schulmaedchen-Report 1. Teil", "Madagascar [OV]");

    $filteredMovies = array();
    foreach ($videos as $video) {
        foreach ($videosOfInterest as $videoOfInterest) {
            $movieTitle = $video['movie'];
            if (strcmp($movieTitle, $videoOfInterest) == 0) {
                $filteredMovies[] = $video;
            }
        }
    }

    $outFileName = './output/filteredMovies.txt';
    if (!file_exists($outFileName)) {
        $file = fopen($outFileName, "w");
        fclose($file);
    }
    file_put_contents($outFileName, serialize($filteredMovies));