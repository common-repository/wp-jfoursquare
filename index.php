<?php
/*
Plugin Name: WP-jFoursquare
Plugin URI: http://www.sideways8.com/plugins/wp-jfoursquare
Description: A widget that uses jQuery and Foursquare to display someone's checkins in a sidebar.
Version: 0.8.1
Author: Aaron Reimann
Author URI: http://sideways8.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Copyright 2012  Aaron Reimann  (email : aaron.reimann@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

include_once('class_widget.php');

/* inits */
function jfoursquare_init()
{
	if (!is_admin()) {
		wp_enqueue_script('jfoursquare', WP_PLUGIN_URL . '/wp-jfoursquare/js/jfoursquare.js', array('jquery'));
	}
}
add_action('init', 'jfoursquare_init');

function jfoursquare_widget_init()
{
	register_widget("jFoursquareWidget");
}
add_action('widgets_init', 'jfoursquare_widget_init');


/* Adding style sheet for front end */
function jfoursquare_add_css()
{
	if (!is_admin())
	{
		//$stylesheet_url  = plugins_url( 'style.css', __FILE__ );
		wp_enqueue_style( 's8_jfoursquare_stylesheets', plugins_url( '/style.css', __FILE__ ) );
	}
}
add_action('wp_print_styles', 'jfoursquare_add_css');


/* time formatting - twitter style */
function jfoursquare_formatter($date)
{
	$time = strftime("%s", strtotime($date));
	$foursquare_time = human_time_diff($time, current_time('timestamp') ) . ' ago';
	return $foursquare_time;
}

/* scrub the data and remove the paranoia items */
function jfoursquare_paranoid (
		$foursquare_history_feed,
		$foursquare_number, 
		$time_delay
		)
{
	$paranoid_array = array();

	include_once(ABSPATH . WPINC . '/rss.php');

	$feed = fetch_rss($foursquare_history_feed);

	if ( $feed )
	{ // if there is an XML feed to pull, if user doesn't exist, no feed

		$items = array_slice($feed->items, 0, $foursquare_number);

		foreach ( $items as $item ) :

			$post_time = strtotime($item['pubdate']);
			$cur_time = time();
			$fut_time = $post_time + (60 * 60 * $time_delay);

			if ( $cur_time > $fut_time )
			{
				$title = $item['title'];
				$description = $item['description'];
				$link = $item['link'];
				$date = jfoursquare_formatter($item['pubdate']);

				$array_item = array(
					$title,
					$description,
					$link,
					$date
					);

				$paranoid_array[] = $array_item;
			}
		endforeach;

		return $paranoid_array;
	}
}

/* actual functions that spits out the code */
function jfoursquare_echo (
		$paranoid_array,
		$foursquare_number, 
		$time_pause, 
		$pixel_height, 
		$widget_number,
		$no_fade,
		$time_delay
		)
{
	// if there is an array, note: the paranoid array might not be paranoid if this is not a time delay
	if ( $paranoid_array )
	{ 
		$items = $paranoid_array;

		if ($no_fade) { $nofade = "-no-fade"; }

		echo '<div id="widgetnumber-'.$widget_number.'" class="jfoursquare-feed'.$nofade.' jfoursquare-feed-'.$widget_number.'"';
			if (!empty($pixel_height)) { echo ' style="height: '.$pixel_height.'px;"'; }
			echo ' data-rotatetime="'.$time_pause.'"';
		echo '>';

		$count = 0;
		foreach ( $items as $item ) :
			
			if ($count < $foursquare_number)
			{

				$title = $item[0];
				$desc = $item[1];
				$link = $item[2];
				$date = $item[3];

				echo '<li class="jfoursquare-item jfoursquare-item-'.$widget_number.'">';
					echo $title;
					echo ' <span>'.$date.'</span>';
				echo '</li>'."\r\n";

			}

			$count = $count + 1;

		endforeach;

		echo '</div>'."\r\n";
	}
	else
	{
		echo '<div id="jfoursquare-feed" data-rotatetime="2000"><li>No Foursquare Feed to Display</li><li>Check Your Settings</li></div>';
	}
}
