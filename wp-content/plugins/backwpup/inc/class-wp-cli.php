<?php
/**
 * Class for WP-CLI commands
 */
class BackWPup_WP_CLI extends WP_CLI_Command {

	/**
	 * Start a BackWPup job
	 *
	 * @param $args
	 * @param $assoc_args
	 * @synopsis start  --jobid=<ID of job to start>
	 */
	public function start( $args, $assoc_args ) {

		$job_object = BackWPup_Job::get_working_data( FALSE );
		if ( $job_object )
			WP_CLI::error( __( 'A job is already running.', 'backwpup' ) );

		if ( empty( $assoc_args['jobid'] ) )
			WP_CLI::error( __( 'No job ID specified!', 'backwpup' ) );

		$jobids = BackWPup_Option::get_job_ids();
		if ( ! in_array( $assoc_args['jobid'], $jobids ) )
			WP_CLI::error( __( 'Job ID does not exist!', 'backwpup' ) );

		BackWPup_Job::start_cli( $assoc_args['jobid'] );

	}

	/**
	 *  Abort a working BackWPup Job
	 *
	 *  @synopsis abort
	 */
	public function abort( $args, $assoc_args ) {

		$job_object = BackWPup_Job::get_working_data();
		if ( ! $job_object )
			WP_CLI::error( __( 'Nothing to abort!', 'backwpup' ) );
		
		delete_site_option( 'backwpup_working_job' );
		unlink( BackWPup::get_plugin_data( 'running_file' ) );
		
		if ( ! is_object( $job_object ) )
			WP_CLI::error( __( 'Running file can\'t read. tra again.', 'backwpup' ) );
		
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

		WP_CLI::success( __( 'Job will be terminated.', 'backwpup' ) ) ;
	}

	/**
	 * Display a List of Jobs
	 *
	 * @synopsis jobs
	 */
	public function jobs( $args, $assoc_args ) {

		$jobids = BackWPup_Option::get_job_ids();

		WP_CLI::line( __('List of jobs', 'backwpup' ) );
		WP_CLI::line( '----------------------------------------------------------------------' );
		foreach ($jobids as $jobid ) {
			WP_CLI::line( sprintf( __('ID: %1$d Name: %2$s', 'backwpup' ),$jobid, BackWPup_Option::get( $jobid, 'name' ) ) );
		}

	}

	/**
	 * See Status of a working job
	 *
	 * @param $args
	 * @param $assoc_args
	 * @synopsis working
	 */
	public function working( $args, $assoc_args ) {

		$job_object = BackWPup_Job::get_working_data();
		if ( ! $job_object )
			WP_CLI::error( __( 'No job running', 'backwpup' ) );
		if ( ! is_object( $job_object ) )
			WP_CLI::error( __( 'Running file can\'t read. tra again.', 'backwpup' ) );
		WP_CLI::line( __('Running job', 'backwpup' ) );
		WP_CLI::line( '----------------------------------------------------------------------' );
		WP_CLI::line( sprintf( __( 'ID: %1$d Name: %2$s', 'backwpup' ), $job_object->job[ 'jobid' ], $job_object->job[ 'name' ] ) );
		WP_CLI::line( sprintf( __( 'Warnings: %1$d Errors: %2$d', 'backwpup' ), $job_object->warnings , $job_object->errors ) );
		WP_CLI::line( sprintf( __( 'Steps in percent: %1$d percent of step: %2$d', 'backwpup' ), $job_object->step_percent, $job_object->substep_percent) );
		WP_CLI::line( sprintf( __( 'On step: %s', 'backwpup' ), $job_object->steps_data[ $job_object->step_working ][ 'NAME' ] ) );
		WP_CLI::line( sprintf( __( 'Last message: %s', 'backwpup' ), str_replace( '&hellip;', '...', strip_tags( $job_object->lastmsg ) ) ) );

	}

}
