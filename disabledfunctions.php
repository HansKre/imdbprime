<?php
   echo ini_get("disable_functions"), '<br>';
  // is cURL installed yet?
  if (!function_exists('curl_init')){
        die('Sorry cURL is not installed!');
  } else {echo "Curl is available";}
?>