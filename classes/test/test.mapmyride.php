<?php
  define('MAGPIE_DEBUG', 1);
  require_once dirname(__FILE__)."/../mapmyride.php";
  $mmr = new MapMyRide(1746161);
  $mmr->fetch_data();
  print_r($mmr->workouts);
?>