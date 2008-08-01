<?php
  require_once dirname(__FILE__) . "/friendfeed/friendfeed.php";
  require_once dirname(__FILE__) . "/classes/mapmyride.php";
  require_once dirname(__FILE__) . "/functions.php";
  $mmr = new MapMyRide(1746161);
  $mmr->fetch_data();
  $ff = new FriendFeed("robdimarco", "lucid129horns");
  if ('true' == $_GET['add']) {
      addWorkoutLinkToFriendFeed($ff, $mmr, $_GET['workoutId']);
      header("Location: index.php");
  }
  $feed = $ff->fetch_user_feed("robdimarco");
?>
<html>
  <head>FriendFeed/MayMyFitness Bridge</head>
  <body>
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
<?     foreach ($mmr->findWorkoutsSinceTime(time() - 24*60*60) as $workout) { ?>
<li><a href="<?=$workout->link?>"><?=$workout->title?></a> on <?= $workout->detailArray['Date']?> (<a href="index.php?add=true&workoutId=<?=$workout->workoutId?>">Add</a>)</li>
    <?     } ?>
     </ul>
     <ul>
<?     foreach ($mmr->workouts as $workout) { ?>
<li><a href="<?=$workout->link?>"><?=$workout->title?></a> on <?= $workout->detailArray['Date']?> (<a href="index.php?add=true&workoutId=<?=$workout->workoutId?>">Add</a>)</li>
    <?     } ?>
     </ul>
<?
  } 
    ?>
  </body>
</html>
