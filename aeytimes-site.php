<?php

/*
 * Plugin Name: AeyTimes Site Feedback / Comments Widget
 * Plugin URI: http://aeytimes.com/
 * Author: Alexey Yermolai
 * Author URI: http://aeytimes.com/
 * Version: 1.0.5
 * Description: Displays the latest comments and feedback for a website.
 */

include_once(ABSPATH . WPINC . '/feed.php');

class AeytimesSiteRSS extends WP_Widget {
	function AeytimesSiteRSS() {
		$widget_ops = array( 'classname' => 'aeytimes', 'description' => 'A widget displaying the latest comments of your idea on AeyTimes.' );
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'aeytimes-site' );
		$this->WP_Widget('aeytimes-site', 'Aeytimes Site Widget', $widget_ops, $control_ops);
	}

	function widget($args, $instance) {
		add_filter( 'wp_feed_cache_transient_lifetime', create_function( '$a', 'return 900;' ) );
		extract($args, EXTR_SKIP);
		$title = apply_filters('widget_title', $instance['title']);
		$uri = "http://aeytimes.com/comment_feeds/".$instance['aeyid'].".xml";
		$max = $instance['latestmax'];

		echo $before_widget;
		if ($title) {
			echo $before_title . $title . $after_title;
		}

		if($instance['userid'] == "") {
			echo "Invalid Idea";
		}
		else
		{
		$rss = fetch_feed($uri);

		if (!is_wp_error($rss)) {
			$maxitems = $rss->get_item_quantity($max);
			$rss_items = $rss->get_items(0, $maxitems);
		}
		
		echo "<ul>";
		foreach ($rss_items as $item) {
			echo "<li>";
//			echo "<a href=\"".$item->get_permalink()."\">";
			echo $item->get_description();
//			echo "</a>";
			echo "</li>";
		}
		echo "</ul>";
		echo "<p style=\"text-align: right;\"><a href=\"http://aeytimes.com/ideas/".$instance['aeyid']."/".$instance['userid']."/\" target=\"_blank\">... Give feedback</a></p>";
		}
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['aeyid'] = strip_tags($new_instance['aeyid']);
		$instance['userid'] = file_get_contents("http://aeytimes.com/getfileid/".$instance['aeyid']."/");
		$instance['latestmax'] = strip_tags($new_instance['latestmax']);
		return $instance;
	}

	function form($instance) {
		$title = esc_attr($instance['title']);
		$aeyid = esc_attr($instance['aeyid']);
		$latestmax = esc_attr($instance['latestmax']);
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
			<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('aeyid'); ?>">Idea ID:</label>
			<input id="<?php echo $this->get_field_id('aeyid'); ?>" name="<?php echo $this->get_field_name('aeyid'); ?>" value="<?php echo $aeyid; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('latestmax'); ?>">Number of comments to display:</label>
			<select id="<?php echo $this->get_field_id('latestmax'); ?>" name="<?php echo $this->get_field_name('latestmax'); ?>" class="widefat" style="width:100%;">
				<option <?php if ( '1' == $latestmax ) echo 'selected="selected"'; ?>>1</option>
				<option <?php if ( '2' == $latestmax ) echo 'selected="selected"'; ?>>2</option>
				<option <?php if ( '3' == $latestmax ) echo 'selected="selected"'; ?>>3</option>
				<option <?php if ( '4' == $latestmax ) echo 'selected="selected"'; ?>>4</option>
				<option <?php if ( '5' == $latestmax ) echo 'selected="selected"'; ?>>5</option>
				<option <?php if ( '6' == $latestmax ) echo 'selected="selected"'; ?>>6</option>
				<option <?php if ( '7' == $latestmax ) echo 'selected="selected"'; ?>>7</option>
				<option <?php if ( '8' == $latestmax ) echo 'selected="selected"'; ?>>8</option>
				<option <?php if ( '9' == $latestmax ) echo 'selected="selected"'; ?>>9</option>
				<option <?php if ( '10' == $latestmax ) echo 'selected="selected"'; ?>>10</option>
			</select>
		</p>
<?php
	}
}

function register_AeytimesSiteRSSWidget(){
	register_widget('AeytimesSiteRSS');
}


function addAeytimesSiteRSSWidgetOptions() {
	echo '<div class="wrap">';
	echo '<h2>Instructions for using the AeyTimes Site Feedback / Comments Widget</h2>';
	echo '<p><a href="http://aeytimes.com/">AeyTimes</a> is an Idea Journal and Social Network that allows people to share ideas and inspirations, and to submit feedback or comments to improve websites, services, or products.</p>';
	echo '<p>To start using the Aeytimes Site Feedback / Comments Widget:</p>';
	echo '<ol>';
	echo '<li>First <a href="http://aeytimes.com/signup/">register</a> at <a href="http://aeytimes.com/">AeyTimes</a>.</li>';
	echo '<li>Once you have created your account, <a href="http://aeytimes.com/login/">login</a> and create your AeyTimes ideas pages.</li>';
	echo '<li>Title the name of your idea page something like "Feedback for YOURSITE", where YOURSITE is the name of your site.</li>';
	echo '<li>Once you complete the rest of the idea submission process, a URL with the idea number can be found after the forward slash after the word "ideas" ( eg http://aeytimes.com/ideas/###/Feedback_for_YourSite ).</li>';
	echo '<li>Within your WordPress administration panel, drag your widget to the desired position on the \'Widgets\' page under \'Appearance\'.</li>';
	echo '<li>Enter a title for your widget, and select the number of feedback comments that you would like to display.</li>';
	echo '<li>Enter that idea number into the "Idea ID" field of your widget within the WordPress administration panel.</li>';
	echo '<li>Click on "Save".</li>';
	echo '<li>For feedback comments to appear on your widget, users must submit feedback on the Comments section on the particular ideas feedback page created on AeyTimes. It may take up to 15 minutes before the comments start displaying.</li>';
	echo '</ol>';
	echo '</div>';
}

function addAeytimesSiteRSSWidgetOptionsPage() {
	add_options_page('Site Feedback Options', 'Site Feedback', 'manage_options', 'aeytimes-sites', 'addAeytimesSiteRSSWidgetOptions' );
}

add_action('admin_menu', 'addAeytimesSiteRSSWidgetOptionsPage' );
add_action('init', 'register_AeytimesSiteRSSWidget', 1);

?>
