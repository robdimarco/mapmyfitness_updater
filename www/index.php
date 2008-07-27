<?php
  require_once dirname(__FILE__) . "/friendfeed/friendfeed.php";
  require_once dirname(__FILE__) . "/../classes/mapmyride.php";
  $mmr = new MapMyRide(1746161);
  $mmr->fetch_data();
  $ff = new FriendFeed("robdimarco", "lucid129horns");
  $added = false;
  if ('true' == $_GET['add']) {
      $id = $_GET['workoutId'];
      $workout = null;
      foreach ($mmr->workouts as $wo) {
          if ($wo->workoutId == $id) {
              $workout = $wo;
          }
      }
      if (null != $workout) {
          $ff->publish_link($workout->title, $workout->link);
      }
      $added = true;
  }
  $feed = $ff->fetch_user_feed("robdimarco");
?>
<html>
  <head>FriendFeed/MayMyFitness Bridge</head>
  <body>
      <? if ($added) { ?>Added<?}?>
<?    if ($feed) {?>
      <ul>
     <? foreach ($feed->entries as $entry) { ?>
         <li>
             <a href="<?=$entry->link?>"><?= $entry->title?></a>
              by <a href="<?=$entry->user->profileUrl?>"><?= $entry->user->nickname ?></a>
         </li>
     <? }?>
     </ul> 
     <?}?>
<? if ($mmr->workouts) {?>
     <ul>
<?     foreach ($mmr->workouts as $workout) { ?>
<li><a href="<?=$workout->link?>"><?=$workout->title?></a> (<a href="index.php?add=true&workoutId=<?=$workout->workoutId?>">Add</a>)</li>
    <?     } ?>
     </ul>
<?
  } 
    ?>
  </body>
</html>