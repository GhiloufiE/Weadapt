<?php

/**
 * Add Unique Key
 */
add_filter( 'render_block', function( $block_content, $block ) {
	if ( in_array( $block['blockName'], [ 'core/embed', 'core-embed/youtube', 'core-embed/wordpress' ] ) ) {
		$block_content = '<div data-key="core-embed"></div>' . $block_content;
	}

	return $block_content;
}, 10, 2 );


/**
 * Enqueue Blocks Assets
 */
add_action( 'enqueue_block_editor_assets', function() {
	$path = '/parts/gutenberg/core-embed';

	if( is_admin() ) {
		wp_enqueue_style(
			'core/embed',
			get_theme_file_uri( "$path/style-editor.css" ),
			[ 'wp-edit-blocks' ],
			filemtime( get_theme_file_path( "$path/style-editor.css" ) )
		);
	}
} );