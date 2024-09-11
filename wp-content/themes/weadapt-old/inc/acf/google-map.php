<?php

/*
 * Google Maps API Key
 */
add_filter('acf/fields/google_map/api', function( $api ) {
	if ( ! empty( $google_maps_api_key = get_field( 'google_maps_api_key', 'options' ) ) ) {
		$api['key'] = esc_attr( $google_maps_api_key );
	}

	return $api;
} );