<?php

/**
 * Load Blocks Script
 */
if ( ! function_exists( 'load_blocks_script' ) ) :

	function load_blocks_script( $path, $name, $deps = [], $base = 'parts/components' ) {
		add_action( 'wp_footer', function() use ( $path, $name, $deps, $base ) {
			wp_enqueue_script(
				$name,
				get_theme_file_uri( "/$base/$path/index.min.js" ),
				$deps,
				filemtime( get_theme_file_path( "/$base/$path/index.min.js" ) ),
				true
			);
		}, 1);
	}

endif;