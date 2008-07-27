<?php
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "workout.php");

if (!defined('MAP_MY_RIDE_RSS_URL')) {
    define('MAP_MY_RIDE_RSS_URL','http://www.mapmyride.com/rss/rss_workouts?u=');
}

class MapMyRide {
    var $userId;
    var $workouts;
    var $fetched;
    function MapMyRide($userId){
        $this->userId = $userId;
        $this->workouts = array();
        $this->fetched = false;
    }
    function fetch_data() {
        if ($this->fetched) {
            return;
        }
        if (!function_exists('fetch_rss')) {
            if (!defined('MAGPIE_DIR')) {
                define('MAGPIE_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . "magpierss". DIRECTORY_SEPARATOR);
            }
            require_once (MAGPIE_DIR ."rss_fetch.inc");
        }
        $rss = fetch_rss(MAP_MY_RIDE_RSS_URL . $this->userId);
	    if ( is_array( $rss->items ) && !empty( $rss->items ) ) {
		    foreach ($rss->items as $item ) {
		        $this->workouts[] = new Workout($item);
		    }
		    usort($this->workouts, array("Workout", "cmp_obj"));
	    }
	    $this->fetched = true;
    }
    function findWorkoutsSinceTime($time) {
        $this->fetch_data();
        $workouts = array();
        foreach ($this->workouts as $wo) {
            $d = strtotime($wo->getEntryDate());
            if ($d > $time) {
                $workouts[] = $wo;
            }
        }
        return $workouts;
    }
}
?>