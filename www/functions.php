<?php
function addWorkoutLinkToFriendFeed($ff, $mmr, $id) {
      $workout = null;
      foreach ($mmr->workouts as $wo) {
          if ($wo->workoutId == $id) {
              $workout = $wo;
          }
      }
      if (null != $workout) {
          $ff->publish_link($workout->title, $workout->link);
      }

}
?>