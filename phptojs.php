<?php
    $data = file_get_contents( 'videosWithRatings.txt' );
    $videos = unserialize( $data );
    echo json_encode($videos);
?>