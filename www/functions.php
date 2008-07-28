<?php
function addWorkoutLinkToFriendFeed($ff, $mmr, $id, $intro ="Just finished a workout: ") {
      $workout = null;
      foreach ($mmr->workouts as $wo) {
          if ($wo->workoutId == $id) {
              $workout = $wo;
          }
      }
      if (null != $workout) {
          $ff->publish_link($intro . $workout->title, $workout->link);
      }

}
?>
