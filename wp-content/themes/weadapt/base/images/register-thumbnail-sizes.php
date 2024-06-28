<?php

/**
 * Register Thumbnail Sizes
 */
add_action( 'after_setup_theme', function() {
	global $custom_settings;

	if ( isset( $custom_settings->thumbnails ) ) {
		foreach ( $custom_settings->thumbnails as $thumb ) {
			add_image_size(
				$thumb[0],
				$thumb[1],
				$thumb[2],
				$thumb[3]
			);
		}
	}
} );