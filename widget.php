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
*/<?php
/*
Plugin Name: MapMyRide Widget
Plugin URI: http://www.innovationontherun.com/plugin/mapmyride
Description: MapMyRideWidget
Author: Rob Di Marco
Version: 1
Author URI: http://www.innovationontherun.com/
*/
require_once ABSPATH.WPINC.'/rss.php';
function mapMyRide($rss, $args = array()) 
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
	$show_summary  = (int) $show_summary;
	$show_author   = (int) $show_author;
	$show_date     = (int) $show_date;

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
			$desc = '';
				if ( isset( $item['description'] ) && is_string( $item['description'] ) )
					$desc = str_replace(array("\n", "\r"), ' ', attribute_escape(strip_tags(html_entity_decode($item['description'], ENT_QUOTES))));
				elseif ( isset( $item['summary'] ) && is_string( $item['summary'] ) )
					$desc = str_replace(array("\n", "\r"), ' ', attribute_escape(strip_tags(html_entity_decode($item['summary'], ENT_QUOTES))));

			$summary = '';
			if ( isset( $item['description'] ) && is_string( $item['description'] ) )
				$summary = $item['description'];
			elseif ( isset( $item['summary'] ) && is_string( $item['summary'] ) )
				$summary = $item['summary'];

			$desc = str_replace(array("\n", "\r"), ' ', attribute_escape(strip_tags(html_entity_decode($summary, ENT_QUOTES))));

			if ( $show_summary ) {
				$desc = '';
				$summary = wp_specialchars( $summary );
				$summary = "<div class='rssSummary'>$summary</div>";
			} else {
				$summary = '';
			}

			$date = '';
			if ( $show_date ) {
				if ( isset($item['pubdate']) )
					$date = $item['pubdate'];
				elseif ( isset($item['published']) )
					$date = $item['published'];

				if ( $date ) {
					if ( $date_stamp = strtotime( $date ) )
						$date = '<span class="rss-date">' . date_i18n( get_option( 'date_format' ), $date_stamp ) . '</span>';
					else
						$date = '';
				}
			}

			$author = '';
			if ( $show_author ) {
				if ( isset($item['dc']['creator']) )
					$author = ' <cite>' . wp_specialchars( strip_tags( $item['dc']['creator'] ) ) . '</cite>';
				elseif ( isset($item['author_name']) )
					$author = ' <cite>' . wp_specialchars( strip_tags( $item['author_name'] ) ) . '</cite>';
			}

			echo "<li><a class='rsswidget' href='$link' title='$desc'>$title</a>{$date}{$summary}{$author}</li>";
		}
		echo '</ul>';
	} else {
		echo '<ul><li>' . __( 'An error has occurred; the feed is probably down. Try again later.' ) . '</li></ul>';
	}
}
function widget_mapMyRide($args) {
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
    mapMyRide($rss);
	echo $after_widget;
}

function mapMyRide_control() 
{
  $options = get_option("widget_mapMyRide");
  
  if (!is_array( $options ))
  {
		$options = array('url' => ''); 
  }      
  
  if ($_POST['mapMyRide-Submit']) 
  {
    $options['url'] = htmlspecialchars($_POST['mapMyRide-URL']);
    update_option("widget_mapMyRide", $options);
  }
  
?>
  <p>
    <label for="mapMyRide-URL">Map My Ride RSS URL: </label>
    <input type="text" id="mapMyRide-URL" name="mapMyRide-URL" value="<?php echo $options['url'];?>" />
    <input type="hidden" id="mapMyRide-Submit" name="mapMyRide-Submit" value="1" />
  </p>
<?php

}

function mapMyRide_init()
{
  register_sidebar_widget(__('Map My Ride'), 'widget_mapMyRide');     
  register_widget_control(   'Map My Ride', 'mapMyRide_control', 300, 200 );     
}
add_action("plugins_loaded", "mapMyRide_init");
?>
