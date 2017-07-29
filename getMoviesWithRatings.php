<?php
    $data = file_get_contents( './output/moviesWithRatings.txt' );
    $videos = unserialize( $data );
    echo json_encode($videos);
?>