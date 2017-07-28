<?php
  $now = new DateTime(null, new DateTimeZone('Europe/Berlin'));
  $name = "cron " . $now->format('Y-m-d--H-i-s') . ".txt";
  $file = fopen($name, "w");
  fclose($file);
?>