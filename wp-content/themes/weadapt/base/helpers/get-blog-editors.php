<?php

/**
 * Get Blog Editors
 */
function get_blog_editors( $is_for_mail = false ) {
	$blog_administrators = get_users( [
		'exclude'     => [1],  // Exclude 'Developers' User
		'theme_query' => true, // multisite fix
		'role'        => 'editor',
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