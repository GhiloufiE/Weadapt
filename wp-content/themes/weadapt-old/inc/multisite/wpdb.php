<?php

/**
 * Set Multisite Global WPDB Prefixes
 *
 * $wpdb->insert_id > 100:
 * - new multisite has new id ~ 8
 * - new post_meta has ID > 100
 */
function multisite_change_tax_table() {
	global $wpdb;

	// s($wpdb->insert_id);

	if (
		( empty( $wpdb->insert_id ) || $wpdb->insert_id > 100 ) &&
		$wpdb->prefix !== $wpdb->base_prefix
	) {

		// Terms
		$wpdb->terms              = $wpdb->base_prefix . 'terms';
		$wpdb->termmeta           = $wpdb->base_prefix . 'termmeta';
		$wpdb->term_taxonomy      = $wpdb->base_prefix . 'term_taxonomy';
		$wpdb->term_relationships = $wpdb->base_prefix . 'term_relationships';

		// Posts
		$wpdb->posts    = $wpdb->base_prefix . 'posts';
		$wpdb->postmeta = $wpdb->base_prefix . 'postmeta';

		// Comments
		$wpdb->comments    = $wpdb->base_prefix . 'comments';
		$wpdb->commentmeta = $wpdb->base_prefix . 'commentmeta';
	}
}

add_action( 'init', 'multisite_change_tax_table', 0 );
add_action( 'switch_blog', 'multisite_change_tax_table', 0 );


/**
 * Set Multisite Global Uploads Folder
 */
add_filter( 'upload_dir', function( $uploads ) {
	$uploads['baseurl'] = WP_CONTENT_URL . '/uploads';
	$uploads['basedir'] = WP_CONTENT_DIR . '/uploads';
	$uploads['path']    = $uploads['basedir'] . $uploads['subdir'];
	$uploads['url']     = $uploads['baseurl'] . $uploads['subdir'];

	return $uploads;
} );


/**
 * Filters the upload base directory to delete when the site is deleted.
 */
add_filter( 'wpmu_delete_blog_upload_dir', '__return_false' );