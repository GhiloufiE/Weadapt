<?php

/**
 * Get Theme Network Option
 */
function get_theme_network_option( $option = '' ) {
	global $wpdb;

	$cache_key = "acf:$option";
	$value     = wp_cache_get( $cache_key, 'site-options' );

	if ( ! isset( $value ) || false === $value ) {
		$options = $wpdb->base_prefix . 'options';
		$row     = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM $options WHERE option_name = %s", $option ) );

		if ( is_object( $row ) ) {
			$value = $row->option_value;
			$value = maybe_unserialize( $value );

			wp_cache_set( $cache_key, $value, 'site-options' );
		}
	}

	return $value;
}


/**
 * Get Theme Network Post Types
 */
function get_theme_network_post_types() {
	$post_types = get_theme_network_option( 'options_network_post_types_' . get_current_blog_id() . '_post_types' );

	if ( ! empty( $post_types ) ) {
		return $post_types;
	}

	return [];
}

/**
 * Get Allowed Post Types
 */
function get_allowed_post_types( $post_types ) {
	return array_intersect( $post_types, get_theme_network_post_types() );
}