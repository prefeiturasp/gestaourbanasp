<?php
/**
 * Class For BackWPup Jobs page
 */
class BackWPup_Page_Jobs extends WP_List_Table {

	private static $listtable = NULL;
	static $logfile = NULL;


	/**
	 *
	 */
	function __construct() {
		parent::__construct( array(
								  'plural'   => 'jobs',
								  'singular' => 'job',
								  'ajax'     => TRUE
							 ) );
	}


	/**
	 * @return bool|void
	 */
	function ajax_user_can() {

		return current_user_can( 'backwpup' );
	}

	/**
	 *
	 */
	function prepare_items() {

		$this->items = BackWPup_Option::get_job_ids();
	}

	/**
	 *
	 */
	function no_items() {

		_e( 'No Jobs.', 'backwpup' );
	}

	/**
	 * @return array
	 */
	function get_bulk_actions() {

		if ( ! $this->has_items() )
			return array ();

		$actions             = array();
		$actions[ 'delete' ] = __( 'Delete', 'backwpup' );

		return apply_filters( 'backwpup_page_jobs_get_bulk_actions', $actions );
	}

	/**
	 * @return array
	 */
	function get_columns() {

		$jobs_columns              = array();
		$jobs_columns[ 'cb' ]      = '<input type="checkbox" />';
		$jobs_columns[ 'id' ]      = __( 'ID', 'backwpup' );
		$jobs_columns[ 'jobname' ] = __( 'Job Name', 'backwpup' );
		$jobs_columns[ 'type' ]    = __( 'Type', 'backwpup' );
		$jobs_columns[ 'dest' ]    = __( 'Destinations', 'backwpup' );
		$jobs_columns[ 'next' ]    = __( 'Next Run', 'backwpup' );
		$jobs_columns[ 'last' ]    = __( 'Last Run', 'backwpup' );

		return $jobs_columns;
	}

	/**
	 *
	 */
	function display_rows() {

		//check for running job
		$job_object = BackWPup_Job::get_working_data();
		$style      = '';
		foreach ( $this->items as $jobid ) {
			$style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';
			echo PHP_EOL . "\t", $this->single_row( $jobid, $job_object, $style );
		}
	}

	/**
	 * @param int 	 $jobid
	 * @param        $job_object BackWPup_Job
	 * @param string $style
	 * @return string
	 */
	function single_row( $jobid, $job_object, $style = '' ) {

		$job_types    = BackWPup::get_job_types();
		$destinations = BackWPup::get_destinations();
		list( $columns, $hidden, $sortable ) = $this->get_column_info();
		$r = "<tr id=\"jodid-" . $jobid . "\"" . $style . ">";
		foreach ( $columns as $column_name => $column_display_name ) {
			$class = "class=\"$column_name column-$column_name\"";

			$style = '';
			if ( in_array( $column_name, $hidden ) )
				$style = ' style="display:none;"';

			$attributes = "$class$style";

			$job_normal_hide ='';
			if ( is_object( $job_object ) )
				$job_normal_hide = ' style="display:none;"';

			switch ( $column_name ) {
				case 'cb':
					$r .= '<th scope="row" class="check-column"><input type="checkbox" name="jobs[]" value="' . esc_attr( $jobid ) . '" /></th>';
					break;
				case 'id':
					$r .= "<td $attributes>" . $jobid . "</td>";
					break;
				case 'jobname':
					$r .= "<td $attributes><strong>" . esc_html( BackWPup_Option::get( $jobid, 'name' ) ) . "</strong>";
					$actions = array();
					if ( current_user_can( 'backwpup_jobs_edit' ) ) {
						$actions[ 'edit' ]     = "<a href=\"" . wp_nonce_url( network_admin_url( 'admin.php' ) . '?page=backwpupeditjob&jobid=' . $jobid, 'edit-job' ) . "\">" . __( 'Edit', 'backwpup' ) . "</a>";
						$actions[ 'copy' ]     = "<a href=\"" . wp_nonce_url( network_admin_url( 'admin.php' ) . '?page=backwpupjobs&action=copy&jobid=' . $jobid, 'copy-job_' . $jobid ) . "\">" . __( 'Copy', 'backwpup' ) . "</a>";
						$actions[ 'delete' ]   = "<a class=\"submitdelete\" href=\"" . wp_nonce_url( network_admin_url( 'admin.php' ) . '?page=backwpupjobs&action=delete&jobs[]=' . $jobid, 'bulk-jobs' ) . "\" onclick=\"return showNotice.warn();\">" . __( 'Delete', 'backwpup' ) . "</a>";
					}
					if ( current_user_can( 'backwpup_jobs_start' ) ) {
						$url                   = BackWPup_Job::get_jobrun_url( 'runnowlink', $jobid );
						$actions[ 'runnow' ]   = "<a href=\"" . $url[ 'url' ] . "\">" . __( 'Run now', 'backwpup' ) . "</a>";
					}
					$actions = apply_filters( 'backwpup_page_jobs_actions', $actions, $jobid, FALSE );
					$r .= '<div class="job-normal"' . $job_normal_hide . '>' . $this->row_actions( $actions ) . '</div>';
					if ( is_object( $job_object ) ) {
						$actionsrun = array();
						$actionsrun = apply_filters( 'backwpup_page_jobs_actions', $actionsrun, $jobid, TRUE );
						$r .= '<div class="job-run">' . $this->row_actions( $actionsrun ) . '</div>';
					}

					$r .= '</td>';
					break;
				case 'type':
					$r .= "<td $attributes>";
					if ( $types = BackWPup_Option::get( $jobid, 'type' ) ) {
						foreach ( $types as $type ) {
							if ( isset( $job_types[ $type ] ) ) {
								$r .= $job_types[ $type ]->info[ 'name' ] . '<br />';
							}
							else {
								$r .= $type . '<br />';
							}
						}
					}
					$r .= "</td>";
					break;
				case 'dest':
					$r .= "<td $attributes>";
					$backup_to = FALSE;
					foreach ( BackWPup_Option::get( $jobid, 'type' ) as $typeid ) {
						if ( isset( $job_types[ $typeid ] ) && $job_types[ $typeid ]->creates_file() ) {
							$backup_to = TRUE;
							break;
						}
					}
					if ( $backup_to ) {
						$job_object_can_run = new stdClass;
						$job_object_can_run->job = BackWPup_Option::get_job( $jobid );
						foreach ( BackWPup_Option::get( $jobid, 'destinations' ) as $destid ) {
							if ( isset( $destinations[ $destid ] ) && $destinations[ $destid ]->can_run( $job_object_can_run ) ) {
								$r .= $destinations[ $destid ]->info[ 'name' ] . '<br />';
							} else {
								$r .= $destid . '<br />';
							}
						}
					}
					else {
						$r .= '<i>' . __( 'Not needed or set', 'backwpup' ) . '</i><br />';
					}
					$r .= "</td>";
					break;
				case 'next':
					$r .= "<td $attributes>";
					if ( is_object( $job_object ) && $job_object->job[ 'jobid' ] == $jobid ) {
						$runtime = current_time( 'timestamp' ) - $job_object->start_time;
						$r .= '<div class="job-run">' . sprintf( __( 'Running for: %s seconds', 'backwpup' ), '<span id="runtime">' . $runtime . '</span>' ) .'</div>';
					}
					if ( is_object( $job_object ) && $job_object->job[ 'jobid' ] == $jobid )
						$r .='<div class="job-normal"' . $job_normal_hide . '>';
					if ( BackWPup_Option::get( $jobid, 'activetype' ) == 'wpcron' ) {
						if ( $nextrun = wp_next_scheduled( 'backwpup_cron', array( 'id' => $jobid ) ) + ( get_option( 'gmt_offset' ) * 3600 )  )
							$r .= '<span title="' . sprintf( __( 'Cron: %s','backwpup'),BackWPup_Option::get( $jobid, 'cron' ) ). '">' . sprintf( __( '%1$s at %2$s by WP-Cron', 'backwpup' ) , date_i18n( get_option( 'date_format' ), $nextrun, TRUE ) , date_i18n( get_option( 'time_format' ), $nextrun, TRUE ) ) . '</span>';
						else
							$r .= __( 'Not scheduled!', 'backwpup' );
					}
					else {
						$r .= __( 'Inactive', 'backwpup' );
					}
					if ( is_object( $job_object ) && $job_object->job[ 'jobid' ] == $jobid )
						$r .= '</div>';
					$r .= "</td>";
					break;
				case 'last':
					$r .= "<td $attributes>";
					if ( BackWPup_Option::get( $jobid, 'lastrun' ) ) {
						$lastrun = BackWPup_Option::get( $jobid, 'lastrun' );
						$r .= sprintf( __( '%1$s at %2$s', 'backwpup' ), date_i18n( get_option( 'date_format' ), $lastrun, TRUE ), date_i18n( get_option( 'time_format' ), $lastrun, TRUE ) );
						if ( BackWPup_Option::get( $jobid, 'lastruntime' ) )
							$r .= '<br />' . sprintf( __( 'Runtime: %d seconds', 'backwpup' ), BackWPup_Option::get( $jobid, 'lastruntime' ) );
					}
					else {
						$r .= __( 'not yet', 'backwpup' );
					}
					$r .= "<br />";
					if ( current_user_can( 'backwpup_backups_download' ) && BackWPup_Option::get( $jobid, 'lastbackupdownloadurl' ) )
						$r .= "<a href=\"" . wp_nonce_url( BackWPup_Option::get( $jobid, 'lastbackupdownloadurl' ), 'download-backup' ) . "\" title=\"" . esc_attr( __( 'Download last backup', 'backwpup' ) ) . "\">" . __( 'Download', 'backwpup' ) . "</a> | ";
					if ( current_user_can( 'backwpup_logs' ) && BackWPup_Option::get( $jobid, 'logfile' ) ) {
						$logfile = basename( BackWPup_Option::get( $jobid, 'logfile' ) );
						if ( is_object( $job_object ) && $job_object->job[ 'jobid'] == $jobid )
							$logfile = basename( $job_object->logfile );
						$r .= '<a class="thickbox" href="' . admin_url( 'admin-ajax.php' ) . '?&action=backwpup_view_log&logfile=' . $logfile .'&_ajax_nonce=' . wp_create_nonce( 'view-logs' ) . '&height=440&width=630&TB_iframe=true" title="' . esc_attr( $logfile ) . '">' . __( 'Log', 'backwpup' ) . '</a>';

					}
					$r .= "</td>";
					break;
			}
		}
		$r .= '</tr>';

		return $r;
	}


	/**
	 *
	 */
	public static function load() {

		//Create Table
		self::$listtable = new self;

		switch ( self::$listtable->current_action() ) {
			case 'delete': //Delete Job
				if ( ! current_user_can( 'backwpup_jobs_edit' ) )
					break;
				if ( is_array( $_GET[ 'jobs' ] ) ) {
					check_admin_referer( 'bulk-jobs' );
					foreach ( $_GET[ 'jobs' ] as $jobid ) {
						wp_clear_scheduled_hook( 'backwpup_cron', array( 'id' => $jobid ) );
						BackWPup_Option::delete_job( $jobid );
					}
				}
				break;
			case 'copy': //Copy Job
				if ( ! current_user_can( 'backwpup_jobs_edit' ) )
					break;
				$old_job_id = (int)$_GET[ 'jobid' ];
				check_admin_referer( 'copy-job_' . $_GET[ 'jobid' ] );
				//create new
				$newjobid = BackWPup_Option::get_job_ids();
				sort( $newjobid );
				$newjobid    = end( $newjobid ) + 1;
				$old_options = BackWPup_Option::get_job( $old_job_id );
				foreach ( $old_options as $key => $option ) {
					if ( $key == "jobid" )
						$option = $newjobid;
					if ( $key == "name" )
						$option = __( 'Copy of', 'backwpup' ) . ' ' . $option;
					if ( $key == "activetype" )
						$option = '';
					if ( $key == "archivename" )
						$option = str_replace( $_GET[ 'jobid' ], $newjobid, $option );
					if ( $key == "logfile" || $key == "lastbackupdownloadurl" || $key == "lastruntime" ||
						$key == "lastrun" )
						continue;
					BackWPup_Option::update( $newjobid, $key, $option );
				}
				break;
			case 'start_cli': //Get cmd start file
				if ( ! current_user_can( 'backwpup_jobs_start' ) )
					break;
				check_admin_referer( 'start_cli' );
				if ( empty( $_GET[ 'jobid' ] ) )
					break;
				if ( FALSE === strpos( PHP_OS, "WIN" ) ) {
					header( "Pragma: public" );
					header( "Expires: 0" );
					header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
					header( "Content-Type: application/octet-stream" );
					header( "Content-Disposition: attachment; filename=BackWPup_cmd_start_job_" . $_GET[ 'jobid' ] . ".sh;" );
					if ( defined( 'PHP_BINDIR' ) )
						echo "#!/bin/sh" . PHP_EOL;
					echo "@\$1php -c \"" . php_ini_loaded_file() . "\" -r \"define( 'DOING_CRON', TRUE ); require '" . ABSPATH . "wp-load.php'; if( class_exists( 'BackWPup_Job' ) ) BackWPup_Job::start_cli( " . $_GET[ 'jobid' ] . " );\"";
					die();
				}
				else {
					header( "Pragma: public" );
					header( "Expires: 0" );
					header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
					header( "Content-Type: application/octet-stream" );
					header( "Content-Disposition: attachment; filename=BackWPup_cmd_start_job_" . $_GET[ 'jobid' ] . ".cmd;" );
					echo "@%1php.exe -c \"" . php_ini_loaded_file() . "\" -r \"define( 'DOING_CRON', TRUE ); require '" . ABSPATH . "wp-load.php'; if( class_exists( 'BackWPup_Job' ) ) BackWPup_Job::start_cli( " . $_GET[ 'jobid' ] . " );\"";
					die();
				}
				break;
			case 'runnow':
				if ( ! empty( $_GET[ 'jobid' ] ) ) {
					if ( ! current_user_can( 'backwpup_jobs_start' ) )
						wp_die( __( 'Sorry, you don\'t have permissions to do that.', 'backwpup') );
					check_admin_referer( 'backwup_job_run-runnowlink' );
					
					//check temp folder
					BackWPup_Job::check_folder( BackWPup::get_plugin_data( 'TEMP' ) );
					if ( is_dir( BackWPup::get_plugin_data( 'TEMP' ) ) ) {
						if ( ! is_writable( BackWPup::get_plugin_data( 'TEMP' ) ) || ! is_readable( BackWPup::get_plugin_data( 'TEMP' ) ) ) {
							BackWPup_Admin::message( sprintf( __( 'Temp folder %s not read or writable. Please set proper write permissions.','backwpup' ), BackWPup::get_plugin_data( 'TEMP' ) ) );
						}
					} else {
						BackWPup_Admin::message( sprintf( __( 'Temp folder %s does not exist and cannot be created. Please create it and set proper write permissions.','backwpup' ), BackWPup::get_plugin_data( 'TEMP' ) ) );
					}
					//check log folder
					BackWPup_Job::check_folder( BackWPup_Option::get( 'cfg', 'logfolder' ) );
					if ( is_dir( BackWPup_Option::get( 'cfg', 'logfolder' ) ) ) {
						if ( ! is_writable( BackWPup_Option::get( 'cfg', 'logfolder' ) )  || ! is_readable( BackWPup_Option::get( 'cfg', 'logfolder' ) )  ) {
							BackWPup_Admin::message( sprintf( __( 'Logs folder %s is not read or writable. Please set proper write permissions.','backwpup' ), BackWPup_Option::get( 'cfg', 'logfolder' ) ) );
						}
					} else {
						BackWPup_Admin::message( sprintf( __( 'Logs folder %s does not exist and cannot be created. Please create it and set proper write permissions.','backwpup' ), BackWPup_Option::get( 'cfg', 'logfolder' ) ) );
					}
					//check backups folder
					$backups_folder = BackWPup_Option::get( $_GET[ 'jobid' ], 'backupdir' );
					if ( ! empty( $backups_folder ) ) {
						BackWPup_Job::check_folder( $backups_folder );
						if ( is_dir( $backups_folder ) ) {
							if ( ! is_writable( $backups_folder )  || ! is_readable( $backups_folder )  ) {
								BackWPup_Admin::message( sprintf( __( 'Backups folder %s is not read or writable. Please set proper write permissions.','backwpup' ), $backups_folder ) );
							}
						} else {
							BackWPup_Admin::message( sprintf( __( 'Backups folder %s does not exist and cannot be created. Please create it and set proper write permissions.','backwpup' ), $backups_folder ) );
						}
					}
					//check sever callback
					$wp_admin_user = get_users( array( 'role' => 'administrator' ) );
					$raw_response = wp_remote_get( site_url( 'wp-cron.php?backwpup_run=test' ), array(
																									   'blocking'   => TRUE,
																									   'sslverify'  => FALSE,
																									   'timeout' 	=> 15,
																									   'redirection' => 0,
																									   'headers'    => array( 'Authorization' => 'Basic ' . base64_encode( BackWPup_Option::get( 'cfg', 'httpauthuser' ) . ':' . BackWPup_Encryption::decrypt( BackWPup_Option::get( 'cfg', 'httpauthpassword' ) ) ), 'Cookie' => LOGGED_IN_COOKIE. '='. wp_generate_auth_cookie( $wp_admin_user[ 0 ]->ID, time() + 60, 'logged_in') ),
																									   'user-agent' => BackWPup::get_plugin_data( 'user-agent' ) ) );
					$test_result = '';
					if ( is_wp_error( $raw_response ) )
						$test_result .= sprintf( __( 'The HTTP response test get a error "%s"','backwpup' ), $raw_response->get_error_message() );
					if ( 200 != wp_remote_retrieve_response_code( $raw_response ) )
						$test_result .= sprintf( __( 'The HTTP response test get a false http status (%s)','backwpup' ), wp_remote_retrieve_response_code( $raw_response ) );	
					if ( ! empty( $test_result ) )
						BackWPup_Admin::message( $test_result );

					//only start job if messages empty
					$log_messages = BackWPup_Admin::get_message();					
					if ( empty ( $log_messages ) )  {
						BackWPup_Admin::message( sprintf( __( 'Job "%s" started.', 'backwpup' ), esc_attr( BackWPup_Option::get( $_GET[ 'jobid' ], 'name' ) ) ) );
						BackWPup_Job::get_jobrun_url( 'runnow', $_GET[ 'jobid' ] );
						usleep( 250000 ); //wait a quarter second
						//sleep as long as job not started
						$i=0;
						$job_object = BackWPup_Job::get_working_data( TRUE );
						while ( empty( $job_object->logfile ) ) {
							usleep( 250000 ); //wait a quarter second for net try
							clearstatcache();
							$job_object = BackWPup_Job::get_working_data( TRUE );
							//wait maximal 10 sec.
							if ( $i >= 40 )
								break;
							$i++;
						}
						if ( ! empty( $job_object->logfile ) ) 
							self::$logfile = $job_object->logfile;
						else
							self::$logfile = BackWPup_Option::get( $_GET[ 'jobid' ], 'logfile', NULL, FALSE );
					}
				}
				break;
			case 'abort': //Abort Job
				if ( ! current_user_can( 'backwpup_jobs_start' ) )
					break;
				check_admin_referer( 'abort-job' );
				$job_object = BackWPup_Job::get_working_data();
				if ( ! $job_object )
					break;
				delete_site_option( 'backwpup_working_job' );
				unlink( BackWPup::get_plugin_data( 'running_file' ) );
				if ( ! is_object( $job_object ) )
					break;
				//remove restart cron
				wp_clear_scheduled_hook( 'backwpup_cron', array( 'id' => 'restart' ) );
				//add log entry
				$timestamp = "<span title=\"[Type: " . E_USER_ERROR . "|Line: " . __LINE__ . "|File: " . basename( __FILE__ ) . "|PID: " . $job_object->pid . "]\">[" . date_i18n( 'd-M-Y H:i:s' ) . "]</span> ";
				file_put_contents( $job_object->logfile, $timestamp . "<samp style=\"background-color:red;color:#fff\">" . __( 'ERROR:', 'backwpup' ) . " " . __( 'Aborted by user!', 'backwpup' ) . "</samp>" . PHP_EOL, FILE_APPEND );
				//write new log header
				$job_object->errors ++;
				$fd      = fopen( $job_object->logfile, 'r+' );
				$filepos = ftell( $fd );
				while ( ! feof( $fd ) ) {
					$line = fgets( $fd );
					if ( stripos( $line, "<meta name=\"backwpup_errors\"" ) !== FALSE ) {
						fseek( $fd, $filepos );
						fwrite( $fd, str_pad( "<meta name=\"backwpup_errors\" content=\"" . esc_attr( $job_object->errors ) . "\" />", 100 ) . PHP_EOL );
						break;
					}
					$filepos = ftell( $fd );
				}
				fclose( $fd );
				//update job settings
				if ( ! empty( $job_object->job[ 'jobid' ] ) )
					BackWPup_Option::update( $job_object->job[ 'jobid' ], 'lastruntime', ( current_time( 'timestamp' ) - $job_object->start_time ) );
				//clean up temp
				if ( ! empty( $job_object->backup_file ) && is_file( BackWPup::get_plugin_data( 'TEMP' ) . $job_object->backup_file ) )
					unlink( BackWPup::get_plugin_data( 'TEMP' ) . $job_object->backup_file );
				if ( ! empty( $job_object->folder_list_file ) && is_file( $job_object->folder_list_file ) )
					unlink( $job_object->folder_list_file );
				if ( ! empty( $job_object->additional_files_to_backup ) ) {
					foreach ( $job_object->additional_files_to_backup as $additional_file ) {
						if ( $additional_file == BackWPup::get_plugin_data( 'TEMP' ) . basename( $additional_file ) && is_file( $additional_file ) )
							unlink( $additional_file );
					}
				}
				BackWPup_Admin::message( __( 'Job will be terminated.', 'backwpup' ) ) ;
				break;
			default:
				do_action( 'backwpup_page_jobs_load', self::$listtable->current_action() );
				break;
		}

		self::$listtable->prepare_items();
	}

	/**
	 *
	 */
	public static function admin_print_styles() {

		wp_enqueue_style('backwpupgeneral');

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			wp_enqueue_style( 'backwpuppageworking', BackWPup::get_plugin_data( 'URL' ) . '/css/page_jobs.dev.css', '', time(), 'screen' );
		} else {
			wp_enqueue_style( 'backwpuppageworking', BackWPup::get_plugin_data( 'URL' ) . '/css/page_jobs.css', '', BackWPup::get_plugin_data( 'Version' ), 'screen' );
		}

	}

	/**
	 *
	 */
	public static function admin_print_scripts() {

		wp_enqueue_script( 'backwpupgeneral' );
	}

	/**
	 *
	 */
	public static function page() {

		echo '<div class="wrap">';
		screen_icon();
		echo '<h2>' . esc_html( sprintf( __( '%s Jobs', 'backwpup' ), BackWPup::get_plugin_data( 'name' ) ) ) . '&nbsp;<a href="' . wp_nonce_url( network_admin_url( 'admin.php' ) . '?page=backwpupeditjob', 'edit-job' ) . '" class="button add-new-h2">' . esc_html__( 'Add New', 'backwpup' ) . '</a></h2>';
		BackWPup_Admin::display_messages();
		$job_object = BackWPup_Job::get_working_data();
		if ( current_user_can( 'backwpup_jobs_start' ) && ! empty( $job_object->logfile )  ) {
			echo '<div id="runningjob">';
				//read existing logfile
				$logfiledata = file_get_contents( $job_object->logfile, FALSE, NULL, 0 );
				preg_match( '/<body[^>]*>/si', $logfiledata, $match );
				if ( ! empty( $match[ 0 ] ) )
					$startpos = strpos( $logfiledata, $match[ 0 ] ) + strlen( $match[ 0 ] );
				else
					$startpos = 0;
				$endpos = stripos( $logfiledata, '</body>' );
				if ( empty( $endpos ) )
					$endpos = strlen( $logfiledata );
				$length = strlen( $logfiledata ) - ( strlen( $logfiledata ) - $endpos ) - $startpos;

				echo '<div id="runniginfos">';
					echo '<h2 id="runningtitle">' . sprintf( __('Job currently running: %s','backwpup'), $job_object->job[ 'name' ] ) . '</h2>';
					echo '<span id="warningsid">' . __( 'Warnings:', 'backwpup' ) . ' <span id="warnings">' . $job_object->warnings . '</span></span>';
					echo '<span id="errorid">' . __( 'Errors:', 'backwpup' ) . ' <span id="errors">' . $job_object->errors . '</span></span>';				   
					echo '<div class="infobuttons"><a href="#TB_inline?height=440&width=630&inlineId=tb-showworking" id="showworkingbutton" class="thickbox" title="' . __( 'Working job log', 'backwpup') . '">' . __( 'Display working log', 'backwpup' ) . '</a>';
					echo '<a href="' . wp_nonce_url( network_admin_url( 'admin.php' ) . '?page=backwpupjobs&action=abort', 'abort-job' ) . '" id="abortbutton" class="backwpup-fancybox">' . __( 'Abort', 'backwpup' ) . '</a>';
					echo '<a href="#" id="showworkingclose" title="' . __( 'Close working screen', 'backwpup') .'" style="display:none" >' . __( 'close', 'backwpup' ) . '</a></div>';
				echo '</div>';
				echo '<input type="hidden" name="logpos" id="logpos" value="' . strlen( $logfiledata ) . '">';
				echo '<div id="lasterrormsg"></div>';
				echo '<div class="progressbar"><div id="progressstep" style="width:' . $job_object->step_percent . '%;">' . $job_object->step_percent . '%</div></div>';
				echo '<div id="onstep"><samp>' . $job_object->steps_data[ $job_object->step_working ][ 'NAME' ] . '</samp></div>';
				echo '<div class="progressbar"><div id="progresssteps" style="width:' . $job_object->substep_percent . '%;">' . $job_object->substep_percent . '%</div></div>';
				echo '<div id="lastmsg">' . $job_object->lastmsg . '</div>';
				echo '<div id="tb-showworking" style="display:none;"><div id="showworking">';	
				echo  substr( $logfiledata, $startpos, $length );
				echo '</div></div>';
			echo '</div>';
		}
		//display jos Table
		echo '<form id="posts-filter" action="" method="get">';
		echo '<input type="hidden" name="page" value="backwpupjobs" />';
		wp_nonce_field( 'backwpup_ajax_nonce', 'backwpupajaxnonce', FALSE );
		self::$listtable->display();
		echo '<div id="ajax-response"></div>';
		echo '</form>';
		echo '</div>';

		if ( is_object( $job_object ) ) { ?>
        <script type="text/javascript">
            //<![CDATA[
            jQuery(document).ready(function ($) {
                backwpup_show_working = function () {
                    $.ajax({
                        type: 'GET',
                        url: ajaxurl,
                        cache: false,
                        data:{
                            action: 'backwpup_working',
                            logfile: '<?php echo basename( $job_object->logfile );?>',
                            logpos: $('#logpos').val(),
                            _ajax_nonce: '<?php echo wp_create_nonce( 'backwpupworking_ajax_nonce' );?>'
                        },
                        dataType: 'json',
                        success:function (rundata) {
							if (0 < rundata.log_pos) {
								$('#logpos').val(rundata.log_pos);
							}
                            if ('' != rundata.log_text) {
                                $('#showworking').append(rundata.log_text);
								$('#TB_ajaxContent').scrollTop(rundata.log_pos * 15);
                            }
                            if (0 < rundata.error_count) {
                                $('#errors').replaceWith('<span id="errors">' + rundata.error_count + '</span>');
                            }
                            if (0 < rundata.warning_count) {
                                $('#warnings').replaceWith('<span id="warnings">' + rundata.warning_count + '</span>');
                            }
                            if (0 < rundata.step_percent) {
                                $('#progressstep').replaceWith('<div id="progressstep">' + rundata.step_percent + '%</div>');
                                $('#progressstep').css('width', parseFloat(rundata.step_percent) + '%');
                            }
                            if (0 < rundata.sub_step_percent) {
                                $('#progresssteps').replaceWith('<div id="progresssteps">' + rundata.sub_step_percent + '%</div>');
                                $('#progresssteps').css('width', parseFloat(rundata.sub_step_percent) + '%');
                            }
                            if (0 < rundata.running_time) {
                                $('#runtime').replaceWith('<span id="runtime">' + rundata.running_time + '</span>');
                            }
                            if ( rundata.onstep ) {
                                $('#onstep').replaceWith('<div id="onstep"><samp>' + rundata.on_step + '</samp></div>');
                            }
                            if ( rundata.last_msg ) {
                                $('#lastmsg').replaceWith('<div id="lastmsg">' + rundata.last_msg + '</div>');
                            }
							if ( rundata.last_error_msg ) {
							    $('#lasterrormsg').replaceWith('<div id="lasterrormsg">' + rundata.last_error_msg + '</div>');
						    }
                            if ( rundata.job_done == 1 ) {
                                $("#abortbutton").remove();
                                $("#backwpup-adminbar-running").remove();
								$(".job-run").hide();
                                $("#message").hide();
                                $(".job-normal").show();
                                $('#showworkingclose').show();
                            } else {
                            	setTimeout('backwpup_show_working()', 750);
                            }
                        },
						error:function ( ) {
							setTimeout('backwpup_show_working()', 750);
						}
                    });
                };
                backwpup_show_working();
                $('#showworkingclose').click( function() {
                    $("#runningjob").hide( 'slow' );
                    return false;
                });
            });
            //]]>
        </script>
		<?php }
	}


	/**
	 *
	 * Function to generate json data
	 *
	 */
	public static function ajax_working() {

		check_ajax_referer( 'backwpupworking_ajax_nonce' );

		$logfile = BackWPup_Option::get( 'cfg', 'logfolder') . $_GET[ 'logfile' ];
		$logpos  = isset( $_GET[ 'logpos' ] ) ? (int)$_GET[ 'logpos' ] : 0;

		//check if logfile renamed
		if ( is_file( $logfile . '.gz' ) )
			$logfile .= '.gz';
		if ( is_file( $logfile . '.bz2' ) )
			$logfile .= '.bz2';

		if ( is_file( $logfile ) ) {
			$job_object = BackWPup_Job::get_working_data();
			$done = 0;
			if ( $job_object ) {
				if ( ! is_object( $job_object ) ) {
					die();
				}
				$warnings        = $job_object->warnings;
				$errors          = $job_object->errors;
				$step_percent    = $job_object->step_percent;
				$substep_percent = $job_object->substep_percent;
				$runtime 		 = current_time( 'timestamp' ) - $job_object->start_time;
				$onstep			 = $job_object->steps_data[ $job_object->step_working ][ 'NAME' ];
				$lastmsg		 = $job_object->lastmsg;
				$lasterrormsg    = $job_object->lasterrormsg;
			}
			else {
				$logheader       = BackWPup_Job::read_logheader( $logfile );
				$warnings        = $logheader[ 'warnings' ];
				$runtime         = $logheader[ 'runtime' ];
				$errors          = $logheader[ 'errors' ];
				$step_percent    = 100;
				$substep_percent = 100;
				$onstep			 = __( 'Job end' , 'backwpup' );
				if ( $errors > 0 )
					$lastmsg		 = '<samp style="background-color:red;color:#fff">' . __( 'ERROR:', 'backwpup' ) . ' ' .  sprintf( __( 'Job has ended with errors in %s seconds. You must resolve the errors for correct execution.', 'backwpup' ), $logheader[ 'runtime' ] ) . '</samp>';
				elseif ( $warnings > 0 )
					$lastmsg		 = '<samp style="background-color:#ffc000;color:#fff">' . __( 'WARNING:', 'backwpup' ) . ' ' .  sprintf( __( 'Job has done with warnings in %s seconds. Please resolve them for correct execution.', 'backwpup' ), $logheader[ 'runtime' ] ) . '</samp>';
				else
					$lastmsg		 = '<samp>' .  sprintf( __( 'Job done in %s seconds.', 'backwpup' ), $logheader[ 'runtime' ] ) . '</samp>';
				$lasterrormsg    = '';
			}

			if ( '.gz' == substr( $logfile, -3 ) )
				$logfiledata = file_get_contents( 'compress.zlib://' . $logfile, FALSE, NULL, $logpos );
			elseif ( '.bz2' == substr( $logfile, -4 ) )
				$logfiledata = file_get_contents( 'compress.bzip2://' . $logfile, FALSE, NULL, $logpos );
			else
				$logfiledata = file_get_contents( $logfile, FALSE, NULL, $logpos );

			preg_match( '/<body[^>]*>/si', $logfiledata, $match );
			if ( ! empty( $match[ 0 ] ) )
				$startpos = strpos( $logfiledata, $match[ 0 ] ) + strlen( $match[ 0 ] );
			else
				$startpos = 0;

			$endpos = stripos( $logfiledata, '</body>' );
			$stop   = '';
			if ( $endpos !== FALSE ) {
				$done            = 1;
				$step_percent    = 100;
				$substep_percent = 100;
			}
			if ( FALSE === $endpos )
				$endpos = strlen( $logfiledata );
			$length = strlen( $logfiledata ) - ( strlen( $logfiledata ) - $endpos ) - $startpos;

			@header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ), TRUE );
			echo json_encode( array(
								   'log_pos'         => strlen( $logfiledata ) + $logpos,
								   'log_text'        => substr( $logfiledata, $startpos, $length ) . $stop,
								   'warning_count'   => $warnings,
								   'error_count'     => $errors,
								   'running_time'	 => $runtime,
								   'step_percent'    => $step_percent,
								   'on_step'		 => $onstep,
								   'last_msg'		 => $lastmsg,
								   'last_error_msg'	 => $lasterrormsg,
								   'sub_step_percent'=> $substep_percent,
								   'job_done'		 => $done
							  ) );
		}
		die();
	}

}

