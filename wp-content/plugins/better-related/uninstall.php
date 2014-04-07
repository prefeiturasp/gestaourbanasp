<?php
/**
 * Delete the better-related option
 *
 * @package better-related
 * @subpackage uninstall
 * @since 0.2.4
 */

// If uninstall/delete not called from WordPress then exit
if( ! defined ( 'ABSPATH' ) && ! defined ( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();

// Delete shadowbox option from options table
delete_option ( 'better-related' );

// @todo delete the post meta!
// @todo delete fulltext index?
// ALTER TABLE wp_posts DROP INDEX post_content_index
// ALTER TABLE wp_posts DROP INDEX post_title_index
