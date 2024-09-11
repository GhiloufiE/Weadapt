<?php

/**
 * Enqueue Blocks Assets
 */
add_action( 'enqueue_block_editor_assets', function() {
	global $pagenow;

	$path = '/parts/gutenberg/custom-column';
	$deps = [ 'wp-blocks', 'wp-element', 'wp-i18n' ];

	if ( $pagenow !== 'widgets.php' ) {
		array_push( $deps, 'wp-editor' );
	}

	wp_register_script(
		'custom/column',
		get_theme_file_uri( "$path/admin.min.js" ),
		$deps,
		filemtime( get_theme_file_path( "$path/admin.min.js" ) )
	);

	register_block_type( 'custom/column', [
		'editor_script' => 'custom/column'
	] );
} );