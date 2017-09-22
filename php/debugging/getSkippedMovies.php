<?php
require_once ('../Legacy/FileOperations.php');

    $movies = unserialize(file_get_contents( FileNames::imdbQuerySkippedMoviesName() ));

    usort($movies, function($a, $b) {
        //sort descending by ratingValue
        return strcasecmp($a['movie'],$b['movie']);
    });


    // return HTML
    echo "<table><h1>Skipped Movies</h1>";
    echo "Skipped: " . count($movies) . "<br>";
    // table header
    echo    "<tr>";
    echo            "<th>Movie</th>";
    echo            "<th>Director</th>";
    echo            "<th>Actors</th>";
    echo            "<th>Year</th>";
    echo    "</tr>";

    // insert displayedMovies as rows into the table
    foreach ($movies as $movie) {
        $director = null;
        if (is_array($movie['director'])) {
            foreach ($movie['director'] as $dir) {
                $director = $director . " " . $dir;
            }
        } else {
            $director = $movie['director'];
        }

        $actors = null;
        if (is_array($movie['actors'])) {
            foreach ($movie['actors'] as $actor) {
                $actors = $actors . " " . $actor;
            }
        } else {
            $actors = $movie['actors'];
        }

        $movieTitle = $movie['movie'];
        $year = $movie['year'];

        echo    "<tr>";
        echo            "<td>$movieTitle</td>";
        echo            "<td>$director</td>";
        echo            "<td>$actors</td>";
        echo            "<td>$year</td>";
        echo    "</tr>";
    }
    echo "</table>";