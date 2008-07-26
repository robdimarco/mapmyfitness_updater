<?php

if (!defined('MAGPIE_DIR')) {
    define('MAGPIE_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . "magpierss". DIRECTORY_SEPARATOR);
}
if (!defined('MAP_MY_RIDE_RSS_URL')) {
    define('MAP_MY_RIDE_RSS_URL','http://www.mapmyride.com/rss/rss_workouts?u=');
}

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "workout.php");

class MapMyRide {
    var $userId;
    var $workouts;
    function MapMyRide($userId){
        $this->userId = $userId;
        $this->workouts = array();
    }
    function fetch_data() {
        if (!function_exists('fetch_rss')) {
            require_once (MAGPIE_DIR ."rss_fetch.inc");
        }
        print "Getting ".MAP_MY_RIDE_RSS_URL . $this->userId."\n";
        $rss = fetch_rss(MAP_MY_RIDE_RSS_URL . $this->userId);
	    if ( is_array( $rss->items ) && !empty( $rss->items ) ) {
		    foreach ($rss->items as $item ) {
		        $this->workouts[] = new Workout($item);
		    }
	    } else {
	        throw new Exception('Could not find any RSS items for user Id :'. $this->userId);
	    }
    }
}
?>