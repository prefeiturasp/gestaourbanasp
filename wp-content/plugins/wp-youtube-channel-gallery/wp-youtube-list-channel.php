<?php
/*
Plugin Name: WP Youtube channel gallery
Plugin URI: http://brunotnteixeira.wordpress.com/2010/11/07/update-to-2-0-version-of-wp-youtube-channel-gallery/
Description: Displays the most recent videos on a YouTube channel in your wp blog.  This plugin have an page to config the channel and the number of videos to be displayed.
Version: 2.1
Author: Bruno Neves
Author URI: http://brunotnteixeira.wordpress.com


	Note: This plugin is based in "latest-youtube-videos". Please check this also ;)
			URL: http://wordpress.org/extend/plugins/latest-youtube-videos/

	
	Copyright 2010-2011 Bruno Neves (email: bruno.tntex at gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, see <http://www.gnu.org/licenses/>
	or write to the Free Software Foundation, Inc., 59 Temple Place,
	Suite 330, Boston, MA  02111-1307  USA
	
*/

register_activation_hook(__FILE__,'wp_youtube_gallery_install');
function wp_youtube_gallery_install(){
}

// Carregar o widget
add_action('widgets_init', 'wp_youtube_widget');

// Action hook to create the shortcode
add_shortcode('youtubechannel', 'wp_youtube_shortcode');

//create shortcode e.g. [youtubechannel channelname="GoogleDevelopers" numvideos="1" width="560" showvideotitle="No"]
//  [youtubechannel] uses all defaults which is the same as [youtubechannel channelname="GoogleDevelopers" numvideos="1" width="560" showvideotitle="No"]
//  [youtubechannel channelname="virtualeyesee" numvideos="3"]
function wp_youtube_shortcode($atts, $content = null) {
	global $post;
	extract(shortcode_atts(array(
		"channelname" => 'GoogleDevelopers', 
		"numvideos" => '1',
		"width" => '560',
		"showvideotitle" => 'Yes'
	), $atts));
	
	$height = 340/560*$width;
	if ($showvideotitle != 'Yes' && $showvideotitle != 'No' ) {
    $showvideotitle = 'No';
  }
  $videoDisplay = '';
  
	$videoDisplay .= '<script type="text/javascript">
    function wp_youtube_gallery_post_page(data) {
    	var feed = data.feed;
    	var entries = feed.entry || [];
    	var html = [];
    	for (var i = 0; i < entries.length; i++) {
    		// Parse out YouTube entry data
    		var entry = entries[i];
    		var title = entry.title.$t;
    		var width = "'.$width.'";
    		var height = "'.$height.'";
    		var showtitle = "'.$showvideotitle.'";
    		var titledisplay = \'\';
    		if(showtitle == "Yes") {
    		  titledisplay = "<h4>" + title + "</h4>";
        } else {
          titledisplay = "";
        }
    		var playerUrl = entries[i].media$group.media$content[0].url;
    		';
  $videoDisplay .= '  	
    		html.push( "<div class=\"wp_youtube_gallery\">", titledisplay ,"\n",
    		           "<object width=\'" , width , "\' height=\'" , height , "\'><param name=\'movie\' value=\'" , playerUrl , "&hl=en&fx=1&\'></param><param name=\'allowFullScreen\' value=\'true\'></param><param name=\'allowscriptaccess\' value=\'always\'></param><embed src=\'" , playerUrl , "&hl=en&fs=1&\' type=\'application/x-shockwave-flash\' allowscriptaccess=\'always\' allowfullscreen=\'true\' width=\'" , width , "\' height=\'" , height , "\'></embed></object></div><p></p>" );
    		}
    	document.getElementById(\'videos_on_post_'.$post->ID.'\').innerHTML = html.join(\'\');
    	} 
    </script>
    <div id="videos_on_post_'.$post->ID.'"> <!-- The wp_youtube_gallery_post_page() JavaScript function places the YouTube video code here -->
    </div> 
    <script 
        type="text/javascript" 
        src="http://gdata.youtube.com/feeds/users/'.$channelname.'/uploads?alt=json-in-script&max-results='.$numvideos.'&callback=wp_youtube_gallery_post_page">
    </script>';

    return $videoDisplay;
}	

// Ativa o sidebar
function wp_youtube_widget() {
  /* 
    2010-11-05 - Brad Trivers - http://sunriseweb.ca - switched to use register_widget instead of register_sidebar_widget which is deprecated 
                                                     - removed use of plugin options since can set all info in widget settings
                                                     - added widget settings for channel name, num videos, width and showtitle
  */
  register_widget('show_wp_youtube_gallery');
}


//wp_youtube_widget class
class show_wp_youtube_gallery extends WP_Widget {

	//process our new widget
	function show_wp_youtube_gallery() {
		$widget_ops = array('classname' => 'wp_youtube_widget', 'description' => 'WP Youtube Channel Gallery');
		$this->WP_Widget('wp_youtube_widget', 'WP Youtube Channel Gallery', $widget_ops);
	}
 
 	//build our widget settings form
	function form($instance) {
		$defaults = array( 'title' => 'Youtube Channel', 'channelname' => 'GoogleDevelopers', 'numvideos' => '1', 'width' => '250', 'showtitle' => 'No');
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title = strip_tags($instance['title']);
		$channelname = strip_tags($instance['channelname']);
		$numvideos = strip_tags($instance['numvideos']);
		$width = strip_tags($instance['width']);  
		$showtitle = strip_tags($instance['showtitle']);
		?>
			<p><?php _e('Title', 'wp_youtube_gallery') ?>: <input class="widefat" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
			<p><?php _e('Channel Name', 'wp_youtube_gallery') ?>: <input class="widefat" name="<?php echo $this->get_field_name('channelname'); ?>" type="text" value="<?php echo esc_attr($channelname); ?>" /></p>
			<p><?php _e('# Videos', 'wp_youtube_gallery') ?>: <input class="widefat" name="<?php echo $this->get_field_name('numvideos'); ?>" type="text" value="<?php echo esc_attr($numvideos); ?>" /></p>
			<p><?php _e('Width in px', 'wp_youtube_gallery') ?>: <input class="widefat" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo esc_attr($width); ?>" /></p>
			<p><?php _e('Show Video Title', 'wp_youtube_gallery') ?>: <select class="widefat" name="<?php echo $this->get_field_name('showtitle'); ?>">
                                                        		<?php 
                                                            if(esc_attr($showtitle) == "Yes") {
                                                              echo '
                                                                <option value="No">No</option>
                                                        		    <option selected value="Yes">Yes</option>
                                                              </select>   
                                                              ';
                                                            } else {
                                                              echo '
                                                                <option value="No">No</option>
                                                        		    <option value="Yes">Yes</option>
                                                              </select>   
                                                              ';
                                                            } 
                                                            ?> 
                                                        		
      </p>
		<?php
	}
 
  	//save our widget settings
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags(esc_attr($new_instance['title']));
		$instance['channelname'] = strip_tags(esc_attr($new_instance['channelname']));
		$instance['width'] = intval($new_instance['width']);
		$instance['numvideos'] = intval($new_instance['numvideos']);
		$instance['showtitle'] = strip_tags(esc_attr($new_instance['showtitle']));
		return $instance;
	}
 
 	//display our widget
	function widget($args, $instance) {
		global $post;
		extract($args);
 
		echo $before_widget;
		$title = apply_filters('widget_title', $instance['title'] );
		$channelname = empty($instance['channelname']) ? 'GoogleDevelopers' : apply_filters('widget_channelname', $instance['channelname']);
		$width = empty($instance['width']) ? 250 : apply_filters('widget_width', $instance['width']);
		$height = 340/560*$width; 
		$numvideos = empty($instance['numvideos']) ? 1 : apply_filters('widget_numvideos', $instance['numvideos']);
		$showtitle = apply_filters('widget_showtitle', $instance['showtitle'] );
 
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };
		
		?>
    <script type="text/javascript">
    function wp_youtube_gallery(data) {
    	var feed = data.feed;
    	var entries = feed.entry || [];
    	var html = [];
    	for (var i = 0; i < entries.length; i++) {
    		// Parse out YouTube entry data
    		var entry = entries[i];
    		var title = entry.title.$t;
    		var width = "<?php echo $width; ?>";
    		var height = "<?php echo $height; ?>";
    		var showtitle = "<?php echo $showtitle; ?>";
    		var titledisplay = '';
    		if(showtitle == "Yes") {
    		  titledisplay = '<h4>' + title + '</h4>';
        } else {
          titledisplay = '';
        }
    		var playerUrl = entries[i].media$group.media$content[0].url;
    		html.push( "<div class=\"wp_youtube_gallery\">", titledisplay ,"\n",
    		           "<object width='" , width , "' height='" , height , "'><param name='movie' value='" , playerUrl , "&hl=en&fx=1&'></param><param name='allowFullScreen' value='true'></param><param name='allowscriptaccess' value='always'></param><embed src='" , playerUrl , "&hl=en&fs=1&' type='application/x-shockwave-flash' allowscriptaccess='always' allowfullscreen='true' width='" , width , "' height='" , height , "'></embed></object></div><p></p>" );
    		}
    	document.getElementById('videos').innerHTML = html.join('');
    	} 
    </script>
    <div id="videos"> <!-- The showMyVideos() JavaScript function places the YouTube video code here -->
    </div>
    <?php
    
    // Request feed of latest videos from YouTube 
    ?>
    <script 
        type="text/javascript" 
        src="http://gdata.youtube.com/feeds/users/<?php echo $channelname ?>/uploads?alt=json-in-script&max-results=<?php echo $numvideos ?>&callback=wp_youtube_gallery">
    </script>
    <?php

		echo $after_widget;
	}
}
?>