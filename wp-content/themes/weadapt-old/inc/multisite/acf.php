<?php


/**
 * Set json path from parent theme
 */
add_filter('acf/settings/load_json', function( $paths ) {
	unset( $paths[0] );

	$paths[] = get_parent_theme_file_path( 'acf-json' );

	return $paths;
});