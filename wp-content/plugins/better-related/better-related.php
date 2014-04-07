<?php
/*
    Copyright 2010 Nicolas Kuttler (email : wp@nicolaskuttler.de )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA

Plugin Name: Better Related Content
Author: Nicolas Kuttler
Author URI: http://www.nkuttler.de/
Plugin URI: http://www.nkuttler.de/wordpress-plugin/better-related-posts-and-custom-post-types/
Description: Better related posts plugin that finds custom post types and searches custom taxonomies
Version: 0.4.3.4
Text Domain: better-related
*/

/**
 * @package better-related
 * @subpackage pluginwrapper
 * @since 0.0.1
 */
if ( !class_exists( 'BetterRelated' ) ) {

	class BetterRelated {

		/**
		 * Array containing the options
		 *
		 * @since unknown
		 *
		 * @var array
		 */
		private $options;

		/**
		 * Path to the plugin
		 *
		 * @since 0.2.1
		 *
		 * @var string
		 */
		protected $plugin_dir;

		/**
		 * Path to the plugin file
		 *
		 * @since 0.2.3
		 *
		 * @var string
		 */
		protected $plugin_file;

		/**
		 * Plugin config version
		 *
		 * @since 0.2.5
		 *
		 * @var string
		 */
		private $version = '0.4.2';

		/**
		 * Constructor, set up the variables
		 *
		 * @since 0.0.1
		 *
		 * return none
		 */
		protected function __construct() {
			// Full path to main file
			$this->plugin_file= __FILE__;
			$this->plugin_dir = dirname( $this->plugin_file );
			$this->options = $this->get_saved_options();
		}

		/**
		 * Return a specific option value
		 *
		 * @since 0.1.1
		 *
		 * @param string $option name of option to return
		 * @return mixed
		 */
		protected function get_option( $option ) {
			if ( isset ( $this->options[$option] ) )
				return $this->options[$option];
			else
				return false;
		}

		/**
		 * Return the plugin options as stored in the DB, or an empty array
		 *
		 * @fixme don't fill empty strings with defaults! (?)
		 * @since 0.4.1
		 *
		 * @return array
		 */
		protected function get_saved_options() {
			if ( $options = get_option( 'better-related' ) )
				return $options;
			// If the option wasn't found, the plugin wasn't activated properly
			$this->create_fulltext_index( 'posts', 'post_content' );
			$this->create_fulltext_index( 'posts', 'post_title' );
			return $this->defaults();
		}

		/**
		 * Override the options retrived from the db for custom displays etc
		 *
		 * @since unknown
		 *
		 * @param array config options
		 * @return success
		 */
		protected function override_options( $options ) {
			$db_options	= $this->options;
			if ( !is_array( $db_options ) || !is_array( $options ) ) {
				trigger_error( 'Warning: override_options() did not receive a config array, or the stored config was invalid.' );
				return false;
			}
			$this->options = array_merge( $db_options, $options );
			return true;
		}

		/**
		 * Update a plugin option
		 *
		 * @since 0.3.5
		 *
		 * @return none
		 */
		protected function update_option( $option, $value ) {
			$this->options[$option] = $value;
			update_option( 'better-related', $this->options );
		}

		/**
		 * Return default plugin configuration
		 *
		 * @since 0.2.3
		 *
		 * @return array config
		 */
		protected function defaults() {
			$config = array(
				'version'        => $this->version,
				'autoshowpt'     => array(
					//'post'=> true
				),
				'usept'          => array(
					'post'   => true
				),
				'usetax'         => array(),
				'autoshowrss'    => false,
				'do_c2c'         => 1,
				'do_t2t'         => 0,
				'do_t2c'         => 0,
				'do_k2c'         => 0,
				'do_k2t'         => 0,
				'do_x2x'         => 1,
				'minscore'       => 50,
				'maxresults'     => 5,
				'cachetime'      => 60,
				'filterpriority' => 10,
				'log'            => false,
				'loglevel'       => false,
				'storage'        => 'postmeta',
				'storage_id'     => 'better-related-',
				'querylimit'     => 1000,
				't_querylimit'   => 10000,
				'mtime'          => time(),
				'relatedtitle'   => sprintf(
					"<strong>%s</strong>",
					__( 'Related content:', 'better-related' )
				),
				'relatednone'    => sprintf(
					"<p>%s</p>",
					__( 'No related content found.', 'better-related' )
				),
				'thanks'         => 'none',
				'stylesheet'     => true,
				'showdetails'    => false
			);
			return $config;
		}

		/**
		 * Create a fulltext index on a column
		 *
		 * @since 0.3.2
		 * @todo first query always throws a WP_DEBUG error
		 *
		 * @return none
		 */
		protected function create_fulltext_index( $table, $column ) {
			if ( $this->fulltext_index_exists( $table, $column ) )
				return;
			global $wpdb;
			$query = "CREATE FULLTEXT INDEX {$column}_index ON {$wpdb->prefix}{$table} ($column);";
			$r = $wpdb->query( $wpdb->prepare( $query ) );
		}

		/**
		 * Check if a fulltext index for a column exists
		 *
		 * @since 0.3.2
		 *
		 * @return bool full text index exists
		 */
		protected function fulltext_index_exists( $table, $column ) {
			global $wpdb;
			$query = "SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_NAME='{$wpdb->prefix}posts' AND COLUMN_NAME='$column' AND INDEX_TYPE='FULLTEXT';";
			$indexes = $wpdb->get_var( $wpdb->prepare( $query ) );
			if ( intval( $indexes ) > 1 ) {
				trigger_error( sprintf( __( 'Warning: Multiple fulltext indexes found for %s', 'better-related' ), "{$wpdb->prefix}$table.$column" ) );
				return true;
			}
			elseif ( $indexes === '0' )
				return false;
			elseif ( $indexes === '1' )
				return true;
			trigger_error( sprintf( __( 'Warning: Unknown mysql result from %s', 'better-related' ), $query ) );
			return false;
		}

		/**
		 * Deactivate this plugin and die with an error message
		 *
		 * @since 0.2.8
		 *
		 * @param string error
		 * @return none
		 */
		protected function deactivate_and_die( $error = false ) {
			#load_plugin_textdomain(
			#	'better-related',
			#	false,
			#	basename( $this->plugin_dir ) . '/translations'
			#);
			$message = sprintf( __( "Better Related has been automatically deactivated because of the following error: <strong>%s</strong>." ), $error );
			if ( !function_exists( 'deactivate_plugins' ) )
				include ( ABSPATH . 'wp-admin/includes/plugin.php' );
			deactivate_plugins( __FILE__ );
			wp_die( $message );
		}

		/**
		 * Log helper, exposes the log to the web by default
		 *
		 * @since 0.0.1
		 *
		 * @param string $msg message
		 * @param string $level loglevel
		 * @return none
		 */
		protected function log( $msg, $level = 0 ) {
			if ( !$this->get_option( 'log' ) )
				return;
			$msg  = date( 'H:i:s' ) . ": $msg";
			$msg .= "\n";
			$log  = $this->plugin_dir . '/log.txt';
			$fh   = fopen( $log, 'a' );
			if ( !$fh ) {
				trigger_error( sprintf(
					__( "Logfile %s is not writable..", 'better-related' ),
					$log
				) );
				return;
			}
			if ( !$this->get_option( 'log' ) )
				return;
			$loglevel = $this->get_option( 'loglevel' );
			if ( $loglevel != 'all' && $loglevel != $level && $level != 'global' )
				return;
			fwrite( $fh, $msg );
			fclose( $fh );
		}

	}

	/**
	 * Instantiate the appropriate classes
	 */
	$missing = 'Core plugin files are missing, please reinstall the plugin';
	if ( is_admin() ) {
		if ( @include( 'inc/admin.php' ) )
			$BetterRelatedAdmin = new BetterRelatedAdmin;
		else
			BetterRelated::deactivate_and_die( $missing );
	}
	else {
		if ( @include( 'inc/frontend.php' ) ) {
			global $BetterRelatedFrontend;
			$BetterRelatedFrontend = new BetterRelatedFrontend;
		}
		else
			BetterRelated::deactivate_and_die( $missing );
	}

}
