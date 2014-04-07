<?php
/**
 * Plugin Name: BackWPup
 * Plugin URI: https://marketpress.com/product/backwpup-pro/
 * Description: WordPress Backup and more...
 * Author: Inpsyde GmbH
 * Author URI: http://inpsyde.com
 * Version: 3.0.13
 * Text Domain: backwpup
 * Domain Path: /languages/
 * Network: true
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0
 * Slug: backwpup
 */

/**
 *	Copyright (C) 2012-2013 Inpsyde GmbH (email: info@inpsyde.com)
 *
 *	This program is free software; you can redistribute it and/or
 *	modify it under the terms of the GNU General Public License
 *	as published by the Free Software Foundation; either version 2
 *	of the License, or (at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with this program; if not, write to the Free Software
 *	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

if ( ! class_exists( 'BackWPup' ) ) {

	// Don't activate on anything less than PHP 5.2.4 or WordPress 3.1
	if ( version_compare( PHP_VERSION, '5.2.6', '<' ) || version_compare( get_bloginfo( 'version' ), '3.2', '<' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		deactivate_plugins( basename( __FILE__ ) );
		if ( isset( $_GET['action'] ) && ( $_GET['action'] == 'activate' || $_GET['action'] == 'error_scrape' ) )
			die( __( 'BackWPup requires PHP version 5.2.6 or greater and WordPress 3.2 or greater.', 'backwpup' ) );
	}

	//Start Plugin
	if ( function_exists( 'add_filter' ) )
		add_action( 'plugins_loaded', array( 'BackWPup', 'getInstance' ), 11 );

	/**
	 * Main BackWPup Plugin Class
	 */
	final class BackWPup {

		private static $instance = NULL;
		private static $plugin_data = array();
		private static $destinations = array();
		private static $job_types = array();
		private static $wizards = array();

		/**
		 * Set needed filters and actions and load
		 */
		private function __construct() {

			// Nothing else matters if we're on WP-MS and not on the main site
			if ( is_multisite() && ! is_main_site() )
				return;
			//auto loader
			if ( function_exists( 'spl_autoload_register' ) ) //register auto load
				spl_autoload_register( array( $this, 'autoloader' ) );
			else //auto loader fallback
				$this->autoloader_fallback();
			//start upgrade if needed
			if ( get_site_option( 'backwpup_version' ) != self::get_plugin_data( 'Version' ) && class_exists( 'BackWPup_Install' ) )
				BackWPup_Install::activate();
			//load pro features
			if ( is_file( dirname( __FILE__ ) . '/inc/features/class-features.php' ) )
				require dirname( __FILE__ ) . '/inc/features/class-features.php';						
			//WP-Cron
			if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
				// add normal cron actions
				add_action( 'backwpup_cron', array( 'BackWPup_Cron', 'run' ) );
				add_action( 'backwpup_check_cleanup', array( 'BackWPup_Cron', 'check_cleanup' ) );
				// add action for doing thinks if cron active
				// must done in int before wp-cron control
				add_action( 'init', array( 'BackWPup_Cron', 'cron_active' ), 1 ); 
				// if in cron the rest must not needed
				return;
			}
			//deactivation hook
			register_deactivation_hook( __FILE__, array( 'BackWPup_Install', 'deactivate' ) );
			//Things that must do in plugin init
			add_action( 'init', array( $this, 'plugin_init' ) );
			//only in backend
			if ( is_admin() && class_exists( 'BackWPup_Admin' ) )
				BackWPup_Admin::getInstance();
			//work with wp-cli
			if ( defined( 'WP_CLI' ) && WP_CLI ) {
				require dirname( __FILE__ ) . '/inc/class-wp-cli.php';
				WP_CLI::addCommand( 'backwpup', 'BackWPup_WP_CLI' );
			}
		}

		/**
		 * @static
		 *
		 * @return self
		 */
		public static function getInstance() {

			if (NULL === self::$instance) {
				self::$instance = new self;
			}
			return self::$instance;
		}


		private function __clone() {}

		/**
		 * get information about the Plugin
		 *
		 * @param string $name Name of info to get or NULL to get all
		 * @return string|array
		 */
		public static function get_plugin_data( $name = NULL ) {

			if ( $name )
				$name = strtolower( $name );

			if ( empty( self::$plugin_data ) ) {
				self::$plugin_data = get_file_data( __FILE__, array(
																   'name'        => 'Plugin Name',
																   'pluginuri'   => 'Plugin URI',
																   'version'     => 'Version',
																   'description' => 'Description',
																   'author'      => 'Author',
																   'authoruri'   => 'Author URI',
																   'textdomain'  => 'Text Domain',
																   'domainpath'  => 'Domain Path',
																   'slug'  		 => 'Slug',
																   'license'     => 'License',
																   'licenseuri'  => 'License URI'
															  ), 'plugin' );
				//Translate some vars
				self::$plugin_data[ 'name' ]        = trim( self::$plugin_data[ 'name' ] );
				self::$plugin_data[ 'pluginuri' ]   = trim( self::$plugin_data[ 'pluginuri' ] );
				self::$plugin_data[ 'description' ] = trim( self::$plugin_data[ 'description' ] );
				self::$plugin_data[ 'author' ]      = trim( self::$plugin_data[ 'author' ] );
				self::$plugin_data[ 'authoruri' ]   = trim( self::$plugin_data[ 'authoruri' ] );
				//set some extra vars
				self::$plugin_data[ 'basename' ] = plugin_basename( dirname( __FILE__ ) );
				self::$plugin_data[ 'mainfile' ] = __FILE__ ;
				self::$plugin_data[ 'plugindir' ] = untrailingslashit( dirname( __FILE__ ) ) ;
				if ( defined( 'WP_TEMP_DIR' ) && is_dir( WP_TEMP_DIR ) ) {
					self::$plugin_data[ 'temp' ] = trailingslashit( str_replace( '\\', '/', WP_TEMP_DIR ) ) . 'backwpup-' . substr( md5( md5( SECURE_AUTH_KEY ) ), - 5 ) . '/';
				} else {
					$upload_dir = wp_upload_dir();
					self::$plugin_data[ 'temp' ] = trailingslashit( str_replace( '\\', '/',$upload_dir[ 'basedir' ] ) ) . 'backwpup-' . substr( md5( md5( SECURE_AUTH_KEY ) ), - 5 ) . '-temp/';
				}
				self::$plugin_data[ 'running_file' ] = self::$plugin_data[ 'temp' ] . 'backwpup-' . substr( md5( NONCE_SALT ), 13, 6 ) . '-working.php';
				self::$plugin_data[ 'url' ] = plugins_url( '', __FILE__ );
				//get unmodified WP Versions
				include ABSPATH . WPINC . '/version.php';
				/** @var $wp_version string */
				self::$plugin_data[ 'wp_version' ] = $wp_version;
				//Build User Agent
				self::$plugin_data[ 'user-agent' ] = self::$plugin_data[ 'name' ].'/' . self::$plugin_data[ 'version' ] . '; WordPress/' . self::$plugin_data[ 'wp_version' ] . '; ' . home_url();
			}

			if ( ! empty( $name ) )
				return self::$plugin_data[ $name ];
			else
				return self::$plugin_data;
		}


		/**
		 * include not existing classes automatically
		 *
		 * @param string $class_name Class to load from file
		 */
		private function autoloader( $class_name ) {

			//BackWPup classes to load
			if ( strstr( $class_name, 'BackWPup_' ) ) {
				$class_file_name =  DIRECTORY_SEPARATOR . 'class-' . strtolower( str_replace( array( 'BackWPup_', '_' ), array( '', '-' ), $class_name ) ) . '.php';
				if ( class_exists( 'BackWPup_Features', FALSE ) && is_file( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'features' . $class_file_name ) )
					require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'features' . $class_file_name;
				elseif ( is_file( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'inc' . $class_file_name ) )
					require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'inc' . $class_file_name;
			}
		}

		/**
		 * load all classes if spl auto loader not exists
		 */
		private function autoloader_fallback() {

			//add class files that should excluded that are classes that extra loaded with require or needs spl_autoload_register to work
			$loaded_class_files = array( 'class-auto-update.php', 'class-documentation.php', 'class-features.php',
										 'class-destination-msazure.php', 'class-destination-msazure-pro.php',
										 'class-destination-email.php', 'class-destination-email-pro.php',
										 'class-destination-rsc.php', 'class-destination-rsc-pro.php',
										 'class-destination-s3.php', 'class-destination-s3-pro.php',
										 'class-destination-s3-v1.php', 'class-destination-s3-v1-pro.php',
										 'class-destinations.php', 'class-jobtypes.php', 'class-wizards.php',
										 'class-wp-cli.php' );

			//load first abstraction classes
			require dirname( __FILE__ ) . '/inc/class-destinations.php';
			require dirname( __FILE__ ) . '/inc/class-jobtypes.php';
			if ( is_file( dirname( __FILE__ ) . '/inc/features/class-features.php' ) )
				require  dirname( __FILE__ ) . '/inc/features/class-wizards.php';
			require_once ABSPATH . '/wp-admin/includes/class-wp-list-table.php';

			//load normal classes
			foreach( glob( dirname( __FILE__ ) . '/inc/class-*.php' ) as $class ) {
				if ( ! in_array( basename( $class ), $loaded_class_files ) )
					require $class;
			}

			//load features
			if ( is_file( dirname( __FILE__ ) . '/inc/features/class-features.php' ) ) {
				foreach( glob( dirname( __FILE__ ) . '/inc/features/class-*.php' ) as $class ) {
					if ( ! in_array( basename( $class ), $loaded_class_files ) )
						require $class;
				}
			}
		}

		/**
		 * Plugin init function
		 *
		 * @return void
		 */
		public function plugin_init() {

			//Add Admin Bar
			if ( ! defined( 'DOING_CRON' ) && current_user_can( 'backwpup' ) && current_user_can( 'backwpup' ) && is_admin_bar_showing() && get_site_option( 'backwpup_cfg_showadminbar', FALSE ) )
				BackWPup_Adminbar::getInstance();
			
		}

		/**
		 * Get a array of instances for Backup Destination's
		 *
		 * @return array BackWPup_Destinations
		 */
		public static function get_destinations() {

			if ( ! empty( self::$destinations ) )
				return self::$destinations;

			//add BackWPup Destinations
			self::$destinations[ 'FOLDER' ] 		= new BackWPup_Destination_Folder;
			if ( class_exists( 'BackWPup_Destination_Email' ) )
				self::$destinations[ 'EMAIL' ]   	= new BackWPup_Destination_Email;
			if ( function_exists( 'ftp_login' ) )
				self::$destinations[ 'FTP' ] 		= new BackWPup_Destination_Ftp;
			if ( function_exists( 'curl_exec' ) )
				self::$destinations[ 'DROPBOX' ] 	= new BackWPup_Destination_Dropbox;
			if (  function_exists( 'curl_exec' ) && version_compare( PHP_VERSION, '5.3.3', '>=' ) && class_exists( 'BackWPup_Destination_S3' ) )
				self::$destinations[ 'S3' ] 		= new BackWPup_Destination_S3;
			elseif (  function_exists( 'curl_exec' ) && class_exists( 'BackWPup_Destination_S3_V1' ) )
				self::$destinations[ 'S3' ] 		= new BackWPup_Destination_S3_V1;
			if ( version_compare( PHP_VERSION, '5.3.2', '>=' ) && class_exists( 'BackWPup_Destination_MSAzure' )  )
				self::$destinations[ 'MSAZURE' ] 	= new BackWPup_Destination_MSAzure;
			if ( function_exists( 'curl_exec' ) && version_compare( PHP_VERSION, '5.3.0', '>=' ) && class_exists( 'BackWPup_Destination_RSC' ) )
				self::$destinations[ 'RSC' ] 		= new BackWPup_Destination_RSC;
			if ( function_exists( 'curl_exec' ) )
				self::$destinations[ 'SUGARSYNC' ]	= new BackWPup_Destination_SugarSync;

			self::$destinations = apply_filters( 'backwpup_destinations', self::$destinations );

			//remove destinations can't load
			foreach ( self::$destinations as $key => $destination ) {
				if ( empty( $destination ) || ! is_object( $destination ) )
					unset( self::$destinations[ $key ] );
			}

			return self::$destinations;
		}


		/**
		 * Gets a array of instances from Job types
		 *
		 * @return array BackWPup_JobTypes
		 */
		public static function get_job_types() {

			if ( !empty( self::$job_types ) )
				return self::$job_types;

			self::$job_types[ 'DBDUMP' ]= new BackWPup_JobType_DBDump;
			self::$job_types[ 'FILE' ] 		= new BackWPup_JobType_File;
			self::$job_types[ 'WPEXP' ] 	= new BackWPup_JobType_WPEXP;
			self::$job_types[ 'WPPLUGIN' ]  = new BackWPup_JobType_WPPlugin;
			self::$job_types[ 'DBOPTIMIZE' ]= new BackWPup_JobType_DBOptimize;
			self::$job_types[ 'DBCHECK' ]   = new BackWPup_JobType_DBCheck;

			self::$job_types = apply_filters( 'backwpup_job_types', self::$job_types );

			//remove types can't load
			foreach ( self::$job_types as $key => $job_type ) {
				if ( empty( $job_type ) || ! is_object( $job_type ) )
					unset( self::$job_types[ $key ] );
			}

			return self::$job_types;
		}



		/**
		 * Gets a array of instances from Wizards Pro version Only
		 *
		 * @return array BackWPup_Wizards
		 */
		public static function get_wizards() {

			if ( !empty( self::$wizards ) )
				return self::$wizards;

			self::$wizards  = apply_filters( 'backwpup_wizards', self::$wizards );

			//remove wizards can't load
			foreach ( self::$wizards as $key => $wizard ) {
				if ( empty( $wizard ) || ! is_object( $wizard ) )
					unset( self::$wizards[ $key ] );
			}

			return self::$wizards;

		}

	}

}
