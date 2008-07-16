<?php
/*
    Copyright (C) 2008, 416 Software Inc.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along
    with this program; if not, write to the Free Software Foundation, Inc.,
    51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.


    MapMyRun Widget, Copyright (c) 2008 Author: Rob Di Marco
    MapMyRun Widget comes with ABSOLUTELY NO WARRANTY;
    This is free software, and you are welcome to redistribute it
    under certain conditions.
    
    Contact at http://www.innovationontherun.com

Plugin Name: MapMyRide Widget
Plugin URI: http://www.innovationontherun.com/mapmyride-wordpress-plugin-released
Description: MapMyRideWidget
Author: Rob Di Marco
Version: 1.0.2
Author URI: http://www.innovationontherun.com/
*/
if (!defined('RUNNER_ICON_URL'))
    define('RUNNER_ICON_URL', "http://static.mapmyfitness.com/v3/images/icon_running.gif");
if (!defined('SWIMMER_ICON_URL'))
    define('SWIMMER_ICON_URL', "http://static.mapmyfitness.com/v3/images/icon_swimming.gif");
if (!defined('GYM_ICON_URL'))
    define('GYM_ICON_URL', "http://static.mapmyfitness.com/v3/images/icon_gym.gif");
if (!defined('CYCLING_ICON_URL'))
    define('CYCLING_ICON_URL', "http://static.mapmyfitness.com/v3/images/icon_cycling.gif");
   
function determineWorkoutType($title) {
    $vals = array("CYCLING" => array("cycl","bike", "bicycl"),
    "RUNNING" => array("run","walk"),
    "SWIMMING" => array("swim"),
    "GYM" => array("lift", "gym"));
    foreach($vals as $key => $exps) {
        foreach ($exps as $re) {
            if (preg_match("/$re/", strtolower($title))) {
                return $key;
            }
        }
    }
    return "";
} 
function mapmyride($rss, $args = array()) 
{
	if ( is_string( $rss ) ) {
		require_once(ABSPATH . WPINC . '/rss.php');
		if ( !$rss = fetch_rss($rss) )
			return;
	} elseif ( is_array($rss) && isset($rss['url']) ) {
		require_once(ABSPATH . WPINC . '/rss.php');
		$args = $rss;
		if ( !$rss = fetch_rss($rss['url']) )
			return;
	} elseif ( !is_object($rss) ) {
		return;
	}

	extract( $args, EXTR_SKIP );

	$items = (int) $items;
	if ( $items < 1 || 20 < $items )
		$items = 10;

	if ( is_array( $rss->items ) && !empty( $rss->items ) ) {
		$rss->items = array_slice($rss->items, 0, $items);
		echo '<ul>';
		foreach ($rss->items as $item ) {
			while ( strstr($item['link'], 'http') != $item['link'] )
				$item['link'] = substr($item['link'], 1);
			$link = clean_url(strip_tags($item['link']));
			$title = attribute_escape(strip_tags($item['title']));
			if ( empty($title) )
				$title = __('Untitled');
			$workoutData = preg_split("/<br *\/>/",str_replace(array("\n", "\r"), ' ', $item['description']));
			$liStyle = "";
            $workoutType = determineWorkoutType($title);
			if ($workoutType != "") {
			    $img = "";
			    if ($workoutType == "CYCLING") {
			        $img = CYCLING_ICON_URL;
			    } elseif ($workoutType == "RUNNING") {
			        $img = RUNNER_ICON_URL;
			    } elseif ($workoutType == "SWIMMING") {
			        $img = SWIMMER_ICON_URL;
			    } elseif ($workoutType == "GYM") {
			        $img = GYM_ICON_URL;
			    }
			    $liStyle .= "background: transparent url($img) no-repeat";
			}
			
			echo "<li style='$liStyle'><a class='$linkClass' href='$link' title='$title'>$title</a>";
			foreach ($workoutData as $workout) {
			    $vals = preg_split("/<\/?b *>/", $workout);
			    echo "<p><b>".attribute_escape(strip_tags(html_entity_decode($vals[1], ENT_QUOTES)))."</b> ".attribute_escape(strip_tags(html_entity_decode($vals[2], ENT_QUOTES)))."</p>";
			}
			echo "</li>";
		}
		echo '</ul><p><a href="http://www.innovationontherun.com/mapmyride-wordpress-plugin-released">Get This Widget!</a></p>';
	} else {
		echo '<ul><li>' . __( 'An error has occurred; the feed is probably down. Try again later.' ) . '</li></ul>';
	}
}
function widget_mapmyride($args, $widget_args = 1) {
	extract($args, EXTR_SKIP);
	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract($widget_args, EXTR_SKIP);
    $options = get_option("widget_mapmyride");
    $url = $options['url'];
	while ( strstr($url, 'http') != $url )
		$url = substr($url, 1);
	if ( empty($url) )
		return;
    
	require_once(ABSPATH . WPINC . '/rss.php');
    $rss = fetch_rss($url);
    $link = clean_url(strip_tags($rss->channel['link']));
	while ( strstr($link, 'http') != $link )
		$link = substr($link, 1);
	$desc = attribute_escape(strip_tags(html_entity_decode($rss->channel['description'], ENT_QUOTES)));
	$title = $options[$number]['title'];
	if ( empty($title) )
		$title = htmlentities(strip_tags($rss->channel['title']));
	if ( empty($title) )
		$title = $desc;
	if ( empty($title) )
		$title = __('Unknown Feed');
	$url = clean_url(strip_tags($url));
	$title = "<a class='rsswidget' href='$link'>Recent Workouts from MapMyRide.com</a>";

	echo $before_widget;
	echo $before_title . $title . $after_title;
    mapmyride($rss, $options);
	echo $after_widget;
}

function widget_mapmyride_control() 
{
  $options = get_option("widget_mapmyride");
  
  if (!is_array( $options ))
  {
		$options = array('url' => '', 'items' => '10', 
		'show_date' =>1, 
		'show_reps' =>1, 
		'show_cals' =>1,
		'show_weight' =>1,
		'show_distance' =>1,
		'show_duration' => 1); 
  }      
  
  if ($_POST['mapmyride-submit']) 
  {
    $url = $_POST['mapmyride-url'];
    preg_match("/u=\d+/", $url, $matches);
    $options['url'] = "http://www.mapmyride.com/rss/rss_workouts?" . $matches[0];
    $options['items'] = htmlspecialchars($_POST['mapmyride-items']);
    update_option("widget_mapmyride", $options);
  }
  
?>
  <p>
    <label style="font-weight:bold" for="mapmyride-url">Map My Ride RSS Feed URL: </label>
    <input type="text" id="mapmyride-url" name="mapmyride-url" value="<?php echo $options['url'];?>" />
    <input type="hidden" id="mapmyride-submit" name="mapmyride-submit" value="1" />
    <p style="font-size:90%">This URL can be found by clicking on the <a href="http://www.mapmyride.com/training_data">Data Center</a> at <a href="http://www.mapmyride.com">MapMyRide.com</a> and looking for the link <span style="font-weight:bold">RSS Feed for your Workouts</span></p>
  </p>
  	<p>
		<label for="mapmyride-items"><?php _e('How many workouts would you like to display?'); ?>
			<select id="mapmyride-items" name="mapmyride-items">
				<?php
					for ( $i = 1; $i <= 20; ++$i )
						echo "<option value='$i' " . ( $options['items'] == $i ? "selected='selected'" : '' ) . ">$i</option>";
				?>
			</select>
		</label>
	</p>

<?php

}

function mapmyride_init()
{

    if(function_exists('wp_register_sidebar_widget') && function_exists('wp_register_widget_control')) {
    	wp_register_sidebar_widget('mapmyride', 'Map My Ride Workouts', 'widget_mapmyride', array('classname' => 'widget_mapmyride', 'description' => 'Share your MapMyRide workouts on your blog.'));
    	wp_register_widget_control('mapmyride', 'Map My Ride Workouts', 'widget_mapmyride_control', array('width' => 400, 'height' => 300));
    } else {
	    register_sidebar_widget(__('Map My Ride Workouts'), 'widget_mapmyride');     
        register_widget_control(   'Map My Ride Workouts', 'widget_mapmyride_control', 400, 300 );     
    }
}
add_action("plugins_loaded", "mapmyride_init");
?>
