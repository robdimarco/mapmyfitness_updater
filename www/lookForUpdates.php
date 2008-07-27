<?php
  require_once dirname(__FILE__) . "/friendfeed/friendfeed.php";
  require_once dirname(__FILE__) . "/../classes/mapmyride.php";
  require_once dirname(__FILE__) . "/functions.php";
?>
<html>
  <head><title>Updating Users</title></head>
  <body>
      Updating users:
      <ul>
<?php
  mysql_connect("localhost", "root", "admin");
  mysql_select_db("mmr");
  $users = mysql_query("select id,
    mmr_user_id, 
    friend_feed_user_name, 
    friend_feed_auth_key, 
    last_workout_id, 
    last_update_date from mmr_ff_user where is_active = 1");
    while ($row = mysql_fetch_assoc($users)) {
        $workoutCount = 0;
      $mmr = new MapMyRide($row['mmr_user_id']);
      $ff = new FriendFeed($row['friend_feed_user_name'], $row['friend_feed_auth_key']);
      
      if ($row['last_update_date']) {
          $searchFromTime = strtotime($row['last_update_date'])- 24*60*60;
      } else {
          $searchFromTime = time() - 24*60*60*2;
      }
      $lastWorkoutId = $row['last_workout_id'];
      foreach (array_reverse($mmr->findWorkoutsSinceTime($searchFromTime)) as $workout) {
          if ((int)$workout->workoutId > (int)$row['last_workout_id']) {
              addWorkoutLinkToFriendFeed($ff, $mmr, $workout->workoutId);
              $workoutCount ++;
              $lastWorkoutId = max($lastWorkoutId, $workout->workoutId);
          }
      }
      mysql_query("update mmr_ff_user set last_update_date = now(), last_workout_id = {$lastWorkoutId} where id={$row['id']}");
        ?>
        <li>Updating user <?= $row['mmr_user_id']?> with <?= $workoutCount?> workouts.</li>

        <?php
    }

    mysql_free_result($users);
?>