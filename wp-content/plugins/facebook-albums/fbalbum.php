<?php
/*
Plugin Name: Facebook Albums
Plugin URI: http://glamanate.com/wordpress/facebook-album/
Description: Facebook Albums allows you to display Facebook Albums on your WordPress site. Brought to you by <a href="http://dooleyandassociates.com/?utm_source=fbalbum&utm_medium=referral&utm_campaign=Facebook%2BAlbum">Dooley & Associates</a>
Version: 1.0.8
Author: Matt Glaman
Author URI: http://glamanate.com


Copyright 2012  Matt Glaman  (email : nmd.matt@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

//Prior to 3.3 wp_enqueue can't be called mid-body to load in footer.
//TODO: Does jQuery version shipped with 3.3 and prior support this Lightbox?
if( $wp_version < 3.3) {
	add_action('wp_enqueue_scripts', array('FB_Album', '_enqueue_resources'));
}

//Registers the short code to be placed within posts or pages.
add_shortcode( 'fbalbum', array('FB_Album', 'shortcode_callback') );

//Registers options page
add_action('admin_menu', array('FB_Album', '_setup_opt_menu'));

//Registers actions
register_activation_hook(__FILE__, array('FB_Album', 'init'));

//Sets up the widget
add_action( 'widgets_init', create_function( '', 'return register_widget( "FB_Album_Widget" );' ) );

class FB_Album {
	
	protected static $album_url;	//Stores the album URL in the class
	protected static $album_id; 	//Stores the album ID in the class
	protected static $album_limit;
	
	//The graph API URl the plugin will access. Script replaces {ALBUM_ID} with self::$album_id
	protected static $graph_url = 'https://graph.facebook.com/{ALBUM_ID}?fields=id,name,photos.limit({LIMIT}).fields(source,link,images,name)';
	
	//Graph API plugin accesses through widget, limits query to just 6 items.
	protected static $widget_graph_url = 'https://graph.facebook.com/{ALBUM_ID}?fields=id,name,photos.limit({LIMIT}).fields(source,link,images,name)';
	
	//Init, let us save options
	public static function init() {
		add_option('fbalbum_size', '5');
		add_option('fbalbum_pages');
		add_option('fbalbum_lightbox', 'true');
	}
	
	/**
	 * Short code callback for WordPress add_shortcode
	 * @param atts
	 * @return void
	 */
	public static function shortcode_callback( $atts ) {
		
		//Defaults to null URL. If no limit set, default to 200 (max album size, besides Timeline) 
		extract( shortcode_atts( array(
			'url' => '', 'limit' => 200
		), $atts ) );
		self::_set_album_url( $url );
		self::$album_limit = $limit;
		
		//Sets up album HTML. Gathers data from class variables.
		return self::print_album();		
	}
	
	/**
	 * Builds HTML string to output album photos
	 */
	public static function print_album() {
			if(!self::_get_album_id() || !($fb = self::_get_graph_results(self::$album_limit)) )
				return 'Album ID was empty, or Facebook API returned empty result. Please double check your Facebook album URL.';
			if(!$fb['photos'])
				return 'Facebook API came back with a result, but it had no photos!';						
			
			$html = '<div class="fbalbum-container">';
			$html .= '<h2><a href="' . self::_clean_url(self::_get_album_url()) . '" target="_blank"">' . $fb['name'] . '</a></h2>';
			$html .= '<script type="text/javascript">var templateDir = "'.home_url().'";</script><style type="text/css">.fbalbum .image { background-position: 50% 25%; background-size: cover !important;box-shadow: inset 1px 1px 10px black; background-repeat: no-repeat;}.fbalbum .size-8 { width:50px;height:50px;}.fbalbum .size-6 { width:125px;height:125px;}.fbalbum .size-5 { width:206px;height:206px;}.fbalbum .size-4 { width:325px;height:300px;}.fbalbum .size-3 { width:425px;height:400px;}.fbalbum .size-2 { width:650px;height:625px;}</style>';
			//Above line passes your absolute URL to the Javascript Lightbox
			self::_enqueue_resources();
			
			$size = get_option('fbalbum_size', '5');
			
			//Reverse array to show oldest to newest
			if(get_option('fbalbum_order'))
				$fb['photos']['data'] = array_reverse($fb['photos']['data']);
			
			foreach ($fb['photos']['data'] as $img) {
				$html .= '<div class="fbalbum fbalbum-wrapper" style="float: left;margin: 2px;">';
				$html 	.= '<a href="'. self::_clean_url($img['source']) . '" title="'. $img['name'] .'" target="_blank" rel="lightbox[fbalbum]">';
				$html 	.= '<div class="image size-'. $size .'" style="background: url(' . self::_clean_url($img['images'][$size]['source']) . ')">&nbsp;</div>';
				$html	.= '</a>';
				$html .= '</div>';
			}
			$html .= '<div style="clear:both">&nbsp;</div>';
			$html .="</div>";
			return $html;
	}

	/**
	 * Builds Facebook API string and returns JSON output
	 */
	public static function _get_graph_results($limit) {
			$facebook_graph_url = str_replace('{ALBUM_ID}', self::_get_album_id(), self::$graph_url);
			$facebook_graph_url = str_replace('{LIMIT}', $limit, $facebook_graph_url);
			return self::_do_curl($facebook_graph_url);
	}

	public static function _do_curl($uri) {
		$facebook_graph_results = null;
		$facebook_graph_url = $uri; //TODO: Add URL checking here, else error out
		
		if(ini_get('allow_url_fopen')) {
			$facebook_graph_results = @file_get_contents($facebook_graph_url);
		} else {
			//Attempt CURL
			if (extension_loaded('curl')){
				$ch = curl_init();
				curl_setopt($ch,CURLOPT_URL, $facebook_graph_url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				if(!$facebook_graph_results = curl_exec($ch)) 
					echo 'Curl error: ' . curl_error($ch);
				curl_close($ch);
			} else {
				die('Sorry, your server does not allow remote fopen or have CURL');
			}
		}
		$facebook_graph_results = json_decode($facebook_graph_results, true);
		return $facebook_graph_results;		
	}

	public static function _enqueue_resources() {
		if(get_option('fbalbum_lightbox')) {
			wp_enqueue_script('lightbox-js', plugins_url( '/lightbox/lightbox.js', __FILE__ ), array('jquery'));	
			wp_enqueue_style('lightbox-style', plugins_url( 'lightbox/lightbox.css', __FILE__ ));
		}
			wp_enqueue_style('fbalbum-style', plugins_url( '/fbalbum.css', __FILE__ ));
	}

	 public static function _setup_opt_menu() {
		add_options_page('Facebook Album', 'Facebook Album', 'publish_pages', 'fbalbum-menu', array('FB_Album', '_build_opt_menu'), '', 25);
	}
	 
	public static function _build_opt_menu() {
		//Setup plugin options.
		$thumb_size		= get_option('fbalbum_size');
		$wp_pages		= get_option('fbalbum_pages');
		$album_order	= get_option('fbalbum_order');
		$lightbox_opt	= get_option('fbalbum_lightbox');
		
		//If nonce, do update
		if(isset($_POST["fbalbum_nonce"])):
			$thumb_size		= $_POST["fbalbum_size"];
			$wp_pages		= $_POST["fbalbum_pages"];
			$album_order	= $_POST["fbalbum_order"];
			$lightbox_opt	= $_POST["fbalbum_lightbox"];
			
			update_option('fbalbum_size', $thumb_size);
			update_option('fbalbum_pages', $wp_pages);
			update_option('fbalbum_order', $album_order);
			update_option('fbalbum_lightbox', $lightbox_opt);
			
			echo '<div class="updated"><p><strong>Facebook Albums has been updated</strong></p></div>';	
		endif; ?>
		<?php //echo '<pre>';print_r($wp_pages);echo'</pre>'; ?>
			<div class="wrap">
				<div id="icon-options-general" class="icon32"><br></div>
				<h2>Facebook Album Options</h2>
				<p>Pick your thumbnail size to display on pages. More options and features coming soon. Suggestions? Send an email to nmd.matt@gmail.com on features you would like.</p>
				
				<form method="post" action="" name="fbalbum_options">
					<?php 	wp_nonce_field( basename( __FILE__ ), 'fbalbum_nonce' ); ?>
					<table class="form-table">
						<tbody>
							<tr align="top">
								<th scope="row">Album Order</th>
								<td>
									<label><input type="checkbox" value="on" name="fbalbum_order" id="fbalbum_order" <?php checked($album_order, 'on'); ?>/> Reverse the display order (Oldest -> Newest)</label>
								</td>
							</tr>
							<tr align="top">
								<th scope="row">Enable Lightbox</th>
								<td>
									<label><input type="checkbox" value="true" name="fbalbum_lightbox" id="fbalbum_order" <?php checked($lightbox_opt, 'true'); ?>/> Use built in Lightbox supplied with plugin</label>
									<p>Disable this if you already have a Lightbox plugin installed. Facebook Album utilizes rel="lightbox[album]" to call Lightbox and set a gallery.</p>
								</td>
							</tr>
							<tr align="top">
								<th scope="row"><label for="fbalbum_size">Thumbnail Size</label></th>
								<td><select name="fbalbum_size" id="fbalbum_size" value="" class="regular-text">
										<option value="8" <?php if($thumb_size == 8) echo 'selected="selected"'; ?>>Smaller</option>
							  			<option value="6" <?php if($thumb_size == 6) echo 'selected="selected"'; ?>>Small</option>
							  			<option value="5" <?php if($thumb_size == 5) echo 'selected="selected"'; ?>>Medium</option>
							  			<option value="4" <?php if($thumb_size == 4) echo 'selected="selected"'; ?>>Big</option>
							  			<option value="3" <?php if($thumb_size == 3) echo 'selected="selected"'; ?>>Bigger</option>
							  			<option value="2" <?php if($thumb_size == 2) echo 'selected="selected"'; ?>>Large</option>
							  		</select>
									<p class="description">Pick a thumbnail size to be displayed on shortcode pages.</p>
								</td>
							</tr>
							<tr align="top">
								<th scope="row"><label>Widget Album by Page</label></th>
								<td>
									<div style="height: 300px; overflow: auto; padding-left: 10px; border-left: 2px solid #e6e6e6;">
									<?php foreach( get_pages() as $page): ?>
										<p><label for="wp_page_<?php echo $page->ID; ?>"><?php echo $page->post_title; ?></label><br/><input type="text" size="60" name="fbalbum_pages[<?php echo $page->ID; ?>]" id="wp_page_<?php echo $page->ID; ?>" value="<?php echo $wp_pages[$page->ID] ?>"></p>
									<?php endforeach; ?>
									</div>
								</td>
							</tr>
							<tr align="top">
								<th scope="row">Got Ideas?</th>
								<td>Please contact me for more options ideas</td>
							</tr>
						</tbody>
					</table>
					  <p class="submit">
					  <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
					  </p>
				</form>
			</div>
		<?php
	}
	
	/**
	 * Finds and saves album ID by breaking apart the Facebook URL
	 * @return void
	 */
	protected static function _find_album_id() {
		if(!self::_get_album_url())
				return;
		
		//Explodes URL based on slashes, we need the end of the URL
		$facebook_album_id = explode('/', self::_get_album_url());
		$facebook_album_id = $facebook_album_id['5'];
		//Explodes section by periods, Album ID is first of the 3 sets of numbers
		$facebook_album_id = explode('.', $facebook_album_id);
		$facebook_album_id = $facebook_album_id['1'];
		
		self::_set_album_id( $facebook_album_id );
	}
	
	/**
	 * Sets $album_url within class.
	 * @param url
	 * @return void
	 */
	public static function _set_album_url( $url ) {
		 self::$album_url = $url; 
		 self::_find_album_id();
	}
	/**
	 * Gets $album_url from within class
	 * @return url
	 */ public static function _get_album_url() { return self::$album_url; }
	/**
	* Sets $album_id within class
	* @param id
	* @return void
	*/
	public static function _set_album_id( $id ) { self::$album_id = $id; }
	/**
	* Gets $album_url from within class
	*/
	public static function _get_album_id() { return self::$album_id; }
	/**
	* Makes URLs validator friendly by replacing & to &amp;
	* @param url
	* @return string
	*/
	public static function _clean_url( $url ) {
	return str_replace('&', '&amp;', $url);
	}
	/**
	* Switch on sizes for CSS.
	*/

}


/**
 * The Facebook Album Widget
 * Displays 6 
 */
class FB_Album_Widget extends WP_Widget {

  public function __construct() {
    parent::__construct( 'facebook-album', 'Facebook Album', array('description' => 'Paste a Facebook Page album URL and display the most recent photos as a widget.') );
		//Widget has been set up
	}

 	public function form( $instance ) {
		//The form you see on the admin side
    	$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'url' => '', 'thumb_size' => '', 'limit' => '6') );
		$title = $instance['title'];
    	$url = $instance['url'];
		$thumb_size = $instance['thumb_size'];
		$limit = $instance['limit'];
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>">Title:</label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></p>
	  	<p><label for="<?php echo $this->get_field_id('url'); ?>">Default Facebook Album URL: <input class="widefat" id="<?php echo $this->get_field_id('url'); ?>" name="<?php echo $this->get_field_name('url'); ?>" type="text" value="<?php echo attribute_escape($url); ?>" /></label></p>
	  	<p><label for="<?php echo $this->get_field_id('thumb_size'); ?>">Thumbnail Size:</label><select id="<?php echo $this->get_field_id('thumb_size'); ?>" name="<?php echo $this->get_field_name('thumb_size'); ?>">
	  			<option value="8" <?php if($thumb_size == 8) echo 'selected="selected"'; ?>>Smaller</option>
	  			<option value="6" <?php if($thumb_size == 6) echo 'selected="selected"'; ?>>Small</option>
	  			<option value="5" <?php if($thumb_size == 5) echo 'selected="selected"'; ?>>Medium</option>
	  			<option value="4" <?php if($thumb_size == 4) echo 'selected="selected"'; ?>>Big</option>
	  			<!--<option value="3" <?php if($thumb_size == 3) echo 'selected="selected"'; ?>>Bigger</option>-->
	  		</select>
	  	<p><label for="<?php echo $this->get_field_id('limit'); ?>">Number of pictures: <input id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="text" value="<?php echo attribute_escape($limit); ?>" size="3"/></label></p>
	  	</p>
		<?php		
	}

	public function update( $new_instance, $old_instance ) {
		//Saves the options
		
		    $instance = $old_instance;
			$instance['title'] = $new_instance['title'];
		    $instance['url'] = $new_instance['url'];
			$instance['thumb_size'] = $new_instance['thumb_size'];
			$instance['limit'] = $new_instance['limit'];
		    return $instance;
	}

	public function widget( $args, $instance ) {
		extract($args);
		
		echo $before_widget;
		if(!empty($instance['title']))
			echo '<h3 class="widget-title">'.esc_attr($instance['title']).'</h3>';
		
		/** Check if we should show a specified album for this page/post **/
		$wp_pages = get_option('fbalbum_pages');
		global $post;
		$facebook_album_url = ($wp_pages[$post->ID]) ? $wp_pages[$post->ID] : $instance['url'];
		
		if($facebook_album_url != '') {
			FB_Album::_set_album_url( $facebook_album_url );
		} else { 
			echo 'No Facebook Album specified.';
			return;
		}
	
		if(!FB_Album::_get_album_id()) {
			echo 'The Facebook album ID came up empty, double check the URL';
			return;
		} else {
			FB_Album::_enqueue_resources();
			
			if(!FB_Album::_get_album_id() || !($fb = FB_Album::_get_graph_results($instance['limit'])) )
				return 'Sorry, there was an error loading the Facebook album, please refresh the page and try again.';
			
			if(!$fb['photos']) {
				echo 'Album not found, or is not public.';
			} else {   ?> 
				<?php if( $title )
					echo '<h2><a href="' . FB_Album::_clean_url(FB_Album::_get_album_url()) . '" target="_blank"">' . $fb['name'] . '</a></h2>'; ?>
				<script type="text/javascript">var templateDir = "<?php echo home_url(); ?>";</script>
				<div class="fbalbum fbalbum-widget"> <?php
				//Reverse array to show oldest to newest
				if(get_option('fbalbum_order'))
					$fb['photos']['data'] = array_reverse($fb['photos']['data']);
								
				foreach ($fb['photos']['data'] as $img) :
					$img_info = $img['images'][$instance['thumb_size']]; ?>
					<div class="item">
						<a href="<?php echo FB_Album::_clean_url($img['source']) ?>" target="_blank" rel="lightbox[widget]">
							<div class="image size-<?php echo $instance['thumb_size']; ?>" style="background: url('<?php echo FB_Album::_clean_url($img_info['source']) ?>');">&nbsp;</div>
						</a>
					</div>
				
					<?php
				endforeach;
				echo '</div>';
			}
		}
		echo $after_widget; 
	}

}
?>