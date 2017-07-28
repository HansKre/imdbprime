<?php

// read from text file
$data = file_get_contents( 'videos.json');
$videos = unserialize( $data );
print_r($videos);
echo "Anzahl gefundener Videos: " . count($videos);

?>