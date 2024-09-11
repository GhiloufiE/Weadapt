<?php

/**
 * Register Nav Menus
 */
add_action( 'init', function() {
	global $custom_settings;

	if ( isset( $custom_settings->menus ) ) {
		$nav_array = array();

		foreach ( $custom_settings->menus as $key => $value ) {
			$nav_array[ $key ] = $value;
		}

		register_nav_menus( $nav_array );
	}
}, 0 );