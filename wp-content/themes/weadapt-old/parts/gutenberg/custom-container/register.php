<?php

/**
 * Enqueue Blocks Assets
 */
add_action( 'enqueue_block_editor_assets', function() {
	global $pagenow;

	$path = '/parts/gutenberg/custom-container';
	$deps = [ 'wp-blocks', 'wp-element', 'wp-i18n' ];

	if ( $pagenow !== 'widgets.php' ) {
		array_push( $deps, 'wp-editor' );
	}

	wp_register_script(
		'custom/container',
		get_theme_file_uri( "$path/admin.min.js" ),
		$deps,
		filemtime( get_theme_file_path( "$path/admin.min.js" ) )
	);

	if( is_admin() ) :
		wp_register_style(
			'custom/container-editor-styles',
			get_theme_file_uri( "$path/style-editor.css" ),
			[ 'wp-edit-blocks' ],
			filemtime( get_theme_file_path( "$path/style-editor.css" ) )
		);
	endif;

	register_block_type( 'custom/container', [
		'editor_script' => 'custom/container',
		'editor_style'  => 'custom/container-editor-styles'
	] );
} );