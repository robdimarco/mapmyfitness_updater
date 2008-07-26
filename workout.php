<?php
class Workout {
    var $workoutId;
    var $title;
    var $link;
    var $detailArray;
    function Workout($rssItem = '') {
        $this->detailArray = array();
        if (!'' == $rssItem) {
            $this->setDetailsFromRssItem($rssItem);
        }
    }
    function setDetailsFromRssItem($rssItem){
        $this->title = strip_tags($rssItem['title']);
        $this->link  = $rssItem['link'];
        $this->parseLinkForId();
        $this->setDetailArrayFromString($rssItem['description']);
    }

    function setDetailArrayFromString($str) {
        $workoutDetails = preg_split("/<br *\/>/",str_replace(array("\n", "\r"), ' ', $str));
        foreach ($workoutDetails as $workout) {
			$vals = preg_split("/:?<\/?b *>/", $workout);
			if ($vals[1]) {
			    $this->detailArray[strip_tags(html_entity_decode($vals[1], ENT_QUOTES))] = strip_tags(html_entity_decode($vals[2], ENT_QUOTES));
            }
        }
    }
    function parseLinkForId($link = ''){
        if ($link == '') {
            $link = $this ->link;
        }
        if (preg_match('/txtWorkoutID\=[0-9]+/',$link, $matches)) {
            $this->workoutId = substr($matches[0],strlen("txtWorkoutID="));
        }
    }
    
    /* This is the static comparing function: */
    static function cmp_obj($a, $b)
    {
        $ua = (int)$a->workoutId;
        $ub = (int)$b->workoutId;
        if ($ua == $ub) {
            return 0;
        } elseif ($ua > $ub) {
            return -1;
        } else {
            return 1;
        }
    }
}
?>