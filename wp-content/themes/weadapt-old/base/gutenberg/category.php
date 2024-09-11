<?php

/**
 * Register Gutenberg Categories
 */
add_filter( 'block_categories_all', function( $categories ) {
	$cat_array = [
		[
			'slug'  => 'theme_blocks',
			'title' => __( 'Theme Blocks', 'weadapt' ),
			'icon'  => 'star-filled',
		]
	];

	return array_merge( $cat_array, $categories );
}, 10, 2 );