<?php

/**
 * Get Current URL
 */
if ( ! function_exists( 'get_current_url' ) ) :

	function get_current_url() {
		return str_replace( '/web/web/', '/web/', home_url( $_SERVER['REQUEST_URI'] ) );
	}

endif;


/**
 * Get Current Clean URL
 */
if ( ! function_exists( 'get_current_clean_url' ) ) :

	function get_current_clean_url() {
		$url_parts = parse_url( str_replace( '/web/web/', '/web/', home_url( $_SERVER['REQUEST_URI'] ) ) );

		return $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'];
	}

endif;