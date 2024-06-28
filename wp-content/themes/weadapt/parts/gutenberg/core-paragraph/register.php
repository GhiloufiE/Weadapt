<?php

/**
 * Add Unique Key
 */
add_filter( 'render_block', function( $block_content, $block ) {
	if ( 'core/paragraph' === $block['blockName'] ) {
		$block_content = '<div data-key="core-paragraph"></div>' . $block_content;
	}

	return $block_content;
}, 10, 2 );


/**
 * Enqueue Blocks Assets
 */
add_action( 'enqueue_block_editor_assets', function() {
	global $pagenow;

	$path = '/parts/gutenberg/core-paragraph';
	$deps = [ 'wp-i18n', 'wp-dom-ready', 'wp-blocks', 'wp-compose', 'wp-element', 'wp-hooks' ];

	if ( $pagenow !== 'widgets.php' ) {
		array_push( $deps, 'wp-editor' );
	}

	if( is_admin() ) {
		wp_enqueue_script(
			'core/paragraph',
			get_theme_file_uri( "$path/admin.min.js" ),
			$deps,
			filemtime( get_theme_file_path( "$path/admin.min.js" ) )
		);
	}
} );