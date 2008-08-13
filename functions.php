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
function addWorkoutLinkToTwitter($twitter, $mmr, $id, $intro ="Just finished a workout: ") {
      $workout = null;
      foreach ($mmr->workouts as $wo) {
          if ($wo->workoutId == $id) {
              $workout = $wo;
          }
      }
      if (null != $workout) {
          $twitter->status($intro . $workout->title . " " . getShortenedUrl($workout->link));
      }
}

function getShortenedUrl($urlToConvert) {
	$url="http://bit.ly/api?url=" . $urlToConvert;
    $fileopen=file($url);
    $count=count($fileopen);
	for($i=0;$i<$count;$i++){
		$filegets.=$fileopen[$i];
	}
	return $filegets;
}
?>
