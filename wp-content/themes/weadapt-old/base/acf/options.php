<?php

/**
 * ACF Options Pages
 */
add_action( 'acf/init', function () {
	if ( function_exists( 'acf_add_options_page' ) ) {

		// Theme Settings
		acf_add_options_page( array(
			'page_title' 	=> 'Theme Settings',
			'menu_title'	=> 'Theme Settings',
			'menu_slug' 	=> 'theme-general-settings',
			'capability'	=> 'manage_options',
			'redirect'		=> false
		) );

		// Network Settings
		if ( get_current_blog_id() == 1 ) {
			acf_add_options_page( array(
				'page_title' 	=> 'Network Settings',
				'menu_title'	=> 'Network Settings',
				'menu_slug' 	=> 'network-general-settings',
				'capability'	=> 'manage_network',
				'redirect'		=> false
			) );
		}
	}
} );


/**
 * ACF set capability
 */
add_filter( 'acf/settings/capability', function( $show ) {
	return current_user_can( 'manage_network' );
} );