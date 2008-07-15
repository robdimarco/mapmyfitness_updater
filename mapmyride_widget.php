<?php
/*
Plugin Name: MapMyRide Widget
Plugin URI: http://416software.com/projects/mapmyride_widget
Description: A widget for displaying your MayMyRide workouts on your blog.
Author: Rob Di Marco
Version: 1.0.0
Author URI: http://www.416software.com
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
Plugin URI: http://www.innovationontherun.com/plugin/mapmyride
Description: MapMyRideWidget
Author: Rob Di Marco
Version: 1
Author URI: http://www.innovationontherun.com/
*/
require_once ABSPATH.WPINC.'/rss.php';
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
			$desc = str_replace(array("\n", "\r"), ' ', $item['description']);

			echo "<li><a class='rsswidget' href='$link' title='$title'>$title</a>{$desc}</li>";
		}
		echo '</ul>';
	} else {
		echo '<ul><li>' . __( 'An error has occurred; the feed is probably down. Try again later.' ) . '</li></ul>';
	}
}
function widget_mapmyride($args=array() ) {
    $options = get_option("widget_mapmyride");
    $url = $options['url'];
	while ( strstr($url, 'http') != $url )
		$url = substr($url, 1);
	if ( empty($url) )
		return;

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
	if ( file_exists(dirname(__FILE__) . '/rss.png') )
		$icon = str_replace(ABSPATH, get_option('siteurl').'/', dirname(__FILE__)) . '/rss.png';
	else
		$icon = get_option('siteurl').'/wp-includes/images/rss.png';
	$title = "<a class='rsswidget' href='$url' title='" . attribute_escape(__('Syndicate this content')) ."'><img style='background:orange;color:white;border:none;' width='14' height='14' src='$icon' alt='RSS' /></a> <a class='rsswidget' href='$link' title='$desc'>$title</a>";

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
		$options = array('url' => '', 'items' => '10'); 
  }      
  
  if ($_POST['mapmyride-submit']) 
  {
    $options['url'] = htmlspecialchars($_POST['mapmyride-url']);
    $options['items'] = htmlspecialchars($_POST['mapmyride-items']);
    update_option("widget_mapmyride", $options);
  }
  
?>
  <p>
    <label style="font-weight:bold" for="mapmyride-url">Map My Ride RSS Feed URL: </label>
    <input type="text" id="mapmyride-url" name="mapmyride-url" value="<?php echo $options['url'];?>" />
    <input type="hidden" id="mapmyride-submit" name="mapmyride-submit" value="1" />
    <p style="font-size:90%">This URL can be found by clicking on the <a href="http://www.mapmyride.com/training_data">Data Center</a> at MapMyRide.com and looking for the link <span style="font-weight:bold">RSS Feed for your Workouts</span></p>
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
