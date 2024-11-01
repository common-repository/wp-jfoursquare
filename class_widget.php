<?php
class jFoursquareWidget extends WP_Widget
{
	
	function jFoursquareWidget()
	{
		$widget_options = array(
			'classname'		=>	'jfoursquare-widget',
			'description'	=>	'A widget to show a fade-in/fade-out effect on Foursquare checkins using jQuery'
		);

		parent::WP_Widget(false, 'jFoursqure', $widget_options);
	}
	
	function widget($args, $instance)
	{
		extract($args, EXTR_SKIP );
		$title = ($instance['title']) ? $instance['title'] : 'jFoursquare Widget';
		$foursquare_history_feed = ($instance['foursquare_history_feed']) ? $instance['foursquare_history_feed'] : 'No RSS Feed Entered';
		$foursquare_number = ($instance['foursquare_number']) ? $instance['foursquare_number'] : '3';
		$time_pause = $instance['time_pause']; // default is coming from jfoursquare.js
		$pixel_height = ($instance['pixel_height']) ? $instance['pixel_height'] : '0';
		$no_fade = ($instance['no_fade']) ? $instance['no_fade'] : '0';
		$time_delay = ($instance['time_delay']) ? $instance['time_delay'] : '0';

		echo $before_title . $title . $after_title;

		if ($foursquare_history_feed != 'No Foursquare Feed')
		{
			// Foursquare gives you the most recent 25, this will take the "paranoid" time and scrub
			// it and return an array of only the ones that are within the non-paranoid params
			$paranoid_array = jfoursquare_paranoid(
				$foursquare_history_feed,
				25, 
				$time_delay
				);

			jfoursquare_echo(
				$paranoid_array,
				$foursquare_number, 
				$time_pause, 
				$pixel_height, 
				$this->number, //$this->number is the Widgets number that WP generate
				$no_fade,
				$time_delay
				);
		}
	}

	function form($instance)
	{
?>

		<label for="<?php echo $this->get_field_id('title');?>">
			Title:<br />
			<input
				id="<?php echo $this->get_field_id('title');?>"
				name="<?php echo $this->get_field_name('title');?>"
				value="<?php echo esc_attr($instance['title']) ?>"
			/><br />
		</label>

		<label for="<?php echo $this->get_field_id('foursquare_history_feed');?>">
			Foursquare RSS URL:<br />
			Login to FS and <a href="https://foursquare.com/feeds/">go here to get yours</a>.
			<input
				id="<?php echo $this->get_field_id('foursquare_history_feed');?>"
				name="<?php echo $this->get_field_name('foursquare_history_feed');?>"
				value="<?php echo esc_attr($instance['foursquare_history_feed']) ?>"
			/><br />
		</label>

		<label for="<?php echo $this->get_field_id('foursquare_number');?>">
			Number of checkins to show (up to 25):<br />
			<input
				id="<?php echo $this->get_field_id('foursquare_number');?>"
				name="<?php echo $this->get_field_name('foursquare_number');?>"
				value="<?php echo esc_attr($instance['foursquare_number']) ?>"
				size="2" maxlength="2"
			/><br />
		</label>

		<label for="<?php echo $this->get_field_id('time_pause');?>">
			Time to pause (in milliseconds):<br />
			<input
				id="<?php echo $this->get_field_id('time_pause');?>"
				name="<?php echo $this->get_field_name('time_pause');?>"
				value="<?php echo esc_attr($instance['time_pause']) ?>"
				size="5" maxlength="5"
			/><br />
		</label>

		<label for="<?php echo $this->get_field_id('pixel_height');?>">
			Div Height in Pixels (remove if Disable Fade In/Out if is checked):<br />
			<input
				id="<?php echo $this->get_field_id('pixel_height');?>"
				name="<?php echo $this->get_field_name('pixel_height');?>"
				value="<?php echo esc_attr($instance['pixel_height']) ?>"
				size="3" maxlength="3"
			/><br />
		</label>

		<label for="<?php echo $this->get_field_id('no_fade');?>">
			Disable Fade In/Out:<br />
			<input
				type="checkbox"
				id="<?php echo $this->get_field_id('no_fade');?>"
				name="<?php echo $this->get_field_name('no_fade');?>"
				value="1"
				<?php if ( $instance['no_fade'] == TRUE) { print 'checked="yes" '; } ?>
			/><br />
		</label>


		<label for="<?php echo $this->get_field_id('time_delay');?>">
			Hours To Delay (for the paranoid):<br />
			<input
				id="<?php echo $this->get_field_id('time_delay');?>"
				name="<?php echo $this->get_field_name('time_delay');?>"
				value="<?php echo esc_attr($instance['time_delay']) ?>"
				size="5" maxlength="5"
			/><br />
		</label>
<?php
	}
}
