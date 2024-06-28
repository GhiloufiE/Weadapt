<?php

/**
 * Get Blog Administrators
 */
function get_blog_administrators( $is_for_mail = false, $blog_ID = 0 ) {
	if ( empty( $blog_ID ) ) {
		$blog_ID = get_current_blog_id();
	}

	$blog_administrators = get_users( [
		'blog_id'     => $blog_ID,
		'exclude'     => [1],  // Exclude 'Developers' User
		'theme_query' => true, // multisite fix
		'role'        => 'administrator',
		'fields'      => 'ID'
	] );

	if ( $is_for_mail && ! empty( $blog_administrators ) ) {
		// If this is the development environment, we don't need to send emails to users.
		if ( defined( 'WP_ENV' ) && WP_ENV === 'development' ) {
			$blog_administrators = [1];
		}
	}

	return $blog_administrators;
}