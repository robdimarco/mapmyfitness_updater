<?php 
require_once dirname(__FILE__)."/../workout.php";
$wo = new Workout();
$wo->setDetailArrayFromString("<b>Date:</b> 2008-07-26<br/>
<b>Distance:</b> 8.40 mi.<br/>
<b>Repetitions:</b> 1<br/>
<b>Weight:</b> 165.0 lbs.<br/>");
assert(count($wo->detailArray) == 4);
#print_r($wo->detailArray);

$wo->parseLinkForId("http://www.mapmyrun.com/user_training?username=rob_dimarco&txtWorkoutDay=25&txtWorkoutMonth=07&txtWorkoutYear=2008&txtWorkoutID=3715617");
assert($wo->workoutId == '3715617');
#print($wo->workoutId."\n");
?>