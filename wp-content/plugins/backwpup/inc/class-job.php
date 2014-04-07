<?php
/**
 * Class in that the BackWPup job runs
 */
final class BackWPup_Job {
	/**
	 * @var object The instance
	 */
	private static $instance = NULL;

	/**
	 * @var string The job start type
	 */
	private $jobstarttype = '';
	/**
	 * @var array of the job settings
	 */
	public $job = array();

	/**
	 * @var int The timestamp when the job starts
	 */
	public $start_time = 0;

	/**
	 * @var string the logfile
	 */
	public $logfile = '';
	/**
	 * @var array for temp values
	 */
	public $temp = array();
	/**
	 * @var string Folder where is Backup files in
	 */
	public $backup_folder = '';
	/**
	 * @var string the name of the Backup archive file
	 */
	public $backup_file = '';
	/**
	 * @var int The size of the Backup archive file
	 */
	public $backup_filesize = 0;
	/**
	 * @var int PID of script
	 */
	public $pid = 0;
	/**
	 * @var int Timestamp of last update off .running file
	 */
	public $timestamp_last_update = 0;
	/**
	 * @var int Number of warnings
	 */
	public $warnings = 0;
	/**
	 * @var int Number of errors
	 */
	public $errors = 0;
	/**
	 * @var string the last log notice message
	 */
	public $lastmsg = '';
	/**
	 * @var string the last log error/waring message
	 */	
	public $lasterrormsg = '';
	/**
	 * @var array of steps to do
	 */
	public $steps_todo = array( 'START' );
	/**
	 * @var array of done steps
	 */
	public $steps_done = array();
	/**
	 * @var array  of steps data
	 */
	public $steps_data = array();
	/**
	 * @var string working on step
	 */
	public $step_working = 'START';
	/**
	 * @var int Number of sub steps must do in step
	 */
	public $substeps_todo = 0;
	/**
	 * @var int Number of sub steps done in step
	 */
	public $substeps_done = 0;
	/**
	 * @var int Percent of steps done
	 */
	public $step_percent = 1;
	/**
	 * @var int Percent of sub steps done
	 */
	public $substep_percent = 1;
	/**
	 * @var array of files to additional to backup
	 */
	public $additional_files_to_backup = array();
	/**
	 * @var string file where folders listed for backup
	 */
	public $folder_list_file = '';
	/**
	 * @var array of files/folder to exclude from backup
	 */
	public $exclude_from_backup = array();
	/**
	 * @var int count of affected files
	 */
	public $count_files = 0;
	/**
	 * @var int count of affected file size
	 */
	public $count_filesize = 0;
	/**
	 * @var int count of affected folders
	 */
	public $count_folder = 0;
	/**
	 * @var int count of files in a folder
	 */
	public $count_files_in_folder = 0;
	/**
	 * @var int count of files size in a folder
	 */
	public $count_filesize_in_folder = 0;
	/**
	 * @var string path to remove from file path
	 */
	public $remove_path = '';

	/**
	 *
	 * This starts or restarts the job working
	 *
	 * @param string $start_type Start types are 'runnow', 'runnowalt', 'cronrun', 'runext', 'runcli'         
	 * @param array|int $job_settings The id of job or the settings of a job to start
	 */
	private function __construct( $start_type, $job_settings = 0 ) {
		global $wpdb;
		/* @var wpdb $wpdb */
	
		//check startype
		if ( ! in_array( $start_type, array( 'runnow', 'runnowalt', 'cronrun', 'runext', 'runcli' ) ) )
			return;

		if ( is_int( $job_settings ) )
			$this->job      = BackWPup_Option::get_job( $job_settings );
		elseif( is_array( $job_settings ) )
			$this->job		= $job_settings;
		else
			return;
		$this->jobstarttype = $start_type;
		$this->start_time   =  current_time( 'timestamp' );
		$this->lastmsg		= '<samp>' . __( 'Starting job', 'backwpup' ) . '</samp>';
		//set Logfile
		$this->logfile = BackWPup_Option::get( 'cfg', 'logfolder' ) . 'backwpup_log_' . substr( md5( md5( SECURE_AUTH_KEY ) ), 10, 6 ). '_' . date_i18n( 'Y-m-d_H-i-s' ) . '.html';
		//write settings to job
		if ( ! empty( $this->job[ 'jobid' ] ) ) {
			BackWPup_Option::update( $this->job[ 'jobid' ], 'lastrun', $this->start_time );
			BackWPup_Option::update( $this->job[ 'jobid' ], 'logfile', $this->logfile ); //Set current logfile
			BackWPup_Option::update( $this->job[ 'jobid' ], 'lastbackupdownloadurl', '' );
		}
		//Set needed job values
		$this->timestamp_last_update = microtime( TRUE );
		$this->exclude_from_backup 	= explode( ',', trim( $this->job[ 'fileexclude' ] ) );
		$this->exclude_from_backup 	= array_unique( $this->exclude_from_backup );
		if ( trailingslashit( str_replace( '\\', '/', ABSPATH ) ) != '/' and trailingslashit( str_replace( '\\', '/', ABSPATH ) ) != '' ) //create path to remove
			$this->remove_path 		= trailingslashit( str_replace( '\\', '/', ABSPATH ) );
		//setup job steps
		$this->steps_data[ 'START' ][ 'CALLBACK' ] = '';
		$this->steps_data[ 'START' ][ 'NAME' ]     = __( 'Job Start', 'backwpup' );
		$this->steps_data[ 'START' ][ 'STEP_TRY' ] = 0;
		//ADD Job types file
		/* @var $job_type_class BackWPup_JobTypes */
		$job_need_dest = FALSE;
		if ( $job_types = BackWPup::get_job_types() ) {
			foreach ( $job_types as $id => $job_type_class ) {
				if ( in_array( $id, $this->job[ 'type' ] ) && $job_type_class->creates_file( ) ) {
					$this->steps_todo[ ]                            = 'JOB_' . $id;
					$this->steps_data[ 'JOB_' . $id ][ 'NAME' ]     = $job_type_class->info[ 'description' ];
					$this->steps_data[ 'JOB_' . $id ][ 'STEP_TRY' ] = 0;
					$job_need_dest                                  = TRUE;
				}
			}
		}
		//add destinations and create archive if a job where files to backup
		if ( $job_need_dest ) {
			//Set file for folder list
			$this->folder_list_file = BackWPup::get_plugin_data( 'temp' ) . 'backwpup-' . substr( md5( NONCE_SALT ), 19, 6 ) . '-folders.php';
			//Add archive creation and backup filename on backup type archive
			if ( $this->job[ 'backuptype' ] == 'archive' ) {
				//set Backup folder to temp folder if not set
				if ( in_array( 'FOLDER', $this->job[ 'destinations' ] ) )
					$this->backup_folder = $this->job[ 'backupdir' ];
				//check backups folder
				if ( ! empty( $this->backup_folder ) )
					self::check_folder( $this->backup_folder );
				//set temp folder to backup folder if not set
				if ( ! $this->backup_folder or $this->backup_folder == '/' )
					$this->backup_folder = BackWPup::get_plugin_data( 'TEMP' );
				//Create backup archive full file name
				$this->backup_file = $this->generate_filename( $this->job[ 'archivename' ], $this->job[ 'archiveformat' ] );
				//add archive create
				$this->steps_todo[ ]                                = 'CREATE_ARCHIVE';
				$this->steps_data[ 'CREATE_ARCHIVE' ][ 'NAME' ]     = __( 'Creates archive', 'backwpup' );
				$this->steps_data[ 'CREATE_ARCHIVE' ][ 'STEP_TRY' ] = 0;
			}
			//ADD Destinations
			/* @var BackWPup_Destinations $dest_class */
			foreach ( BackWPup::get_destinations() as $id => $dest_class ) {
				if ( in_array( $id, $this->job[ 'destinations' ] ) && $dest_class->can_run( $this ) ) {
					if ( $this->job[ 'backuptype' ] == 'sync' ) {
						if ( call_user_func( array( $dest_class, 'can_sync' ) ) ) {
							$this->steps_todo[]                                   = 'DEST_SYNC_' . $id;
							$this->steps_data[ 'DEST_SYNC_' . $id ][ 'NAME' ]     = $dest_class->info[ 'description' ];
							$this->steps_data[ 'DEST_SYNC_' . $id ][ 'STEP_TRY' ] = 0;
						}
					} else {
						$this->steps_todo[]                              = 'DEST_' . $id;
						$this->steps_data[ 'DEST_' . $id ][ 'NAME' ]     = $dest_class->info[ 'description' ];
						$this->steps_data[ 'DEST_' . $id ][ 'STEP_TRY' ] = 0;
					}
				}
			}
		}
		//ADD Job type no file
		if ( $job_types = BackWPup::get_job_types() ) {
			foreach ( $job_types as $id => $job_type_class ) {
				if ( in_array( $id, $this->job[ 'type' ] ) && ! $job_type_class->creates_file() ) {
					$this->steps_todo[ ]                            = 'JOB_' . $id;
					$this->steps_data[ 'JOB_' . $id ][ 'NAME' ]     = $job_type_class->info[ 'description' ];
					$this->steps_data[ 'JOB_' . $id ][ 'STEP_TRY' ] = 0;
				}
			}
		}
		$this->steps_todo[]                      = 'END';
		$this->steps_data[ 'END' ][ 'NAME' ]     = __( 'Job End', 'backwpup' );
		$this->steps_data[ 'END' ][ 'STEP_TRY' ] = 0;
		//create log file
		$fd = fopen( $this->logfile, 'w' );
		fwrite( $fd, "<!DOCTYPE html>" . PHP_EOL . "<html lang=\"" . str_replace( '_', '-', get_locale() ) . "\">" . PHP_EOL . "<head>" . PHP_EOL );
		fwrite( $fd, "<meta charset=\"" . get_bloginfo( 'charset' ) . "\" />" . PHP_EOL );
		fwrite( $fd, "<title>" . sprintf( __( 'BackWPup log for %1$s from %2$s at %3$s', 'backwpup' ), $this->job[ 'name' ], date_i18n( get_option( 'date_format' ) ), date_i18n( get_option( 'time_format' ) ) ) . "</title>" . PHP_EOL );
		fwrite( $fd, "<meta name=\"robots\" content=\"noindex, nofollow\" />" . PHP_EOL );
		fwrite( $fd, "<meta name=\"copyright\" content=\"Copyright &copy; 2012 - " . date_i18n( 'Y' ) . " Inpsyde GmbH\" />" . PHP_EOL );
		fwrite( $fd, "<meta name=\"author\" content=\"Daniel H&uuml;sken\" />" . PHP_EOL );
		fwrite( $fd, "<meta name=\"generator\" content=\"BackWPup " . BackWPup::get_plugin_data( 'Version' ) . "\" />" . PHP_EOL );
		fwrite( $fd, "<meta http-equiv=\"cache-control\" content=\"no-cache\" />" . PHP_EOL );
		fwrite( $fd, "<meta http-equiv=\"pragma\" content=\"no-cache\" />" . PHP_EOL );
		fwrite( $fd, "<meta name=\"date\" content=\"" . date( 'c' ) . "\" />" . PHP_EOL );
		fwrite( $fd, str_pad( "<meta name=\"backwpup_errors\" content=\"0\" />", 100 ) . PHP_EOL );
		fwrite( $fd, str_pad( "<meta name=\"backwpup_warnings\" content=\"0\" />", 100 ) . PHP_EOL );
		if ( ! empty( $this->job[ 'jobid' ] ) )
			fwrite( $fd, "<meta name=\"backwpup_jobid\" content=\"" . $this->job[ 'jobid' ] . "\" />" . PHP_EOL );
		fwrite( $fd, "<meta name=\"backwpup_jobname\" content=\"" . esc_attr( $this->job[ 'name' ] ) . "\" />" . PHP_EOL );
		fwrite( $fd, "<meta name=\"backwpup_jobtype\" content=\"" . implode( '+', $this->job[ 'type' ] ) . "\" />" . PHP_EOL );
		fwrite( $fd, str_pad( "<meta name=\"backwpup_backupfilesize\" content=\"0\" />", 100 ) . PHP_EOL );
		fwrite( $fd, str_pad( "<meta name=\"backwpup_jobruntime\" content=\"0\" />", 100 ) . PHP_EOL );
		fwrite( $fd, "</head>" . PHP_EOL . "<body style=\"margin:0;padding:3px;font-family:Fixedsys,Courier,monospace;font-size:12px;line-height:15px;background-color:#000;color:#fff;white-space:pre;\">" );
		$info = '';
		$info .= sprintf( _x( '[INFO] %1$s version %2$s; WordPress version %3$s; A project of Inpsyde GmbH developed by Daniel Hüsken','Plugin name; Plugin Version; WordPress Version','backwpup' ), BackWPup::get_plugin_data( 'name' ) , BackWPup::get_plugin_data( 'Version' ), BackWPup::get_plugin_data( 'wp_version' ) ) . PHP_EOL;
		$info .= __( '[INFO] This program comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under certain conditions.', 'backwpup' ) . PHP_EOL;
		$info .= sprintf(__( '[INFO] Blog url: %s', 'backwpup' ) , esc_attr( site_url( '/' ) ) ). PHP_EOL;		
		$info .= sprintf(__( '[INFO] BackWPup job: %1$s; %2$s', 'backwpup' ), esc_attr( $this->job[ 'name' ] ) , implode( '+', $this->job[ 'type' ] ) ) . PHP_EOL;
		if ( $this->job[ 'activetype' ] != '' )
			$info .= __( '[INFO] BackWPup cron:', 'backwpup' ) . ' ' . $this->job[ 'cron' ] . '; ' . date_i18n( 'D, j M Y @ H:i' ) . PHP_EOL;
		if ( $this->jobstarttype == 'cronrun' )
			$info .= __( '[INFO] BackWPup job started from wp-cron', 'backwpup' ) . PHP_EOL;
		elseif ( $this->jobstarttype == 'runnow' or $this->jobstarttype == 'runnowalt' )
			$info .= __( '[INFO] BackWPup job started manually', 'backwpup' ) . PHP_EOL;
		elseif ( $this->jobstarttype == 'runext' )
			$info .= __( '[INFO] BackWPup job started from external url', 'backwpup' ) . PHP_EOL;
		elseif ( $this->jobstarttype == 'runcli' )
			$info .= __( '[INFO] BackWPup job started form commandline interface', 'backwpup' ) . PHP_EOL;
		$info .= __( '[INFO] PHP ver.:', 'backwpup' ) . ' ' . PHP_VERSION . '; ' . PHP_SAPI . '; ' . PHP_OS . PHP_EOL;
		$info .= sprintf( __( '[INFO] Maximum script execution time is %1$d seconds', 'backwpup' ), ini_get( 'max_execution_time' ) ) . PHP_EOL;
		$info .= sprintf( __( '[INFO] MySQL ver.: %s', 'backwpup' ), $wpdb->get_var( "SELECT VERSION() AS version" ) ) . PHP_EOL;
		if ( function_exists( 'curl_init' ) ) {
			$curlversion = curl_version();
			$info .= sprintf( __( '[INFO] curl ver.: %1$s; %2$s', 'backwpup' ), $curlversion[ 'version' ], $curlversion[ 'ssl_version' ] ) . PHP_EOL;
		}
		$info .= sprintf( __( '[INFO] Temp folder is: %s', 'backwpup' ), BackWPup::get_plugin_data( 'TEMP' ) ) . PHP_EOL;
		$info .= sprintf( __( '[INFO] Logfile folder is: %s', 'backwpup' ), BackWPup_Option::get( 'cfg', 'logfolder' ) ) . PHP_EOL;
		$info .= sprintf( __( '[INFO] Backup type is: %s', 'backwpup' ), $this->job[ 'backuptype' ] ) . PHP_EOL;
		if ( ! empty( $this->backup_file ) && $this->job[ 'backuptype' ] == 'archive' )
			$info .= sprintf( __( '[INFO] Backup file is: %s', 'backwpup' ), $this->backup_folder . $this->backup_file ) . PHP_EOL;
		fwrite( $fd, $info );
		fwrite( $fd, '</header>' );
		fclose( $fd );
		//output info on cli
		if ( defined( 'STDIN' ) && defined( 'STDOUT' ) )
			fwrite( STDOUT, strip_tags( $info ) ) ;
		//test for destinations
		if ( $job_need_dest ) {
			$desttest = FALSE;
			foreach ( $this->steps_todo as $deststeptest ) {
				if ( substr( $deststeptest, 0, 5 ) == 'DEST_' ) {
					$desttest = TRUE;
					break;
				}
			}
			if ( ! $desttest )
				$this->log( __( 'No destination correctly defined for backup! Please correct job settings.', 'backwpup' ), E_USER_ERROR );
		}
		//Set start as done
		$this->steps_done[] = 'START';
		//must write working data
		$this->update_working_data( TRUE );
	}

	// prevent 'clone()' from external.
	private function __clone() {}

	/**
	 *
	 * @return array
	 */
	public function __sleep(){
		//not saved:  'temp',
		return array( 'jobstarttype', 'job', 'start_time', 'logfile', 'backup_folder', 'folder_list_file',
					  'backup_file', 'backup_filesize', 'pid', 'timestamp_last_update', 'warnings', 'errors', 'lastmsg', 'lasterrormsg',
					  'steps_todo', 'steps_done', 'steps_data', 'step_working', 'substeps_todo', 'substeps_done', 'step_percent',
					  'substep_percent', 'additional_files_to_backup', 'exclude_from_backup', 'count_files',
					  'count_filesize', 'count_folder', 'count_files_in_folder', 'count_filesize_in_folder', 'remove_path' );
	}

	/**
	 * get instance
	 *
	 * @return null|object
	 */
	public static function getInstance() {

		return self::$instance;
	}


	/**
	 *
	 * Get a url to run a job of BackWPup
	 *
	 * @param string     $starttype Start types are 'runnow', 'runnowlink', 'cronrun', 'runext', 'runcmd', 'restart'
	 * @param int        $jobid     The id of job to start else 0
	 * @return array|object [url] is the job url [header] for auth header or object form wp_remote_get()
	 */
	public static function get_jobrun_url( $starttype, $jobid = 0 ) {

		
		$wp_admin_user 		= get_users( array( 'role' => 'administrator' ) );	//get a user for cookie auth	
		$url        		= site_url( 'wp-cron.php' );
		$header				= array();
		$header[ 'Cookie' ] = LOGGED_IN_COOKIE. '='. wp_generate_auth_cookie( $wp_admin_user[ 0 ]->ID, time() + 60, 'logged_in'); // add auth cookie to header
		$authurl    		= '';
		$query_args 		= array( '_nonce' => substr( wp_hash( wp_nonce_tick() . 'backwup_job_run-' . $starttype, 'nonce' ), - 12, 10 ) );

		if ( in_array( $starttype, array( 'restart', 'runnow', 'cronrun', 'runext' ) ) )
			$query_args[ 'backwpup_run' ] = $starttype;

		if ( in_array( $starttype, array( 'runnowlink', 'runnow', 'cronrun', 'runext' ) ) && ! empty( $jobid ) )
			$query_args[ 'jobid' ] = $jobid;

		if ( BackWPup_Option::get( 'cfg', 'httpauthuser' ) && BackWPup_Option::get( 'cfg', 'httpauthpassword' ) ) {
			$header[ 'Authorization' ] = 'Basic ' . base64_encode( BackWPup_Option::get( 'cfg', 'httpauthuser' ) . ':' . BackWPup_Encryption::decrypt( BackWPup_Option::get( 'cfg', 'httpauthpassword' ) ) );
			$authurl = BackWPup_Option::get( 'cfg', 'httpauthuser' ) . ':' . BackWPup_Encryption::decrypt( BackWPup_Option::get( 'cfg', 'httpauthpassword' ) ) . '@';
		}	

		if ( $starttype == 'runext' ) {
			$query_args[ '_nonce' ] = BackWPup_Option::get( 'cfg', 'jobrunauthkey' );
			if ( ! empty( $authurl ) ) {
				$url = str_replace( 'https://', 'https://' . $authurl, $url );
				$url = str_replace( 'http://', 'http://' . $authurl, $url );
			}
		}

		if ( $starttype == 'runnowlink' && ( ! defined( 'ALTERNATE_WP_CRON' ) || ! ALTERNATE_WP_CRON ) ) {
			$url                       	= wp_nonce_url( network_admin_url( 'admin.php' ), 'backwup_job_run-' . $starttype );
			$query_args[ 'page' ]      	= 'backwpupjobs';
			$query_args[ 'action' ] 	= 'runnow';
			unset(  $query_args[ '_nonce' ] );
		}

		if ( $starttype == 'runnowlink' && defined( 'ALTERNATE_WP_CRON' ) && ALTERNATE_WP_CRON ) {
			$query_args[ 'backwpup_run' ] = 'runnowalt';
			$query_args[ '_nonce' ]    = substr( wp_hash( wp_nonce_tick() . 'backwup_job_run-runnowalt', 'nonce' ), - 12, 10 );
		}

		$url = array(
			'url'    => add_query_arg( $query_args, $url ),
			'header' => $header
		);

		if ( ! in_array( $starttype, array( 'runnowlink', 'runext' ) ) ) {
			return @wp_remote_get( $url[ 'url' ], array(
													   'blocking'   => FALSE,
													   'sslverify' 	=> FALSE,
													   'timeout' 	=> 0.01,
													   'headers'    => $url[ 'header' ],
													   'user-agent' => BackWpup::get_plugin_data( 'User-Agent' )
												  ) );
		}

		return $url;
	}


	/**
	 *
	 */
	public static function start_http($starttype) {

		//prevent W3TC object cache
		define('DONOTCACHEOBJECT', TRUE);

		//load text domain if needed
		if ( ! is_textdomain_loaded( 'backwpup' ) && ! BackWPup_Option::get( 'cfg', 'jobnotranslate') )
			load_plugin_textdomain( 'backwpup', FALSE, BackWPup::get_plugin_data( 'BaseName' ) . '/languages' );

		//special header
		@putenv( "nokeepalive=1" );
		@header( 'Content-Type: text/html; charset=' . get_bloginfo( 'charset' ) );
		@header( 'X-Robots-Tag: noindex, nofollow' );
		send_nosniff_header();
		nocache_headers();

		//check get vars
		if ( isset( $_GET[ 'jobid' ] ) )
			$jobid = (int)$_GET[ 'jobid' ];
		else
			$jobid = 0;
		//check job id exists
		if ( $starttype != 'restart' && $jobid != BackWPup_Option::get( $jobid, 'jobid' ) ) {
			trigger_error( __( 'Wrong BackWPup JobID', 'backwpup' ) . ' ' . $jobid, E_USER_ERROR );
			wp_die( __( 'Wrong BackWPup JobID', 'backwpup' ), __( 'Wrong BackWPup JobID', 'backwpup' ), array( 'response' => 400 ) );
		}
		//check folders
		if ( ! self::check_folder( BackWPup_Option::get( 'cfg', 'logfolder' ) ) ) {
			trigger_error( __( 'Log folder does not exist or is not writable for BackWPup', 'backwpup' ), E_USER_ERROR );
			wp_die( __( 'Log folder does not exist or is not writable for BackWPup', 'backwpup' ), __( 'Log folder does not exist or is not writable for BackWPup', 'backwpup' ), array( 'response' => 500 ) );
		}
		if ( ! self::check_folder( BackWPup::get_plugin_data( 'TEMP' ) ) ) {
			trigger_error( __( 'Temp folder does not exist or is not writable for BackWPup', 'backwpup' ), E_USER_ERROR );
			wp_die( __( 'Temp folder does not exist or is not writable for BackWPup', 'backwpup' ), __( 'Temp folder does not exist or is not writable for BackWPup', 'backwpup' ), array( 'response' => 500 ) );
		}
		$backups_folder = BackWPup_Option::get( $jobid, 'backupdir' );
		if ( ! empty( $backups_folder ) && ! self::check_folder( $backups_folder ) ) {
			trigger_error( __( 'Backups folder does not exist or is not writable for BackWPup', 'backwpup' ), E_USER_ERROR );
			wp_die( __( 'Backups folder does not exist or is not writable for BackWPup', 'backwpup' ), __( 'Backups folder does not exist or is not writable for BackWPup', 'backwpup' ), array( 'response' => 500 ) );
		}
		//check running job
		$backwpup_job_object = self::get_working_data();
		if ( $starttype == 'restart' && ! $backwpup_job_object ) {
			trigger_error( __( 'No BackWPup job running', 'backwpup' ), E_USER_ERROR );
			wp_die( __( 'No BackWPup job running', 'backwpup' ), __( 'No BackWPup job running', 'backwpup' ), array( 'response' => 400 ) );
		}
		if ( $starttype != 'restart' && ( is_object( $backwpup_job_object ) || is_int( $backwpup_job_object ) ) ) {
			trigger_error( __( 'A BackWPup job is already running', 'backwpup' ), E_USER_ERROR );
			wp_die( __( 'A BackWPup job is already running', 'backwpup' ), __( 'A BackWPup job is already running', 'backwpup' ), array( 'response' => 503 ) );
		}
		if ( $starttype == 'restart' && is_object( $backwpup_job_object ) ) {
			self::$instance = $backwpup_job_object;
		}
		//early write working file to prevent double run
		if ( in_array( $starttype, array( 'runnow', 'runnowalt', 'runext' ) ) && ! $backwpup_job_object ) {
			update_site_option( 'backwpup_working_job', (int)$jobid );
			file_put_contents( BackWPup::get_plugin_data( 'running_file' ), '<?php //'. serialize( (object) array() ), LOCK_EX );
		}
		// disable user abort. wp-cron.php set it now
		//ignore_user_abort( TRUE );
		//close session file on server side to avoid blocking other requests
		session_write_close();
		// disconnect or redirect
		ob_start();
		if ( $starttype == 'runnowalt' )
			wp_redirect( add_query_arg( array( 'page' => 'backwpupjobs' ), network_admin_url( 'admin.php' ) ) );
		header( "Content-Length: " . ob_get_length() );
		header( "Connection: close" );
		ob_end_flush();
		flush();
		//start class
		if ( ! $backwpup_job_object && in_array( $starttype, array( 'runnow', 'runnowalt', 'runext' ) ) ) {
			//schedule restart event
			wp_schedule_single_event( time() + 60, 'backwpup_cron', array( 'id' => 'restart' ) );
			//start job
			self::$instance = new self( $starttype, (int)$jobid );
		}
		if( is_object( self::$instance ) )
			self::$instance->run();
	}

	/**
	 * @param $jobid
	 */
	public static function start_cli( $jobid ) {

		if ( ! defined( 'STDIN' ) )
			return;

		//define DOING_CRON to prevent caching
		if( ! defined( 'DOING_CRON' ) )
			define( 'DOING_CRON', TRUE );

		//prevent W3TC object cache
		define('DONOTCACHEOBJECT', TRUE);

		//load text domain if needed
		if ( ! is_textdomain_loaded( 'backwpup' ) && ! BackWPup_Option::get( 'cfg', 'jobnotranslate') )
			load_plugin_textdomain( 'backwpup', FALSE, BackWPup::get_plugin_data( 'BaseName' ) . '/languages' );

		//check job id exists
		$jobids = BackWPup_Option::get_job_ids();
		if ( ! in_array( $jobid, $jobids ) ) {
			trigger_error( __( 'Wrong BackWPup JobID', 'backwpup' ), E_USER_ERROR );
			die( __( 'Wrong BackWPup JobID', 'backwpup' ) );
		}
		//check folders
		if ( ! self::check_folder( BackWPup_Option::get( 'cfg', 'logfolder' ) ) ) {
			trigger_error( __( 'Log folder does not exist or is not writable', 'backwpup' ), E_USER_ERROR );
			die( __( 'Log folder does not exist or is not writable for BackWPup', 'backwpup' ) );
		}
		if ( ! self::check_folder( BackWPup::get_plugin_data( 'TEMP' ) ) ) {
			trigger_error( __( 'Temp folder does not exist or is not writable', 'backwpup' ), E_USER_ERROR );
			die( __( 'Temp folder does not exist or is not writable for BackWPup', 'backwpup' ) );
		}
		//check running job
		if ( self::get_working_data( FALSE ) ) {
			trigger_error( __( 'A BackWPup job is already running', 'backwpup' ), E_USER_ERROR );
			die( __( 'A BackWPup job is already running', 'backwpup' ) );
		}			
		//early write working file to prevent double run
		update_site_option( 'backwpup_working_job', (int)$jobid );
		file_put_contents( BackWPup::get_plugin_data( 'running_file' ), '<?php //'. serialize( (object) array() ), LOCK_EX );
		//start/restart class
		fwrite( STDOUT, __( 'Job Started' ) . PHP_EOL );
		fwrite( STDOUT, '----------------------------------------------------------------------' . PHP_EOL );
		self::$instance = new self( 'runcli', (int)$jobid );
		if( is_object( self::$instance ) )
			self::$instance->run();
	}

	/**
	 * @param int $jobid
	 */
	public static function start_wp_cron( $jobid = 0 ) {

		if ( ! defined( 'DOING_CRON' ) || ! DOING_CRON )
			return;

		//prevent W3TC object cache
		define('DONOTCACHEOBJECT', TRUE);

		//load text domain if needed
		if ( ! is_textdomain_loaded( 'backwpup' ) && ! BackWPup_Option::get( 'cfg', 'jobnotranslate') )
			load_plugin_textdomain( 'backwpup', FALSE, BackWPup::get_plugin_data( 'BaseName' ) . '/languages' );

		//special header
		@putenv( "nokeepalive=1" );
		@header( 'Content-Type: text/html; charset=' . get_bloginfo( 'charset' ) );
		@header( 'X-Robots-Tag: noindex, nofollow' );
		send_nosniff_header();
		nocache_headers();

		//check job id exists
		if ( $jobid != BackWPup_Option::get( $jobid, 'jobid' ) && $jobid != 0 ) {
			trigger_error( __( 'Wrong BackWPup JobID', 'backwpup' ), E_USER_ERROR );

			return;
		}
		//check folders
		if ( ! self::check_folder( BackWPup_Option::get( 'cfg', 'logfolder' ) ) ) {
			trigger_error( __( 'Log folder does not exist or is not writable for BackWPup', 'backwpup' ), E_USER_ERROR );

			return;
		}
		if ( ! self::check_folder( BackWPup::get_plugin_data( 'TEMP' ) ) ) {
			trigger_error( __( 'Temp folder does not exist or is not writable for BackWPup', 'backwpup' ), E_USER_ERROR);

			return;
		}
		//check running job
		self::$instance = self::get_working_data();
		if ( $jobid != 0 && is_object( self::$instance ) ) {
			trigger_error( __( 'A BackWPup job is already running', 'backwpup' ), E_USER_ERROR );

			return;
		}
		//start/restart class
		if ( ! self::$instance && $jobid != 0 ) {
			//early write working file to prevent double run
			update_site_option( 'backwpup_working_job', (int)$jobid );
			file_put_contents( BackWPup::get_plugin_data( 'running_file' ), '<?php //'. serialize( (object) array() ), LOCK_EX );
			//schedule restart event
			wp_schedule_single_event( time() + 60, 'backwpup_cron', array( 'id' => 'restart' ) );
			//start job
			self::$instance = new self( 'cronrun', (int)$jobid );
		}
		if( is_object( self::$instance ) )
			self::$instance->run();
	}


	/**
	 * Run baby run
	 */
	public function run() {
		global $wpdb;
		/* @var wpdb $wpdb */
		
		//Check double running and inactivity
		$job_object = self::get_working_data();
		if ( ! $job_object )
			return;
		$last_update = microtime( TRUE ) - $job_object->timestamp_last_update;
		if ( $job_object->pid != 0 && $last_update > 300) {
			$this->log( __( 'Job restart due to inactivity for more than 5 minutes.', 'backwpup' ), E_USER_WARNING );
		}
		elseif ( $this->pid != 0 && $job_object->pid != self::get_pid() ) {
			$this->log( __( 'Second process start terminated, because a other job is already running!', 'backwpup' ), E_USER_WARNING );
			return;
		}
		unset( $job_object );
		//set Pid
		$this->pid = self::get_pid();
		$this->update_working_data( TRUE );
		//set function for PHP user defined error handling
		$this->temp[ 'PHP' ][ 'INI' ][ 'ERROR_LOG' ]      = ini_get( 'error_log' );
		$this->temp[ 'PHP' ][ 'INI' ][ 'LOG_ERRORS' ]     = ini_get( 'log_errors' );
		$this->temp[ 'PHP' ][ 'INI' ][ 'DISPLAY_ERRORS' ] = ini_get( 'display_errors' );
		@ini_set( 'error_log', $this->logfile );
		@ini_set( 'display_errors', 'Off' );
		@ini_set( 'log_errors', 'On' );
		//set temp folder
		$can_set_temp_env = TRUE;
		$protected_env_vars = explode( ',', ini_get( 'safe_mode_protected_env_vars') );
		foreach( $protected_env_vars as $protected_env ) {
			if ( strtoupper( trim( $protected_env ) ) == 'TMPDIR' )
				$can_set_temp_env = FALSE;
		}
		if ( $can_set_temp_env ) {
			$this->temp[ 'PHP' ][ 'ENV' ][ 'TEMPDIR' ] = getenv( 'TMPDIR' );
			@putenv( 'TMPDIR='.BackWPup::get_plugin_data( 'TEMP') );
		}		
		//increase MySQL timeout
		@ini_set( 'mysql.connect_timeout', '300' );
		$wpdb->query( "SET session wait_timeout = 300" );
		//Write Wordpress DB errors to log
		$wpdb->suppress_errors( FALSE );
		$wpdb->hide_errors();
		//set php execution time
		@set_time_limit( 0 );
		//set wp max memory limit
		@ini_set( 'memory_limit', apply_filters( 'admin_memory_limit', WP_MAX_MEMORY_LIMIT ) );
		//set error handler
		if ( defined( 'WP_DEBUG') && WP_DEBUG ) //on debug display all errors
			set_error_handler( array( $this, 'log' ) );
		else  //on normal display all errors without notices
			set_error_handler( array( $this, 'log' ), E_ALL ^ E_NOTICE ^ E_STRICT );
		set_exception_handler( array( $this, 'exception_handler' ) );
		//not loading Textdomains and unload loaded
		if ( BackWPup_Option::get( 'cfg', 'jobnotranslate') ) {
			add_filter( 'override_load_textdomain', create_function( '','return TRUE;') );
			$GLOBALS[ 'l10n' ] = array();
		}
		//clear caches then the backups smaller and lesser problems
		if ( function_exists( 'apc_clear_cache' ) ) { //clear APC
			apc_clear_cache();
		}
		if ( class_exists('W3_Plugin_TotalCacheAdmin')  ) { //W3TC
			$totalcacheadmin = & w3_instance('W3_Plugin_TotalCacheAdmin');			
			$totalcacheadmin->flush_all();
		} elseif ( function_exists('wp_cache_clear_cache') ) { //WP Super Cache
			wp_cache_clear_cache();
		} elseif ( has_action('cachify_flush_cache') ) { //Cachify
			do_action('cachify_flush_cache');
		}
		$job_types = BackWPup::get_job_types();
		$destinations = BackWPup::get_destinations();
		// execute function on job shutdown  register_shutdown_function( array( $this, 'shutdown' ) );
		add_action( 'shutdown', array( $this, 'shutdown' ) );
		//remove_action('shutdown', array( $this, 'shutdown' ));
		if ( function_exists( 'pcntl_signal' ) ) {
			declare( ticks = 1 ) ; //set ticks
			pcntl_signal( 15, array( $this, 'shutdown' ) ); //SIGTERM
			//pcntl_signal(9, array($this,'shutdown')); //SIGKILL
			pcntl_signal( 2, array( $this, 'shutdown' ) ); //SIGINT
		}
		foreach ( $this->steps_todo as $this->step_working ) {
			//Run next step
			if ( ! in_array( $this->step_working, $this->steps_done ) ) {
				//calc step percent
				if ( count( $this->steps_done ) > 0 )
					$this->step_percent = round( count( $this->steps_done ) / count( $this->steps_todo ) * 100 );
				else
					$this->step_percent = 1;
				while ( $this->steps_data[ $this->step_working ][ 'STEP_TRY' ] < BackWPup_Option::get( 'cfg', 'jobstepretry' ) ) {
					if ( in_array( $this->step_working, $this->steps_done ) )
						break;
					$this->steps_data[ $this->step_working ][ 'STEP_TRY' ] ++;
					$this->update_working_data( TRUE );
					$done = FALSE;
					//executes the methods of job process
					if ( $this->step_working == 'CREATE_ARCHIVE')
						$done = $this->create_archive();
					elseif ( $this->step_working == 'END')
						$this->end();
					elseif ( strstr( $this->step_working, 'JOB_' ) )
						$done = call_user_func( array( $job_types[ str_replace( 'JOB_', '', $this->step_working ) ], 'job_run' ), $this );
					elseif ( strstr( $this->step_working, 'DEST_SYNC_' ) )
						$done = call_user_func( array( $destinations[ str_replace( 'DEST_SYNC_', '', $this->step_working ) ], 'job_run_sync' ), $this );
					elseif ( strstr( $this->step_working, 'DEST_' ) )
						$done = call_user_func( array( $destinations[ str_replace( 'DEST_', '', $this->step_working ) ], 'job_run_archive' ), $this );
					elseif ( ! empty( $this->steps_data[ $this->step_working ][ 'CALLBACK' ] ) )
						$done = call_user_func( $this->steps_data[ $this->step_working ][ 'CALLBACK' ], $this );
					//set step as done
					if ( $done == TRUE ) {
						unset( $this->temp ); //Clean temp
						$this->steps_done[ ] = $this->step_working;
						$this->substeps_done = 0;
						$this->substeps_todo = 0;
					}
					//restart on every job step expect end and only on http connection
					if ( BackWPup_Option::get( 'cfg', 'jobsteprestart' ) )
						$this->do_restart();
				}
				if ( $this->steps_data[ $this->step_working ][ 'STEP_TRY' ] > BackWPup_Option::get( 'cfg', 'jobstepretry' ) )
					$this->log( __( 'Step aborted: too many attempts!', 'backwpup' ), E_USER_ERROR );
			}
		}
	}

	/**
	 * Do a job restart
	 */
	public function do_restart() {
		
		//no restart if no working job
		if ( ! self::get_working_data( FALSE ) )
			exit();
		
		//no restart if in end step
		if ( $this->step_working == 'END' )
			return;
		
		//no restart on cli usage
		if ( defined( 'STDIN' ) )
			$this->end();
		
		//do things for a clean restart
		$this->pid = 0;
		$this->jobstarttype = 'restart';
		$this->update_working_data( TRUE );
		remove_action( 'shutdown', array( $this, 'shutdown' ) );
		//do restart
		if ( defined( 'ALTERNATE_WP_CRON' ) && ALTERNATE_WP_CRON ) {
			//schedule restart for now
			wp_clear_scheduled_hook( 'backwpup_cron', array( 'id' => 'restart' ) );
			wp_schedule_single_event( time(), 'backwpup_cron', array( 'id' => 'restart' ) );
		} else {
			self::get_jobrun_url( 'restart' );
		}
		exit();		
	}

	/**
	 *
	 * Get data off a working job
	 *
	 * @param bool $get_object is full object needed or only that it working
	 *
	 * @return bool|object|int BackWPup_Job Object or Bool if file not exits or job id if file cant read
	 */
	public static function get_working_data( $get_object = TRUE ) {

		if ( ! is_file( BackWPup::get_plugin_data( 'running_file' ) ) )
			return FALSE;

		if ( ! $get_object )
			return TRUE;

		if ( $running_data = file_get_contents( BackWPup::get_plugin_data( 'running_file' ), FALSE, NULL, 8 ) ) {
			$job_object = unserialize( $running_data );
			if ( is_object( $job_object ) ) {
				return $job_object;
			} else {
				//on defect return job id
				return get_site_option( 'backwpup_working_job', 0 );
			}
				
		}

		return FALSE;
	}

	/**
	 *
	 * Reads a BackWPup logfile header and gives back a array of information
	 *
	 * @param string $logfile full logfile path
	 *
	 * @return array|bool
	 */
	public static function read_logheader( $logfile ) {

		$usedmetas = array(
			"date"                    => "logtime",
			"backwpup_logtime"        => "logtime", //old value of date
			"backwpup_errors"         => "errors",
			"backwpup_warnings"       => "warnings",
			"backwpup_jobid"          => "jobid",
			"backwpup_jobname"        => "name",
			"backwpup_jobtype"        => "type",
			"backwpup_jobruntime"     => "runtime",
			"backwpup_backupfilesize" => "backupfilesize"
		);

		//get metadata of logfile
		$metas = array();
		if ( is_file( $logfile ) ) {
			if (  '.gz' == substr( $logfile, -3 )  )
				$metas = (array)get_meta_tags( 'compress.zlib://' . $logfile );
			elseif (  '.bz2' == substr( $logfile, -4 )  )
				$metas = (array)get_meta_tags( 'compress.bzip2://' . $logfile );
			else
				$metas = (array)get_meta_tags( $logfile );
		}

		//only output needed data
		foreach ( $usedmetas as $keyword => $field ) {
			if ( isset( $metas[ $keyword ] ) ) {
				$joddata[ $field ] = $metas[ $keyword ];
			}
			else {
				$joddata[ $field ] = '';
			}
		}

		//convert date
		if ( isset( $metas[ 'date' ] ) )
			$joddata[ 'logtime' ] = strtotime( $metas[ 'date' ] ) + ( get_option( 'gmt_offset' ) * 3600 );

		//use file create date if none
		if ( empty( $joddata[ 'logtime' ] ) )
			$joddata[ 'logtime' ] = filectime( $logfile );

		return $joddata;
	}


	/**
	 *
	 * Shutdown function is call if script terminates try to make a restart if needed
	 *
	 * Prepare the job for start
	 *
	 * @internal param int the signal that terminates the job
	 */
	public function shutdown() {

		$args = func_get_args();

		//nothing on empty
		if ( empty( $this->logfile ) )
			return;
		//Put last error to log if one
		$lasterror = error_get_last();
		if ( $lasterror[ 'type' ] == E_ERROR or $lasterror[ 'type' ] == E_PARSE or $lasterror[ 'type' ] == E_CORE_ERROR or $lasterror[ 'type' ] == E_CORE_WARNING or $lasterror[ 'type' ] == E_COMPILE_ERROR or $lasterror[ 'type' ] == E_COMPILE_WARNING )
			$this->log( $lasterror[ 'type' ], $lasterror[ 'message' ], $lasterror[ 'file' ], $lasterror[ 'line' ] );
		//Put sigterm to log
		if ( ! empty( $args[ 0 ] ) )
			$this->log( sprintf( __( 'Signal %d is sent to script!', 'backwpup' ), $args[ 0 ] ), E_USER_ERROR );

		//Restart on http job
		if ( ! defined( 'STDIN' ) )
			$this->log( __( 'Script stopped! Will start again.', 'backwpup' ) );
		
		$this->do_restart();
	}


	/**
	 *
	 * Check is folder readable and exists create it if not
	 * add .htaccess or index.html file in folder to prevent directory listing
	 *
	 * @param string $folder the folder to check
	 *
	 * @return bool ok or not
	 */
	public static function check_folder( $folder ) {

		$folder = untrailingslashit( str_replace( '\\', '/', $folder ) );
		if ( empty( $folder ) )
			return FALSE;
		//check that is not home of WP
		if ( $folder == untrailingslashit( str_replace( '\\', '/', ABSPATH ) ) ||
			$folder == untrailingslashit( str_replace( '\\', '/', WP_PLUGIN_DIR ) ) ||
			$folder == untrailingslashit( str_replace( '\\', '/', WP_CONTENT_DIR ) )
		) {
			trigger_error( sprintf( __( 'Please use another folder: %1$s', 'backwpup' ), $folder ), E_USER_WARNING );

			return FALSE;
		}
		//create folder if it not exists
		if ( ! is_dir( $folder ) ) {
			if ( ! wp_mkdir_p( $folder ) ) {
				trigger_error( sprintf( __( 'Cannot create folder: %1$s', 'backwpup' ), $folder ), E_USER_WARNING );

				return FALSE;
			}
		}

		//check is writable dir
		if ( ! is_writable( $folder ) ) {
			trigger_error( sprintf( __( 'Folder "%1$s" is not writable', 'backwpup' ), $folder ), E_USER_WARNING );

			return FALSE;
		}

		//create .htaccess for apache and index.php for folder security
		if ( BackWPup_Option::get( 'cfg', 'protectfolders') && ! is_file( $folder . '/.htaccess' ) )
			file_put_contents( $folder . '/.htaccess', "<Files \"*\">" . PHP_EOL . "<IfModule mod_access.c>" . PHP_EOL . "Deny from all" . PHP_EOL . "</IfModule>" . PHP_EOL . "<IfModule !mod_access_compat>" . PHP_EOL . "<IfModule mod_authz_host.c>" . PHP_EOL . "Deny from all" . PHP_EOL . "</IfModule>" . PHP_EOL . "</IfModule>" . PHP_EOL . "<IfModule mod_access_compat>" . PHP_EOL . "Deny from all" . PHP_EOL . "</IfModule>" . PHP_EOL . "</Files>" );
		if ( BackWPup_Option::get( 'cfg', 'protectfolders') && ! is_file( $folder . '/index.php' ) )
			file_put_contents( $folder . '/index.php', "<?php" . PHP_EOL . "header( \$_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found' );" . PHP_EOL . "header( 'Status: 404 Not Found' );" . PHP_EOL );
		if ( ! BackWPup_Option::get( 'cfg', 'protectfolders') && is_file( $folder . '/.htaccess' ) )
			unlink( $folder . '/.htaccess' );
		if ( ! BackWPup_Option::get( 'cfg', 'protectfolders') && is_file( $folder . '/index.php' ) )
			unlink( $folder . '/index.php' );

		return TRUE;
	}

	/**
	 *
	 * The uncouth exception handler
	 *
	 * @param object $exception
	 */
	public function exception_handler( $exception ) {
		$this->log( E_USER_ERROR, sprintf( __( 'Exception caught in %1$s: %2$s', 'backwpup' ), get_class( $exception ), htmlentities( $exception->getMessage() ) ), $exception->getFile(), $exception->getLine() );
	}

	/**
	 * Write messages to log file
	 *
	 * @internal param int     the error number (E_USER_ERROR,E_USER_WARNING,E_USER_NOTICE, ...)
	 * @internal param string  the error message
	 * @internal param string  the full path of file with error (__FILE__)
	 * @internal param int     the line in that is the error (__LINE__)
	 *
	 * @return bool true
	 */
	public function log() {

		$args = func_get_args();
		// if error has been suppressed with an @
		if ( error_reporting() == 0 )
			return TRUE;

		//if first the message an second the type switch it on user errors
		if ( isset( $args[ 1 ] ) && in_array( $args[ 1 ], array( E_USER_NOTICE, E_USER_WARNING, E_USER_ERROR, 16384 ) ) ) {
			$temp 		= $args[ 0 ];
			$args[ 0 ] 	= $args[ 1 ];
			$args[ 1 ] 	= $temp;
		}

		//if first the message and nothing else set
		if ( ! isset( $args[ 1 ] ) ) {
			$args[ 1 ] = $args[ 0 ];
			$args[ 0 ] = E_USER_NOTICE;
		}

		//json message if array or object
		if ( is_array( $args[ 1 ] ) || is_object( $args[ 1 ] ) )
			$args[ 1 ] = json_encode( $args[ 1 ] );

		//if not set line and file get it
		if ( empty( $args[ 2 ] ) || empty( $args[ 3 ] ) ) {
			$debug_info = debug_backtrace();
			$args[ 2 ] = $debug_info[ 0 ][ 'file' ];
			$args[ 3 ] = $debug_info[ 0 ][ 'line' ];
		}

		$error_or_warning = FALSE;

		switch ( $args[ 0 ] ) {
			case E_NOTICE:
			case E_USER_NOTICE:
				$messagetype = '<samp>';
				break;
			case E_WARNING:
			case E_CORE_WARNING:
			case E_COMPILE_WARNING:
			case E_USER_WARNING:
				$this->warnings ++;
				$error_or_warning = TRUE;
				$messagetype     = '<samp style="background-color:#ffc000;color:#fff">' . __( 'WARNING:', 'backwpup' ) . ' ';
				break;
			case E_ERROR:
			case E_PARSE:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
				$this->errors ++;
				$error_or_warning = TRUE;
				$messagetype     = '<samp style="background-color:red;color:#fff">' . __( 'ERROR:', 'backwpup' ) . ' ';
				break;
			case 8192: //E_DEPRECATED      comes with php 5.3
			case 16384: //E_USER_DEPRECATED comes with php 5.3
				$messagetype = '<samp>' . __( 'DEPRECATED:', 'backwpup' ) . ' ';
				break;
			case E_STRICT:
				$messagetype = '<samp>' . __( 'STRICT NOTICE:', 'backwpup' ) . ' ';
				break;
			case E_RECOVERABLE_ERROR:
				$messagetype = '<samp>' . __( 'RECOVERABLE ERROR:', 'backwpup' ) . ' ';
				break;
			default:
				$messagetype = '<samp>' . $args[ 0 ] . ": ";
				break;
		}

		$in_file = str_replace( str_replace( '\\', '/', ABSPATH ), '', str_replace( '\\', '/', $args[ 2 ] ) );

		//print message to cli
		if ( defined( 'STDIN' ) && defined( 'STDOUT' ) )
			fwrite( STDOUT, '[' . date_i18n( 'd-M-Y H:i:s' ) . '] ' . strip_tags( $messagetype ) . str_replace( '&hellip;', '...', strip_tags( $args[ 1 ] ) ) . PHP_EOL ) ;
		//log line
		$timestamp = '<span datetime="' . date_i18n( 'c' ) . '" title="[Type: ' . $args[ 0 ] . '|Line: ' . $args[ 3 ] . '|File: ' . $in_file . '|Mem: ' . size_format( @memory_get_usage( TRUE ), 2 ) . '|Mem Max: ' . size_format( @memory_get_peak_usage( TRUE ), 2 ) . '|Mem Limit: ' . ini_get( 'memory_limit' ) . '|PID: ' . self::get_pid() . '|Query\'s: ' . get_num_queries() . ']">[' . date_i18n( 'd-M-Y H:i:s' ) . ']</span> ';
		//ste last Message
		if ( $args[ 0 ] == E_NOTICE || $args[ 0 ] == E_USER_NOTICE )
			$this->lastmsg = $messagetype . htmlentities( $args[ 1 ], ENT_COMPAT , get_bloginfo( 'charset' ), FALSE ) . '</samp>';
		if ( $error_or_warning )
			$this->lasterrormsg = $messagetype . htmlentities( $args[ 1 ], ENT_COMPAT , get_bloginfo( 'charset' ), FALSE ) . '</samp>';
		//write log file
		file_put_contents( $this->logfile, $timestamp . $messagetype . htmlentities( $args[ 1 ], ENT_COMPAT , get_bloginfo( 'charset' ), FALSE ) . '</samp>' . PHP_EOL, FILE_APPEND );

		//write new log header
		if ( $error_or_warning ) {
			$found   = 0;
			$fd      = fopen( $this->logfile, 'r+' );
			$file_pos = ftell( $fd );
			while ( ! feof( $fd ) ) {
				$line = fgets( $fd );
				if ( stripos( $line, "<meta name=\"backwpup_errors\" content=\"" ) !== FALSE ) {
					fseek( $fd, $file_pos );
					fwrite( $fd, str_pad( "<meta name=\"backwpup_errors\" content=\"" . $this->errors . "\" />", 100 ) . PHP_EOL );
					$found ++;
				}
				if ( stripos( $line, "<meta name=\"backwpup_warnings\" content=\"" ) !== FALSE ) {
					fseek( $fd, $file_pos );
					fwrite( $fd, str_pad( "<meta name=\"backwpup_warnings\" content=\"" . $this->warnings . "\" />", 100 ) . PHP_EOL );
					$found ++;
				}
				if ( $found >= 2 )
					break;
				$file_pos = ftell( $fd );
			}
			fclose( $fd );
		}

		//write working data
		$this->update_working_data( $error_or_warning );

		//true for no more php error handling.
		return TRUE;
	}

	/**
	 *
	 * Write the Working data to display the process or that i can executes again
	 *
	 * @global wpdb $wpdb
	 * @param bool $must_write overwrite the only ever 1 sec writing
	 * @return bool true if working date written
	 */
	public function update_working_data( $must_write = FALSE ) {
		global $wpdb;
		/*  @var wpdb $wpdb */
		
		//to reduce server load
		if ( BackWPup_Option::get( 'cfg', 'jobwaittimems' ) > 0 && BackWPup_Option::get( 'cfg', 'jobwaittimems') <= 500000 )
			usleep( BackWPup_Option::get( 'cfg', 'jobwaittimems' ) );

		//only run every 1 sec.
		$time_to_update = microtime( TRUE ) - $this->timestamp_last_update;
		if ( ! $must_write && $time_to_update < 1 )
			return TRUE;

		//set execution time again
		@set_time_limit( 0 );
		
		//check free memory
		$this->need_free_memory( '10M' );

		//check MySQL connection to WordPress Database and reconnect if needed
		$res = $wpdb->query( 'SELECT 1' );
		if ( $res === FALSE )
			$wpdb->db_connect();

		//check if job already aborted
		if ( ! self::get_working_data( FALSE ) ) {
			//run job end if aborted
			if ( $this->step_working != 'END' )
				$this->end();

			return FALSE;
		}

		//calc sub step percent
		if ( $this->substeps_todo > 0 && $this->substeps_done > 0 )
			$this->substep_percent = round( $this->substeps_done / $this->substeps_todo * 100 );
		else
			$this->substep_percent = 1;
		$this->timestamp_last_update = microtime( TRUE );

		//write data to file
		file_put_contents( BackWPup::get_plugin_data( 'running_file' ), '<?php //'. serialize( $this ), LOCK_EX );

		return TRUE;
	}

	/**
	 *
	 * Called on job stop makes cleanup and terminates the script
	 *
	 */
	private function end() {

		$this->step_working = 'END';
		$this->substeps_todo = 1;

		//delete old logs
		if ( BackWPup_Option::get( 'cfg', 'maxlogs' ) ) {
			$logfilelist = array();
			if ( $dir = opendir( BackWPup_Option::get( 'cfg', 'logfolder' ) ) ) { //make file list
				while ( ( $file = readdir( $dir ) ) !== FALSE ) {
					if ( strstr( $file, 'backwpup_log_' ) && ( strstr( $file, '.html' ) ||  strstr( $file, '.html.gz' ) ) )
						$logfilelist[ ] = $file;
				}
				closedir( $dir );
			}
			if ( sizeof( $logfilelist ) > 0 ) {
				rsort( $logfilelist );
				$numdeltefiles = 0;
				for ( $i = BackWPup_Option::get( 'cfg', 'maxlogs' ); $i < sizeof( $logfilelist ); $i ++ ) {
					unlink( BackWPup_Option::get( 'cfg', 'logfolder' ) . $logfilelist[ $i ] );
					$numdeltefiles ++;
				}
				if ( $numdeltefiles > 0 )
					$this->log( sprintf( _n( 'One old log deleted', '%d old logs deleted', $numdeltefiles, 'backwpup' ), $numdeltefiles ), E_USER_NOTICE );
			}
		}

		//Display job working time
		if ( $this->errors > 0 )
			$this->log( sprintf( __( 'Job has ended with errors in %s seconds. You must resolve the errors for correct execution.', 'backwpup' ), current_time( 'timestamp' ) - $this->start_time, E_USER_ERROR ) );
		elseif ( $this->warnings > 0 )
			$this->log( sprintf( __( 'Job has done with warnings in %s seconds. Please resolve them for correct execution.', 'backwpup' ), current_time( 'timestamp' ) - $this->start_time, E_USER_WARNING ) );
		else
			$this->log( sprintf( __( 'Job done in %s seconds.', 'backwpup' ), current_time( 'timestamp' ) - $this->start_time, E_USER_NOTICE ) );

		//clean up temp
		if ( ! empty( $this->backup_file ) && is_file( BackWPup::get_plugin_data( 'TEMP' ) . $this->backup_file ) )
			unlink( BackWPup::get_plugin_data( 'TEMP' ) . $this->backup_file );
		if ( ! empty( $this->folder_list_file ) && is_file( $this->folder_list_file ) )
			unlink( $this->folder_list_file );
		if ( ! empty( $this->additional_files_to_backup ) ) {
			foreach ( $this->additional_files_to_backup as $additional_file ) {
				if ( $additional_file == BackWPup::get_plugin_data( 'TEMP' ) . basename( $additional_file ) && is_file( $additional_file ) )
					unlink( $additional_file );
			}
		}
		//delete running file
		delete_site_option( 'backwpup_working_job' );
		if ( is_file( BackWPup::get_plugin_data( 'running_file' ) ) )
			unlink( BackWPup::get_plugin_data( 'running_file' ) );

		//Update job options
		if ( ! empty( $this->job[ 'jobid' ] ) ) {
			$this->job[ 'lastruntime' ] = current_time( 'timestamp' ) - $this->start_time;
			BackWPup_Option::update( $this->job[ 'jobid' ], 'lastruntime', $this->job[ 'lastruntime' ] );
		}

		//write header info
		if ( is_writable( $this->logfile ) ) {
			$fd      = fopen( $this->logfile, 'r+' );
			$filepos = ftell( $fd );
			$found   = 0;
			while ( ! feof( $fd ) ) {
				$line = fgets( $fd );
				if ( stripos( $line, "<meta name=\"backwpup_jobruntime\"" ) !== FALSE ) {
					fseek( $fd, $filepos );
					fwrite( $fd, str_pad( "<meta name=\"backwpup_jobruntime\" content=\"" . $this->job[ 'lastruntime' ] . "\" />", 100 ) . PHP_EOL );
					$found ++;
				}
				if ( stripos( $line, "<meta name=\"backwpup_backupfilesize\"" ) !== FALSE ) {
					fseek( $fd, $filepos );
					fwrite( $fd, str_pad( "<meta name=\"backwpup_backupfilesize\" content=\"" . $this->backup_filesize . "\" />", 100 ) . PHP_EOL );
					$found ++;
				}
				if ( $found >= 2 )
					break;
				$filepos = ftell( $fd );
			}
			fclose( $fd );
		}
		//Restore error handler
		restore_exception_handler();
		restore_error_handler();
		@ini_set( 'log_errors', $this->temp[ 'PHP' ][ 'INI' ][ 'LOG_ERRORS' ] );
		@ini_set( 'error_log', $this->temp[ 'PHP' ][ 'INI' ][ 'ERROR_LOG' ] );
		@ini_set( 'display_errors', $this->temp[ 'PHP' ][ 'INI' ][ 'DISPLAY_ERRORS' ] );
		if ( $this->temp[ 'PHP' ][ 'ENV' ][ 'TEMPDIR' ] )
			@putenv('TMPDIR=' . $this->temp[ 'PHP' ][ 'ENV' ][ 'TEMPDIR' ] );
		//logfile end
		file_put_contents( $this->logfile, "</body>" . PHP_EOL . "</html>", FILE_APPEND );

		//Send mail with log
		$sendmail = FALSE;
		if ( $this->errors > 0 && $this->job[ 'mailerroronly' ] && $this->job[ 'mailaddresslog' ] )
			$sendmail = TRUE;
		if ( ! $this->job[ 'mailerroronly' ] && $this->job[ 'mailaddresslog' ] )
			$sendmail = TRUE;
		if ( $sendmail ) {
			//special subject
			$status   = __( 'SUCCESSFUL', 'backwpup' );
			$priority = 3; //Normal
			if ( $this->warnings > 0 ) {
				$status   = __( 'WARNING', 'backwpup' );
				$priority = 2; //High
			}
			if ( $this->errors > 0 ) {
				$status   = __( 'ERROR', 'backwpup' );
				$priority = 1; //Highest
			}

			$subject = sprintf( __( '[%3$s] BackWPup log %1$s: %2$s', 'backwpup' ), date_i18n( 'd-M-Y H:i', $this->start_time, TRUE ), esc_attr( $this->job[ 'name' ] ), $status );
			$headers = array();
			$headers[] = 'Content-Type: text/html; charset='. get_bloginfo( 'charset' );
			$headers[] = 'X-Priority: '.$priority;
			if ( ! empty( $this->job[ 'mailaddresssenderlog' ] ) )
				$headers[] = 'From: ' . $this->job[ 'mailaddresssenderlog' ];

			wp_mail( $this->job[ 'mailaddresslog' ], $subject, file_get_contents( $this->logfile ), $headers );
		}

		//remove restart cron
		wp_clear_scheduled_hook( 'backwpup_cron', array( 'id' => 'restart' ) );

		//remove shutdown action
		remove_action( 'shutdown', array( $this, 'shutdown' ) );

		//set done
		$this->substeps_done = 1;
		$this->steps_done[ ] = 'END';

		//run cleanup and check
		BackWPup_Cron::check_cleanup();

		exit();
	}

	/**
	 *
	 * Increase automatically the memory that is needed
	 *
	 * @param int|string $memneed of the needed memory
	 */
	public function need_free_memory( $memneed ) {

		//need memory
		$needmemory = @memory_get_usage( TRUE ) + self::convert_hr_to_bytes( $memneed );
		// increase Memory
		if ( $needmemory > self::convert_hr_to_bytes( ini_get( 'memory_limit' ) ) ) {
			$newmemory = round( $needmemory / 1024 / 1024 ) + 1 . 'M';
			if ( $needmemory >= 1073741824 )
				$newmemory = round( $needmemory / 1024 / 1024 / 1024 ) . 'G';
			@ini_set( 'memory_limit', $newmemory );
		}
	}


	/**
	 *
	 * Converts hr to bytes
	 *
	 * @param $size
	 * @return int
	 */
	public static function convert_hr_to_bytes( $size ) {
		$size  = strtolower( $size );
		$bytes = (int) $size;
		if ( strpos( $size, 'k' ) !== FALSE )
			$bytes = intval( $size ) * 1024;
		elseif ( strpos( $size, 'm' ) !== FALSE )
			$bytes = intval($size) * 1024 * 1024;
		elseif ( strpos( $size, 'g' ) !== FALSE )
			$bytes = intval( $size ) * 1024 * 1024 * 1024;
		return $bytes;
	}

	/**
	 *
	 * Callback for the CURLOPT_READFUNCTION that submit the transferred bytes
	 * to build the process bar
	 *
	 * @param $curl_handle
	 * @param $file_handle
	 * @param $read_count
	 * @return string
	 * @internal param $out
	 */
	public function curl_read_callback( $curl_handle, $file_handle, $read_count ) {

		$data = NULL;
		if ( ! empty( $file_handle ) && is_numeric( $read_count ) )
			$data = fread( $file_handle, $read_count );
		
		if (  $this->job[ 'backuptype' ] == 'sync'  )
			return $data;
		
		$length = ( is_numeric( $read_count ) ) ? $read_count : strlen( $read_count );
		$this->substeps_done = $this->substeps_done + $length;				
		$this->update_working_data();

		return $data;
	}


	/**
	 *
	 * Get the mime type of a file
	 *
	 * @param string $file The full file name
	 *
	 * @return bool|string the mime type or false
	 */
	public function get_mime_type( $file ) {

		if ( ! is_file( $file ) )
			return FALSE;

		if ( function_exists( 'fileinfo' ) ) {
			$finfo = finfo_open( FILEINFO_MIME_TYPE );

			return finfo_file( $finfo, $file );
		}

		if ( function_exists( 'mime_content_type' ) ) {
			return mime_content_type( $file );
		}

		$mime_types = array(
			'3gp'     => 'video/3gpp',
			'ai'      => 'application/postscript',
			'aif'     => 'audio/x-aiff',
			'aifc'    => 'audio/x-aiff',
			'aiff'    => 'audio/x-aiff',
			'asc'     => 'text/plain',
			'atom'    => 'application/atom+xml',
			'au'      => 'audio/basic',
			'avi'     => 'video/x-msvideo',
			'bcpio'   => 'application/x-bcpio',
			'bin'     => 'application/octet-stream',
			'bmp'     => 'image/bmp',
			'cdf'     => 'application/x-netcdf',
			'cgm'     => 'image/cgm',
			'class'   => 'application/octet-stream',
			'cpio'    => 'application/x-cpio',
			'cpt'     => 'application/mac-compactpro',
			'csh'     => 'application/x-csh',
			'css'     => 'text/css',
			'dcr'     => 'application/x-director',
			'dif'     => 'video/x-dv',
			'dir'     => 'application/x-director',
			'djv'     => 'image/vnd.djvu',
			'djvu'    => 'image/vnd.djvu',
			'dll'     => 'application/octet-stream',
			'dmg'     => 'application/octet-stream',
			'dms'     => 'application/octet-stream',
			'doc'     => 'application/msword',
			'dtd'     => 'application/xml-dtd',
			'dv'      => 'video/x-dv',
			'dvi'     => 'application/x-dvi',
			'dxr'     => 'application/x-director',
			'eps'     => 'application/postscript',
			'etx'     => 'text/x-setext',
			'exe'     => 'application/octet-stream',
			'ez'      => 'application/andrew-inset',
			'flv'     => 'video/x-flv',
			'gif'     => 'image/gif',
			'gram'    => 'application/srgs',
			'grxml'   => 'application/srgs+xml',
			'gtar'    => 'application/x-gtar',
			'gz'      => 'application/x-gzip',
			'hdf'     => 'application/x-hdf',
			'hqx'     => 'application/mac-binhex40',
			'htm'     => 'text/html',
			'html'    => 'text/html',
			'ice'     => 'x-conference/x-cooltalk',
			'ico'     => 'image/x-icon',
			'ics'     => 'text/calendar',
			'ief'     => 'image/ief',
			'ifb'     => 'text/calendar',
			'iges'    => 'model/iges',
			'igs'     => 'model/iges',
			'jnlp'    => 'application/x-java-jnlp-file',
			'jp2'     => 'image/jp2',
			'jpe'     => 'image/jpeg',
			'jpeg'    => 'image/jpeg',
			'jpg'     => 'image/jpeg',
			'js'      => 'application/x-javascript',
			'kar'     => 'audio/midi',
			'latex'   => 'application/x-latex',
			'lha'     => 'application/octet-stream',
			'lzh'     => 'application/octet-stream',
			'm3u'     => 'audio/x-mpegurl',
			'm4a'     => 'audio/mp4a-latm',
			'm4p'     => 'audio/mp4a-latm',
			'm4u'     => 'video/vnd.mpegurl',
			'm4v'     => 'video/x-m4v',
			'mac'     => 'image/x-macpaint',
			'man'     => 'application/x-troff-man',
			'mathml'  => 'application/mathml+xml',
			'me'      => 'application/x-troff-me',
			'mesh'    => 'model/mesh',
			'mid'     => 'audio/midi',
			'midi'    => 'audio/midi',
			'mif'     => 'application/vnd.mif',
			'mov'     => 'video/quicktime',
			'movie'   => 'video/x-sgi-movie',
			'mp2'     => 'audio/mpeg',
			'mp3'     => 'audio/mpeg',
			'mp4'     => 'video/mp4',
			'mpe'     => 'video/mpeg',
			'mpeg'    => 'video/mpeg',
			'mpg'     => 'video/mpeg',
			'mpga'    => 'audio/mpeg',
			'ms'      => 'application/x-troff-ms',
			'msh'     => 'model/mesh',
			'mxu'     => 'video/vnd.mpegurl',
			'nc'      => 'application/x-netcdf',
			'oda'     => 'application/oda',
			'ogg'     => 'application/ogg',
			'ogv'     => 'video/ogv',
			'pbm'     => 'image/x-portable-bitmap',
			'pct'     => 'image/pict',
			'pdb'     => 'chemical/x-pdb',
			'pdf'     => 'application/pdf',
			'pgm'     => 'image/x-portable-graymap',
			'pgn'     => 'application/x-chess-pgn',
			'pic'     => 'image/pict',
			'pict'    => 'image/pict',
			'png'     => 'image/png',
			'pnm'     => 'image/x-portable-anymap',
			'pnt'     => 'image/x-macpaint',
			'pntg'    => 'image/x-macpaint',
			'ppm'     => 'image/x-portable-pixmap',
			'ppt'     => 'application/vnd.ms-powerpoint',
			'ps'      => 'application/postscript',
			'qt'      => 'video/quicktime',
			'qti'     => 'image/x-quicktime',
			'qtif'    => 'image/x-quicktime',
			'ra'      => 'audio/x-pn-realaudio',
			'ram'     => 'audio/x-pn-realaudio',
			'ras'     => 'image/x-cmu-raster',
			'rdf'     => 'application/rdf+xml',
			'rgb'     => 'image/x-rgb',
			'rm'      => 'application/vnd.rn-realmedia',
			'roff'    => 'application/x-troff',
			'rtf'     => 'text/rtf',
			'rtx'     => 'text/richtext',
			'sgm'     => 'text/sgml',
			'sgml'    => 'text/sgml',
			'sh'      => 'application/x-sh',
			'shar'    => 'application/x-shar',
			'silo'    => 'model/mesh',
			'sit'     => 'application/x-stuffit',
			'skd'     => 'application/x-koan',
			'skm'     => 'application/x-koan',
			'skp'     => 'application/x-koan',
			'skt'     => 'application/x-koan',
			'smi'     => 'application/smil',
			'smil'    => 'application/smil',
			'snd'     => 'audio/basic',
			'so'      => 'application/octet-stream',
			'spl'     => 'application/x-futuresplash',
			'src'     => 'application/x-wais-source',
			'sv4cpio' => 'application/x-sv4cpio',
			'sv4crc'  => 'application/x-sv4crc',
			'svg'     => 'image/svg+xml',
			'swf'     => 'application/x-shockwave-flash',
			't'       => 'application/x-troff',
			'tar'     => 'application/x-tar',
			'tcl'     => 'application/x-tcl',
			'tex'     => 'application/x-tex',
			'texi'    => 'application/x-texinfo',
			'texinfo' => 'application/x-texinfo',
			'tif'     => 'image/tiff',
			'tiff'    => 'image/tiff',
			'tr'      => 'application/x-troff',
			'tsv'     => 'text/tab-separated-values',
			'txt'     => 'text/plain',
			'ustar'   => 'application/x-ustar',
			'vcd'     => 'application/x-cdlink',
			'vrml'    => 'model/vrml',
			'vxml'    => 'application/voicexml+xml',
			'wav'     => 'audio/x-wav',
			'wbmp'    => 'image/vnd.wap.wbmp',
			'wbxml'   => 'application/vnd.wap.wbxml',
			'webm'    => 'video/webm',
			'wml'     => 'text/vnd.wap.wml',
			'wmlc'    => 'application/vnd.wap.wmlc',
			'wmls'    => 'text/vnd.wap.wmlscript',
			'wmlsc'   => 'application/vnd.wap.wmlscriptc',
			'wmv'     => 'video/x-ms-wmv',
			'wrl'     => 'model/vrml',
			'xbm'     => 'image/x-xbitmap',
			'xht'     => 'application/xhtml+xml',
			'xhtml'   => 'application/xhtml+xml',
			'xls'     => 'application/vnd.ms-excel',
			'xml'     => 'application/xml',
			'xpm'     => 'image/x-xpixmap',
			'xsl'     => 'application/xml',
			'xslt'    => 'application/xslt+xml',
			'xul'     => 'application/vnd.mozilla.xul+xml',
			'xwd'     => 'image/x-xwindowdump',
			'xyz'     => 'chemical/x-xyz',
			'zip'     => 'application/zip'
		);

		$filesuffix = pathinfo($file, PATHINFO_EXTENSION);
		$suffix = strtolower( $filesuffix );
		if ( isset( $mime_types[ $suffix ] ) )
			return $mime_types[ $suffix ];

		return 'application/octet-stream';
	}


	/**
	 *
	 * Gifs back a array of files to backup in the selected folder
	 *
	 * @param string $folder the folder to get the files from
	 *
	 * @return array files to backup
	 */
	public function get_files_in_folder( $folder ) {
		
		$files = array();

		if ( ! is_dir( $folder ) ) {
			$this->log( sprintf( _x( 'Folder %s not exists', 'Folder name', 'backwpup' ), $folder ), E_USER_WARNING );
			return $files;
		}
		if ( ! is_readable( $folder ) ) {
			$this->log( sprintf( _x( 'Folder %s not readable', 'Folder name', 'backwpup' ), $folder ), E_USER_WARNING );
			return $files;
		}

		if ( $dir = opendir( $folder ) ) {
			while ( FALSE !== ( $file = readdir( $dir ) ) ) {
				if ( in_array( $file, array( '.', '..' ) ) )
					continue;
				foreach ( $this->exclude_from_backup as $exclusion ) { //exclude files
					$exclusion = trim( $exclusion );
					if ( FALSE !== stripos( $folder . $file, trim( $exclusion ) ) && ! empty( $exclusion ) )
						continue 2;
				}
				if ( $this->job[ 'backupexcludethumbs' ] && strpos( $folder, BackWPup_File::get_upload_dir() ) !== FALSE && preg_match( "/\-[0-9]{2,4}x[0-9]{2,4}\.(jpg|png|gif)$/i", $file ) )
					continue;
				if ( ! is_dir( $folder . $file ) && ! is_readable( $folder . $file ) )
					$this->log( sprintf( __( 'File "%s" is not readable!', 'backwpup' ), $folder . $file ), E_USER_WARNING );
				elseif ( is_link( $folder . $file ) )
					$this->log( sprintf( __( 'Link "%s" not followed.', 'backwpup' ), $folder . $file ), E_USER_WARNING );
				elseif ( is_file( $folder . $file ) ) {
					$files[ ] = $folder . $file;
					$this->count_files_in_folder ++;
					$this->count_filesize_in_folder = $this->count_filesize_in_folder + @filesize( $folder . $file );
				}
			}
			closedir( $dir );
		}

		return $files;
	}

	/**
	 * Creates the backup archive
	 */
	private function create_archive() {

		//load folders to backup
		$folders_to_backup = $this->get_folders_to_backup();

		$this->substeps_todo = $this->count_folder  + 1;
		
		//initial settings for restarts in archiving
		if ( ! isset( $this->steps_data[ $this->step_working ]['step_size'] ) )
			$this->steps_data[ $this->step_working ]['step_size'] = 0;
		if ( ! isset( $this->steps_data[ $this->step_working ]['done_files'] ) )
			$this->steps_data[ $this->step_working ]['done_files'] = array();
		if ( ! isset( $this->steps_data[ $this->step_working ]['folder_files'] ) )
			$this->steps_data[ $this->step_working ]['folder_files'] = array();

		if ( $this->substeps_done == 0 )
			$this->log( sprintf( __( '%d. Trying to create backup archive &hellip;', 'backwpup' ), $this->steps_data[ $this->step_working ][ 'STEP_TRY' ] ), E_USER_NOTICE );

		try {
			$backup_archive = new BackWPup_Create_Archive( $this->backup_folder . $this->backup_file );

			//show method for creation
			if ( $this->substeps_done == 0 )
				$this->log( sprintf( _x( 'Compression method is %s', 'Archive compression method', 'backwpup'), $backup_archive->get_method() ) );

			//add extra files
			if ( $this->substeps_done == 0 ) {
				if ( ! empty( $this->additional_files_to_backup ) && $this->substeps_done == 0 ) {
					foreach ( $this->additional_files_to_backup as $file ) {
						$backup_archive->add_file( $file, basename( $file ) );
						$this->count_files ++;
						$this->count_filesize = filesize( $file );
						$this->steps_data[ $this->step_working ]['step_size'] += filesize( $file );
						$this->update_working_data();
					}
				}
				$this->substeps_done ++;
			}
			
			//add normal files
			$jobrestartarchivesize = BackWPup_Option::get( 'cfg', 'jobrestartarchivesize' );
			for ( $i = $this->substeps_done - 1; $i < $this->substeps_todo - 1; $i ++ ) {
				$this->steps_data[ $this->step_working ]['folder_files'] = $this->get_files_in_folder( $folders_to_backup[ $i ] );
				//add empty folders
				if ( empty( $this->steps_data[ $this->step_working ]['done_files'] ) && empty( $this->steps_data[ $this->step_working ]['folder_files'] ) ) {
					$folder_name_in_archive = trim( ltrim( str_replace( $this->remove_path, '', $folders_to_backup[ $i ] ), '/' ) );
					if ( ! empty ( $folder_name_in_archive ) )
						$backup_archive->add_empty_folder( $folders_to_backup[ $i ], $folder_name_in_archive );
				}
				//add files
				if ( count( $this->steps_data[ $this->step_working ]['folder_files'] ) > 0 ) {
					foreach ( $this->steps_data[ $this->step_working ]['folder_files'] as $file ) {
						//restart if size reached in MB
						if ( ! empty( $jobrestartarchivesize ) && ! defined( 'STDIN' ) && $this->steps_data[ $this->step_working ]['step_size'] > $jobrestartarchivesize * 1024 * 1024 ) {
							$this->steps_data[ $this->step_working ]['step_size'] = 0;
							$this->steps_data[ $this->step_working ][ 'STEP_TRY' ] -= 1; //reduce step try because normal restart
							unset( $backup_archive );
							$this->do_restart();
						}
						//jump over files that are already archived
						if ( in_array( $file, $this->steps_data[ $this->step_working ]['done_files'] ) )
							continue;
						//generate filename in archive
						$in_archive_filename = ltrim( str_replace( $this->remove_path, '', $file ), '/' );
						//add file to archive
						$backup_archive->add_file( $file, $in_archive_filename );
						//count settings					
						$this->steps_data[ $this->step_working ]['done_files'][] = $file;
						$this->steps_data[ $this->step_working ]['step_size'] += filesize( $file );
						$this->update_working_data();
					}
				}
				$this->substeps_done ++;
				$this->steps_data[ $this->step_working ]['done_files'] = array();
			}
			$backup_archive->close();
			unset( $backup_archive );
			$this->log( __( 'Backup archive created.', 'backwpup' ), E_USER_NOTICE );
		} catch ( Exception $e ) {
			$this->log( $e->getMessage(), E_USER_ERROR, $e->getFile(), $e->getLine() );
			unset( $backup_archive );
			return FALSE;
		}

		$this->backup_filesize = filesize( $this->backup_folder . $this->backup_file );
		if ( $this->backup_filesize )
			$this->log( sprintf( __( 'Archive size is %s.', 'backwpup' ), size_format( $this->backup_filesize, 2 ) ), E_USER_NOTICE );
		$this->log( sprintf( __( '%1$d Files with %2$s in Archive.', 'backwpup' ), $this->count_files + $this->count_files_in_folder, size_format( $this->count_filesize + $this->count_filesize_in_folder, 2 ) ), E_USER_NOTICE );

		return TRUE;
	}

	/**
	 * @param        $name
	 * @param string $suffix
	 * @param bool   $delete_temp_file
	 * @return string
	 */
	public function generate_filename( $name, $suffix = '', $delete_temp_file = TRUE ) {

		$datevars   = array( '%d', '%j', '%m', '%n', '%Y', '%y', '%a', '%A', '%B', '%g', '%G', '%h', '%H', '%i', '%s', '%u', '%U' );
		$datevalues = array( date_i18n( 'd' ), date_i18n( 'j' ), date_i18n( 'm' ), date_i18n( 'n' ), date_i18n( 'Y' ), date_i18n( 'y' ), date_i18n( 'a' ), date_i18n( 'A' ), date_i18n( 'B' ), date_i18n( 'g' ), date_i18n( 'G' ), date_i18n( 'h' ), date_i18n( 'H' ), date_i18n( 'i' ), date_i18n( 's' ), date_i18n( 'u' ), date_i18n( 'U' ) );

		if ( ! empty( $suffix ) && substr( $suffix, 0, 1 ) != '.' )
			$suffix = '.' . $suffix;

		$name = str_replace( $datevars, $datevalues, $name );
		$name = sanitize_file_name( $name ) . $suffix; //prevent _ in extension name that sanitize_file_name add.
		if ( $delete_temp_file && is_file( BackWPup::get_plugin_data( 'TEMP' ) . $name ) )
			unlink( BackWPup::get_plugin_data( 'TEMP' ) . $name );

		return $name;
	}

	/**
	 * @param $filename
	 * @return bool
	 */
	public function is_backup_archive( $filename ) {

		$filename  = basename( $filename );
		
		if ( ! substr( $filename, -3 ) == '.gz' ||  ! substr( $filename, -4 ) == '.bz2' ||  ! substr( $filename, -4 ) == '.tar' ||  ! substr( $filename, -4 ) == '.zip' )
			return FALSE;
		
		$datevars  = array( '%d', '%j', '%m', '%n', '%Y', '%y', '%a', '%A', '%B', '%g', '%G', '%h', '%H', '%i', '%s', '%u', '%U' );
		$dateregex = array( '(0[1-9]|[12][0-9]|3[01])', '([1-9]|[12][0-9]|3[01])', '(0[1-9]|1[0-2])', '([1-9]|1[0-2])', '((19|20|21)[0-9]{2})', '([0-9]{2})', '(am|pm)', '(AM|PM)', '([0-9]{3})', '([0-9]|1[0-1])', '([0-9]|1[0-9]|2[0-3])', '(0[0-9]|1[0-1])', '(0[0-9]|1[0-9]|2[0-3])', '(0[0-9]|[1-5][0-9])', '(0[0-9]|[1-5][0-9])', '\d', '\d' );

		$regex = "/^" . str_replace( $datevars, $dateregex, str_replace( "\/", "/", $this->job[ 'archivename' ] ) . $this->job[ 'archiveformat' ] ) . "$/";

		preg_match( $regex, basename( $filename ), $matches );
		if ( ! empty( $matches[ 0 ] ) && $matches[ 0 ] == $filename )
			return TRUE;

		return FALSE;
	}

	/**
	 * Get the Process id of working script
	 *
	 * @return int
	 */
	private static function get_pid( ) {

		if  ( function_exists( 'posix_getpid' ) ) {

			return posix_getpid();
		} elseif ( function_exists( 'getmypid' ) ) {

			return getmypid();
		}

		return -1;
	}

	/**
	 * Add a Folder to list of Folders to Backup
	 *
	 * @param $folder
	 */
	public function add_folder_to_backup( $folder ) {

		if ( empty( $folder ) || empty( $this->folder_list_file ) || ! is_dir( $folder ) )
			return;

		if ( empty( $this->count_folder ) ) {
			if ( is_file( $this->folder_list_file ) )
				unlink( $this->folder_list_file );
			file_put_contents( $this->folder_list_file ,'<?php' . PHP_EOL .'$folders = array();', FILE_APPEND );
		}

		file_put_contents( $this->folder_list_file , PHP_EOL . '$folders[] = utf8_decode( \'' . addslashes( utf8_encode( $folder ) ) .'\' );', FILE_APPEND );

		$this->count_folder ++;
	}

	/**
	 * Get list of Folder for backup
	 *
	 * @return array folder list
	 */
	public function get_folders_to_backup( ) {

		$folders = array();

		if ( empty( $this->count_folder ) || empty( $this->folder_list_file ) || ! is_file( $this->folder_list_file ) )
			return $folders;

		//add memory if needed
		$this->need_free_memory( filesize( $this->folder_list_file ) * 2 );

		//get folders from file
		include $this->folder_list_file;

		//all folders only one time in list and sort
		$folders = array_unique( $folders );
		sort( $folders );
		//save count of folders again
		$this->count_folder = count( $folders );

		return $folders;
	}

	/**
	 * Check whether shell_exec has been disabled.
	 *
	 * @access public
	 * @static
	 * @return bool
	 */
	public static function is_shell_exec() {

		// Is function avail
		if ( ! function_exists( 'shell_exec' ) )
			return FALSE;

		// Is shell_exec disabled?
		if ( in_array( 'shell_exec', array_map( 'trim', explode( ',', @ini_get( 'disable_functions' ) ) ) ) )
			return FALSE;

		// Can we issue a simple echo command?
		if ( ! @shell_exec( 'echo backwpup' ) )
			return FALSE;

		return TRUE;

	}
}
