<?php

/**
 * @package better-related
 * @subpackage admin
 * @since 0.1.1
 * @todo manual scoring metabox
 */
class BetterRelatedAdmin extends BetterRelated {

	/**
	 * Constructor, set up the admin interface
	 *
	 * @since 0.1.1
	 *
	 * @return none
	 */
	public function __construct() {
		BetterRelated::__construct();
		load_plugin_textdomain(
			'better-related',
			false,
			basename( $this->plugin_dir ) . '/translations'
		);
		add_action(
			'admin_menu',
			array ( $this, 'add_page' )
		);
		add_action(
			'admin_init',
			array ( $this, 'register_setting' )
		);
		register_activation_hook(
			$this->plugin_file,
			array( $this, 'activate' )
		);
		add_action(
			'wp_insert_post',
			array( $this, 'timestamp_content' )
		);
	}

	/**
	 * Activation hook
	 *
	 * Get the default settings and update the plugins settings with new
	 * default values. Take care not to overwrite empty strings or falses
	 * in the user's options.
	 *
	 * @since 0.2.3
	 *
	 * @return none
	 */
	public function activate() {
		//$mysql = $this->get_mysql_version();
		//if ( !version_compare( $mysql, 5, '>=' ) )
		//	$this->deactivate_and_die( 'Your MySQL version is not supported' );
		$defaults	= $this->defaults();
		$saved		= $this->get_saved_options();
		foreach( $defaults as $key => $value ) {
			if ( @$saved[$key] === '' )
				unset( $defaults[$key] );
			elseif ( @$saved[$key] === false )
				unset( $defaults[$key] );
		}
		update_option( 'better-related', array_merge( $defaults, $saved ) );
		$this->create_fulltext_index( 'posts', 'post_content' );
		$this->create_fulltext_index( 'posts', 'post_title' );
	}

	/**
	 * Get the MySQL version string
	 *
	 * @since 0.4.3.1
	 *
	 * @return string MySQL version
	 */
	private function get_mysql_version() {
		global $wpdb;
		$query = "SELECT VERSION();";
		$r = $wpdb->get_var( $wpdb->prepare( $query ) );
		return $r;
	}

	/**
	 * Check if fulltext indexes exist and print an error
	 *
	 * @since 0.3.2
	 *
	 * @return none
	 */
	private function fulltext_index_exists_admin( $table, $column ) {
		global $wpdb;
		if ( !$this->fulltext_index_exists( $table, $column ) ) {
			echo '<div class="error">';
			printf(
				__( "There is no fulltext index for %s", 'better-related' ),
				"{$wpdb->prefix}$table.$column"
			);
			echo '</div>';
		}
	}

	/**
	 * Set up the options page
	 *
	 * @since 0.1.1
	 *
	 * @return none
	 */
	public function add_page() {
		if ( current_user_can ( 'manage_options' ) ) {
			$options_page = add_options_page (
				__( 'Better Related Content' , 'better-related' ),
				__( 'Better Related Content' , 'better-related' ),
				'manage_options',
				'better-related',
				array ( $this , 'admin_page' )
			);
			add_action(
				'admin_print_styles-' . $options_page,
				array( $this, 'css' )
			);
		}
	}

	/**
	 * Load admin CSS style
	 *
	 * @since 0.2.4
	 *
	 * @return none
	 */
	public function css() {
		wp_register_style(
			'better-related',
			plugins_url( basename( $this->plugin_dir ) . '/css/admin.css' ),
			null,
			'0.0.1'
		);
		wp_enqueue_style( 'better-related' );
	}

	/**
	 * Register the plugin option with the setting API
	 *
	 * @since 0.1.1
	 *
	 * @return none
	 */
	public function register_setting() {
		register_setting(
			'better-related_options',
			'better-related',
			array( $this, 'sanitize_options' )
		);
	}

	/**
	 * Sanitize the options
	 *
	 * @since 0.3.1
	 *
	 * @return sanitized data
	 */
	public function sanitize_options( $data ) {
		$defaults = $this->defaults();
		// float
		$float = array(
			'do_c2c',
			'do_t2t',
			'do_t2c',
			'do_k2c',
			'do_k2t',
			'do_x2x',
			'minscore'
		);
		foreach ( $float as $var ) {
			$data[$var] = floatval( $data[$var] );
		}
		// int
		$int = array(
			'maxresults',
			'cachetime',
			'filterpriority',
			'querylimit',
			't_querylimit'
		);
		foreach ( $int as $var ) {
			$data[$var] = intval( $data[$var] );
		}
		// int bigger zero
		$int_gt_zero = array(
			'cachetime',
			'filterpriority',
			'querylimit',
			't_querylimit'
		);
		foreach ( $int_gt_zero as $var ) {
			if ( $data[$var] < 1 ) {
				$data[$var] = $defaults[$var];
			}
		}
		// string, not empty
		$not_empty = array(
			'storage_id'
		);
		foreach ( $not_empty as $var ) {
			if ( !$data[$var] ) {
				$data[$var] = $defaults[$var];
			}
		}
		// lower limit
		if ( $data['querylimit'] <= 100 )
			$data['querylimit'] = 100;
		// bigger than var
		if ( $data['querylimit'] >= $data['t_querylimit'] )
			$data['t_querylimit'] = $data['querylimit'] * 2;
		// boolean
		$bool = array(
			'autoshowrss',
			'log',
			'stylesheet',
			'showdetails'
		);
		foreach ( $bool as $var ) {
			if ( $data[$var] == 'on' )
				$data[$var] = true;
			else
				$data[$var] = false;
		}
		// @todo usept and showpt
		// update mtime if necessary
		$update_mtime = array(
			'usept',
			'usetax',
			'do_c2c',
			'do_t2t',
			'do_t2c',
			'do_k2c',
			'do_k2t',
			'do_x2x',
			'querylimit',
			't_querylimit'
		);
		foreach ( $update_mtime as $var )
			if ( @$data[$var] != $this->get_option( $var ) )
				$data['mtime'] = time();
		return $data;
	}

	/**
	 * Form input helper that produces the correct HTML markup
	 *
	 * @since 0.2.2
	 *
	 * @param string $label Input label
	 * @param string $name Input name
	 * @param string $comment Input comment
	 * @return none
	 */
	private function input( $label, $name, $comment = false, $size = false ) {
		if ( is_integer( $size ) )
			$size = " size=\"$size\" "; ?>
		<tr valign="top">
			<th scope="row"> <?php
				echo $label; ?>
			</th>
			<td> <?php
				echo '<input type="text" name="better-related[' . $name . ']" value="' . $this->get_option( $name ) . '"' . $size . '/>';
				if ( $comment )
					echo ' ' . $comment;
				?>
			</td>
		</tr> <?php
	}

	/**
	 * Form hidden input helper
	 *
	 * @since 0.3.8
	 *
	 * @param string $name Input name
	 * @return none
	 */
	private function hidden( $name ) {
		echo '<input type="hidden" name="better-related[' . $name . ']" value="' . $this->get_option( $name ) . '" />';
	}

	/**
	 * Form checkbox helper
	 *
	 * @since 0.2
	 * @todo this is messy
	 *
	 * @param string $label Input label
	 * @param mixed $name String option name or array of name => [ subnames ]
	 * @param string $comment Input comment
	 * @return none
	 */
	private function checkbox( $label, $name, $comment = false ) { ?>
		<tr valign="top">
			<th scope="row"> <?php
				echo $label; ?>
			</th>
			<td> <?php
				if ( is_array( $name ) ) {
					foreach( $name[1] as $value ) {
						$this->single_checkbox( $name[0], $value );
					}
				}
				else {
					$checked = '';
					if ( $this->get_option( $name ) )
						$checked = ' checked="checked" ';
					echo '<input ' . $checked . 'name="better-related[' . $name . ']" type="checkbox" />';
				}
				if ( $comment )
					echo " $comment";
				?>
			</td>
		</tr> <?php
	}

	/**
	 * Form checkbox helper, for a single checkbox when an array of boxes
	 * was passed to checkbox()
	 *
	 * @since 0.2.2
	 *
	 * @param string $name Checkbox name
	 * @param string $value Checkbox value
	 * @return none
	 */
	private function single_checkbox( $name, $value ) {
		$option = $this->get_option( $name );
		$checked = '';
		if ( isset( $option[$value] ) && $option[$value] )
			$checked = ' checked="checked" ';
		echo "<span><input $checked type=\"checkbox\" name=\"better-related[" . $name . '][' . $value . "]\" /> $value</span>" . "\n";
	}

	/**
	 * Form select helper
	 *
	 * @since 0.2
	 *
	 * @param string $label Label
	 * @param string $name Select name
	 * @param array $choices Possible choices (strings)
	 * @return none
	 */
	private function select( $label, $name, $choices, $comment = false ) {
		$current = $this->get_option( $name ); ?>
		<tr valign="top">
			<th scope="row"> <?php
				echo $label; ?>
			</th>
			<td>
				<select name="better-related[<?php echo $name ?>]"> <?php
					foreach( $choices as $key => $value ) {
						if ( $key == $current )
							$select = ' selected="selected" ';
						else
							$select = '';
						echo "<option $select value=\"$key\" >$value</option>\n";
					} ?>
				</select> <?php
				if ( $comment )
					echo ' ' . $comment;
				?>
			</td>
		</tr> <?php
	}

	/**
	 * Output the options page
	 *
	 * @todo manual scoring options
	 * @since 0.1.1
	 *
	 * @return none
	 */
	public function admin_page () {
		$this->fulltext_index_exists_admin( 'posts', 'post_content' );
		$this->fulltext_index_exists_admin( 'posts', 'post_title' ); ?>
		<div id="nkuttler" class="wrap" >
			<div id="nkcontent">
				<h2><?php _e( 'Better Related Posts and Content Options', 'better-related' ) ?></h2>
					<form method="post" action="options.php"> <?php
					settings_fields( 'better-related_options' ); ?>
					<h3><?php _e( 'Main Options', 'better-related' ) ?></h3>
					<table class="form-table form-table-clearnone" > <?php
						$this->hidden(
							'mtime'
						);
						$this->hidden(
							'version'
						);
						// get relevant taxonomies
						$taxonomies	= get_taxonomies( array(
							'public'	=> true,
						) );
						$remove = array_search( 'nav_menu', $taxonomies );
						if ( $remove )
							unset( $taxonomies[$remove] );

						// get relevant post types
						$post_types	= get_post_types( array(
							'public'	=> true,
						) );
						// @todo, hm, keep attachments? make them an option?
						$remove = array_search( 'attachment', $post_types );
						if ( $remove )
							unset( $post_types[$remove] );

						$this->checkbox(
							sprintf( '<strong>%s</strong>', __( 'Automatically show related content on', 'better-related' ) ),
							array( 'autoshowpt', $post_types ),
							__( 'This option will add a link to the plugin\'s website by default, see below for options, or see the <tt>readme.txt</tt> for manual placement. ', 'better-related' )
						);
						$this->checkbox(
							__( 'Automatically in feed', 'better-related' ),
							'autoshowrss'
						); ?>
					</table>

					<h3><?php _e( 'Display Options', 'better-related' ) ?></h3>
					<table class="form-table form-table-clearnone" > <?php
						$this->input(
							__( 'Max results', 'better-related' ),
							'maxresults',
							__( 'How many related posts to show.', 'better-related' )
						);
						$this->input(
							__( 'Content filter priority', 'better-related' ),
							'filterpriority',
							__( 'Changing this value might change the position of the related content list on your pages.', 'better-related' )
						);
						$this->input(
							__( 'Related posts title', 'better-related' ),
							'relatedtitle',
							false,
							60
						);
						$this->input(
							__( 'No related posts found text', 'better-related' ),
							'relatednone',
							__( 'Leave empty for no output at all.', 'better-related' ),
							60
						);
						$this->checkbox(
							__( 'Display scoring details', 'better-related' ),
							'showdetails',
							__( 'If this is enabled logged in admins will see the exact score for each related post on the frontend. There will also be some information about performed database queries and query time.', 'better-related' )
						);
						$this->checkbox(
							__( 'Add default stylesheet', 'better-related' ),
							'stylesheet'
						);
						$this->select(
							__( 'Promote the plugin', 'better-related' ),
							'thanks',
							array(
								//'title'	=> 'Link the title',
								'below'	=> __( 'Link below related posts', 'better-related' ),
								'info'	=> __( 'Tiny info-link next to the title', 'better-related' ),
								'none'	=> __( 'No, don\'t promote', 'better-related' )
							),
							__( 'Please thank the plugin author by linking to the plugin\'s page or consider blogging about the plugin if you don\'t.', 'better-related' )
						);
						?>
					</table>

					<p class="submit">
						<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
					</p>

					<?php /*
					<h3><?php _e( 'Advanced Options', 'better-related' ) ?></h3>

					<h4><?php _e( 'Scoring Presets', 'better-related' ) ?></h4>

					<p><?php
						_e( 'The presets are a very simply way of getting started. It is recommended to pick one of the small, medium and big options.', 'better-related' );
						echo ' ';
						_e( '<strong>Small</strong> is ideal for blogs with not too much traffic and less than 1000 posts.', 'better-related' ); ?>
					</p>

					<table class="form-table form-table-clearnone" > <?php
						$this->select(
							__( 'Scoring presets', 'better-related' ),
							'preset',
							array(
								'simple'	=> 'Quick and dirty',
								'small'		=> 'Small site',
								'medium'	=> 'Medium site',
								'big'		=> 'Big site',
							),
							__( 'TODO, description', 'better-related' )
						); ?>
					</table>

					<p class="submit">
						<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
					</p>
					 */ ?>

					<h4><?php _e( 'Scoring Options', 'better-related' ) ?></h4>

					<p><?php
						_e( 'You can choose a multiplier for each scoring method the plugin uses. Setting the multiplier to zero will disable the scoring method, and lead to less database queries.', 'better-related' ); ?>
					</p>

					<table class="form-table form-table-clearnone" > <?php
						$this->input(
							__( 'Minimum related score', 'better-related' ),
							'minscore',
							__( 'Minimum related necessary to be listed.', 'better-related' )
						);
						$this->checkbox(
							__( 'Find post types', 'better-related' ),
							array( 'usept', $post_types ),
							__( 'Post types to find as related.', 'better-related' )
						);
						$this->checkbox(
							__( 'Use taxonomies', 'better-related' ),
							array( 'usetax', $taxonomies )
						);
						$this->input(
							__( 'Content to content multiplier', 'better-related' ),
							'do_c2c'
						);
						$this->input(
							__( 'Title to title multiplier', 'better-related' ),
							'do_t2t'
						);
						$this->input(
							__( 'Title to content multiplier', 'better-related' ),
							'do_t2c'
						);
						$this->input(
							__( 'Keywords to content multiplier', 'better-related' ),
							'do_k2c'
						);
						$this->input(
							__( 'Keywords to title multiplier', 'better-related' ),
							'do_k2t'
						);
						$this->input(
							__( 'Term to taxonomy multiplier', 'better-related' ),
							'do_x2x'
						); ?>
					</table>

					<p class="submit">
						<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
					</p>

					<h4><?php _e( 'Performance Options', 'better-related' ) ?></h4>
					<table class="form-table form-table-clearnone" > <?php
						/*
						$this->select(
							__( 'Cache clearing behaviour', 'better-related' ),
							'clearcache',
							array(
								'never'		=> __( 'Never clear', 'better-related' ),
								'publish'	=> __( 'Clear all on publish', 'better-related' ),
								'related'	=> __( 'Clear most related posts only', 'better-related' )
							),
							__( 'If the relatedness scores should be deleted when posts are published. TODO', 'better-related' )
						);
						*/
						$this->input(
							__( 'Query limit', 'better-related' ),
							'querylimit'
						);
						$this->checkbox(
							__( 'Incremental scoring', 'better-related' ),
							'incremental'
						);
						$this->input(
							__( 'Total query limit', 'better-related' ),
							't_querylimit'
						); ?>
					</table>

					<p class="submit">
						<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
					</p>

					<h4><?php _e( 'Developer Options', 'better-related' ) ?></h4>
					<table class="form-table form-table-clearnone" > <?php
						$this->select(
							__( 'Data storage engine', 'better-related' ),
							'storage',
							array(
								'postmeta'	=> __( 'post meta', 'better-related' ),
								'transient'	=> __( 'transient', 'better-related' )
							),
							__( 'Which storage engine is used for caching. Transients should only be used to test various scoring configurations.', 'better-related' )
						);
						$this->input(
							__( 'Data storage ID', 'better-related' ),
							'storage_id',
							__( 'The prefix for the post meta or transient. I strongly recommend not to change this value while using post meta as storage.', 'better-related' )
						);
						$this->input(
							__( 'Transient expiration time', 'better-related' ),
							'cachetime',
							__( 'In seconds, only used if the relatedness scores are saved as transients.', 'better-related' )
						);
						$this->checkbox(
							__( 'Enable logging', 'better-related' ),
							'log'
						);
						$this->select(
							__( 'Which events to log', 'better-related' ),
							'loglevel',
							array(
								'' => '',
								'all'       => __( 'all', 'better-related' ),
								'filter'    => __( 'filter', 'better-related' ),
								'storage'   => __( 'storage', 'better-related' ),
								'global'    => __( 'global', 'better-related' ),
								'stopwords' => __( 'stopwords', 'better-related' ),
								'taxscore'  => __( 'taxscore', 'better-related' ),
								'query'     => __( 'db queries', 'better-related' ),
								'cscore'    => __( 'cscore', 'better-related' )
							)
						); ?>
						</table>

					<p class="submit">
						<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
					</p>
				</form>

			</div> <?php
			require_once( 'nkuttler.php' );
			nkuttler0_2_3_links(
				'better-related',
				'http://www.nkuttler.de/wordpress-plugin/wordpress-related-posts-plugin/'
			); ?>
		</div> <?php
	}

	/**
	 * Update content modification timestamp
	 *
	 * We need to update the timestamp every time some user-visible content
	 * is updated.
	 *
	 * @todo does this handle updates to published posts?
	 * @since 0.3.5
	 *
	 * @param int $id Post ID
	 * @return none
	 */
	public function timestamp_content( $id ) {
		$post = get_post( $id );
		if ( $post->post_status == 'publish' || $post->post_status == 'trash' ) {
			$this->log( "update mtime, post $id updated", 'storage' );
			$this->update_option( 'mtime', time() );
		}
	}

}
