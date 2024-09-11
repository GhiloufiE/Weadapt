<?php

/**
 * Add Unique Key
 */
add_filter( 'render_block', function( $block_content, $block ) {
	if ( 'core/quote' === $block['blockName'] ) {
		$block_content = '<div data-key="core-quote"></div>' . $block_content;
	}

	return $block_content;
}, 10, 2 );


/**
 * Enqueue Blocks Assets
 */
add_action( 'enqueue_block_editor_assets', function() {
	$path = '/parts/gutenberg/core-quote';

	if( is_admin() ) {
		wp_enqueue_style(
			'core/quote',
			get_theme_file_uri( "$path/style.css" ),
			[ 'wp-edit-blocks' ],
			filemtime( get_theme_file_path( "$path/style.css" ) )
		);
	}
} );