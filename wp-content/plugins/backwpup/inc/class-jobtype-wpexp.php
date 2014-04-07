<?php
/**
 *
 */
class BackWPup_JobType_WPEXP extends BackWPup_JobTypes {

	/**
	 *
	 */
	public function __construct() {

		$this->info[ 'ID' ]        	 = 'WPEXP';
		$this->info[ 'name' ]        = __( 'XML export', 'backwpup' );
		$this->info[ 'description' ] = __( 'WordPress XML export', 'backwpup' );
		$this->info[ 'URI' ]         = translate( BackWPup::get_plugin_data( 'PluginURI' ), 'backwpup' );
		$this->info[ 'author' ]      = BackWPup::get_plugin_data( 'Author' );
		$this->info[ 'authorURI' ]   = translate( BackWPup::get_plugin_data( 'AuthorURI' ), 'backwpup' );
		$this->info[ 'version' ]     = BackWPup::get_plugin_data( 'Version' );

	}

	/**
	 * @return bool
	 */
	public function creates_file() {

		return TRUE;
	}

	/**
	 * @return array
	 */
	public function option_defaults() {
		return array( 'wpexportcontent' => 'all', 'wpexportfilecompression' => '', 'wpexportfile' => sanitize_file_name( get_bloginfo( 'name' ) ) . '.wordpress.%Y-%m-%d' );
	}


	/**
	 * @param $jobid
	 * @internal param $main
	 */
	public function edit_tab( $jobid ) {
		?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php _e( 'Items to export', 'backwpup' ) ?></th>
				<td>
					<p><label for="idwpexportcontent-all"><input type="radio" name="wpexportcontent" id="idwpexportcontent-all" value="all" <?php checked( BackWPup_Option::get( $jobid, 'wpexportcontent' ), 'all' ); ?> /> <?php _e( 'All content', 'backwpup' ); ?></label></p>
					<p><label for="idwpexportcontent-posts"><input type="radio" name="wpexportcontent" id="idwpexportcontent-posts" value="posts" <?php checked( BackWPup_Option::get( $jobid, 'wpexportcontent' ), 'posts' ); ?> /> <?php _e( 'Posts', 'backwpup' ); ?></label></p>
					<p><label for="idwpexportcontent-pages"><input type="radio" name="wpexportcontent" id="idwpexportcontent-pages" value="pages" <?php checked( BackWPup_Option::get( $jobid, 'wpexportcontent' ), 'pages' ); ?> /> <?php _e( 'Pages', 'backwpup' ); ?></label></p>
					<?php
					foreach ( get_post_types( array( '_builtin' => FALSE, 'can_export' => TRUE ), 'objects' ) as $post_type ) {
						?>
						<p><label for="idwpexportcontent-<?php echo esc_attr( $post_type->name ); ?>"><input type="radio" name="wpexportcontent" id="idwpexportcontent-<?php echo esc_attr( $post_type->name ); ?>" value="<?php echo esc_attr( $post_type->name ); ?>" <?php checked( BackWPup_Option::get( $jobid, 'wpexportcontent' ), esc_attr( $post_type->name ) ); ?> /> <?php echo esc_html( $post_type->label ); ?></label></p>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="idwpexportfile"><?php _e( 'XML Export file name', 'backwpup' ) ?></label></th>
				<td>
					<input name="wpexportfile" type="text" id="idwpexportfile"
						   value="<?php echo BackWPup_Option::get( $jobid, 'wpexportfile' );?>"
						   class="medium-text code"/>.xml
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'File compression', 'backwpup' ) ?></th>
				<td>
					<?php
					echo '<label for="idwpexportfilecompression"><input class="radio" type="radio"' . checked( '', BackWPup_Option::get( $jobid, 'wpexportfilecompression' ), FALSE ) . ' name="wpexportfilecompression" id="idwpexportfilecompression" value="" /> ' . __( 'none', 'backwpup' ). '</label><br />';
					if ( function_exists( 'gzopen' ) )
						echo '<label for="idwpexportfilecompression-gz"><input class="radio" type="radio"' . checked( '.gz', BackWPup_Option::get( $jobid, 'wpexportfilecompression' ), FALSE ) . ' name="wpexportfilecompression" id="idwpexportfilecompression-gz" value=".gz" /> ' . __( 'GZip', 'backwpup' ). '</label><br />';
					else
						echo '<label for="idwpexportfilecompression-gz"><input class="radio" type="radio"' . checked( '.gz', BackWPup_Option::get( $jobid, 'wpexportfilecompression' ), FALSE ) . ' name="wpexportfilecompression" id="idwpexportfilecompression-gz" value=".gz" disabled="disabled" /> ' . __( 'GZip', 'backwpup' ). '</label><br />';
					if ( function_exists( 'bzopen' ) )
						echo '<label for="idwpexportfilecompression-bz2"><input class="radio" type="radio"' . checked( '.bz2', BackWPup_Option::get( $jobid, 'wpexportfilecompression' ), FALSE ) . ' name="wpexportfilecompression" id="idwpexportfilecompression-bz2" value=".bz2" /> ' . __( 'BZip2', 'backwpup' ). '</label><br />';
					else
						echo '<label for="idwpexportfilecompression-bz2"><input class="radio" type="radio"' . checked( '.bz2', BackWPup_Option::get( $jobid, 'wpexportfilecompression' ), FALSE ) . ' name="wpexportfilecompression" id="idwpexportfilecompression-bz2" value=".bz2" disabled="disabled" /> ' . __( 'BZip2', 'backwpup' ). '</label><br />';
					?>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * @param $id
	 */
	public function edit_form_post_save( $id ) {

		BackWPup_Option::update( $id, 'wpexportcontent', $_POST[ 'wpexportcontent' ] );
		BackWPup_Option::update( $id, 'wpexportfile', $_POST[ 'wpexportfile' ] );
		if ( $_POST[ 'wpexportfilecompression' ] == '' || $_POST[ 'wpexportfilecompression' ] == '.gz' || $_POST[ 'wpexportfilecompression' ] == '.bz2' )
			BackWPup_Option::update( $id, 'wpexportfilecompression', $_POST[ 'wpexportfilecompression' ] );
	}

	/**
	 * @param $job_object
	 * @return bool
	 */
	public function job_run( $job_object ) {

		$job_object->substeps_todo = 1;

		$job_object->log( sprintf( __( '%d. Trying to create a WordPress export to XML file&#160;&hellip;', 'backwpup' ), $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] ) );
		//build filename
		if ( empty( $job_object->temp[ 'wpexportfile' ] ) )
			$job_object->temp[ 'wpexportfile' ] = $job_object->generate_filename( $job_object->job[ 'wpexportfile' ], 'xml' ) . $job_object->job[ 'wpexportfilecompression' ];

		//include WP export function
		require_once ABSPATH . 'wp-admin/includes/export.php';
		while (@ob_end_clean());
		ob_start( array( $this, 'wp_export_ob_bufferwrite' ), 1048576 ); //start output buffering
		$args = array(
			'content' =>  $job_object->job[ 'wpexportcontent' ]
		);
		@export_wp( $args ); //WP export
		@ob_flush(); //send rest of data
		@ob_end_clean(); //End output buffering

		//add XML file to backup files
		if ( is_readable( BackWPup::get_plugin_data( 'TEMP' ) . $job_object->temp[ 'wpexportfile' ] ) ) {
			$job_object->additional_files_to_backup[ ] = BackWPup::get_plugin_data( 'TEMP' ) . $job_object->temp[ 'wpexportfile' ];
			$job_object->count_files ++;
			$job_object->count_filesize = $job_object->count_filesize + @filesize( BackWPup::get_plugin_data( 'TEMP' ) . $job_object->temp[ 'wpexportfile' ] );
			$job_object->log( sprintf( __( 'Added XML export "%1$s" with %2$s to backup file list.', 'backwpup' ), $job_object->temp[ 'wpexportfile' ], size_format( filesize( BackWPup::get_plugin_data( 'TEMP' ) . $job_object->temp[ 'wpexportfile' ] ), 2 ) ) );
		}
		$job_object->substeps_done = 1;

		return TRUE;
	}

	/**
	 *
	 * Helper for wp-export()
	 *
	 * @param $output
	 */
	public function wp_export_ob_bufferwrite( $output ) {

		$job_object = BackWPup_Job::getInstance();
		if ( $job_object->job[ 'pluginlistfilecompression' ] == '.gz' )
			file_put_contents(  'compress.zlib://' . BackWPup::get_plugin_data( 'TEMP' ) . $job_object->temp[ 'wpexportfile' ], $output, FILE_APPEND );
		elseif ( $job_object->job[ 'pluginlistfilecompression' ] == '.bz2' )
			file_put_contents(  'compress.bzip2://' . BackWPup::get_plugin_data( 'TEMP' ) . $job_object->temp[ 'wpexportfile' ], $output, FILE_APPEND );
		else
			file_put_contents( BackWPup::get_plugin_data( 'TEMP' ) . $job_object->temp[ 'wpexportfile' ], $output, FILE_APPEND );
		$job_object->update_working_data();
	}
}
