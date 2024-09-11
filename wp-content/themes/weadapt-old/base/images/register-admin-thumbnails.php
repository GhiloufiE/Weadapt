<?php

/**
 * Exclude sizes
 */
if ( ! function_exists( 'exclude_sizes' ) ) :

	function exclude_sizes($string) {
		$disallowed_sizes = [ 'logo' ];

		foreach( $disallowed_sizes as $disallowed_size ) {
			if ( stripos( $string, $disallowed_size ) !== false ) {
				return false;
			}
		}

		return true;
	}

endif;


/**
 * Pass Sizes
 */
add_filter( 'image_size_names_choose', function( $sizes ) {
	$all_sizes    = [];
	$custom_sizes = get_intermediate_image_sizes();
	$custom_sizes = array_filter( $custom_sizes, 'exclude_sizes' );

	foreach ( $custom_sizes as $value) {
		$all_sizes[$value] = $value;
	}

	$all_sizes = array_merge( $all_sizes, $sizes );

	return $all_sizes;
}, 11, 1 );
