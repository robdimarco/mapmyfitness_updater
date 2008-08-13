<?php
  require_once dirname(__FILE__) . "/config.php";
  require_once dirname(__FILE__) . "/friendfeed/friendfeed.php";
  require_once dirname(__FILE__) . "/twitter/class.twitter.php";
  require_once dirname(__FILE__) . "/classes/mapmyride.php";
  require_once dirname(__FILE__) . "/functions.php";
?>
<html>
  <head><title>Updating Users</title></head>
  <body>
      Updating users:
      <ul>
<?php
  mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
  mysql_select_db(DB_DATABASE);
  $users = mysql_query("select id,
    mmr_user_id, 
    app_user_name, 
    app_auth_key,
    notify_type,
    last_workout_id, 
    last_update_date from mmr_notify_user where is_active = 1");
    while ($row = mysql_fetch_assoc($users)) {
      $workoutCount = 0;
      $mmr = new MapMyRide($row['mmr_user_id']);
      
      if ($row['last_update_date']) {
          $searchFromTime = strtotime($row['last_update_date'])- 24*60*60;
      } else {
          $searchFromTime = time() - 24*60*60*2;
      }
      $lastWorkoutId = (int)$row['last_workout_id'];
      foreach (array_reverse($mmr->findWorkoutsSinceTime($searchFromTime)) as $workout) {
          if ((int)$workout->workoutId > (int)$row['last_workout_id']) {
          	if ($row['notify_type'] == 'friendfeed') {
          	  $ff = new FriendFeed($row['app_user_name'], $row['app_auth_key']);
              addWorkoutLinkToFriendFeed($ff, $mmr, $workout->workoutId);
          	} else if ($row['notify_type'] == 'twitter') {
          	  $twitter = new twitter();
          	  $twitter->username=$row['app_user_name'];
          	  $twitter->password=$row['app_auth_key'];
          	  $twitter->user_agent=$row['MMF Updater - Issues: rob@innovationontherun.com'];
          	  addWorkoutLinkToTwitter($twitter, $mmr, $workout->workoutId); 
          	}
            $workoutCount ++;
            $lastWorkoutId = max($lastWorkoutId, (int)$workout->workoutId);
          }
      }
      mysql_query("update mmr_notify_user set last_update_date = now(), last_workout_id = {$lastWorkoutId} where id={$row['id']}");
        ?>
        <li>Updating user <?= $row['mmr_user_id']?> with <?= $workoutCount?> workouts.</li>

        <?php
    }

    mysql_free_result($users);
?>
