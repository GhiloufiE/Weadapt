<?php

/**
 * Store Custom Theme Settings File
 */
global $custom_settings;

$custom_settings = wp_json_file_decode( get_theme_file_path( '/custom-settings.json' ) );

if ( ! $custom_settings ) {
	add_action( 'admin_notices', function () {
		?><div class='error'><p><?php _e( 'custom-settings.json is invalid', 'weadapt' ); ?></p></div><?php
	} );
}


/**
 * Get Glob Folder Path
 */
if ( ! function_exists( 'get_glob_folders_path' ) ) :

	function get_glob_folders_path( $path = '' ) {
		$theme_info  = wp_get_theme();
		$theme_part  = ( $theme_info->stylesheet == $theme_info->template ) ? $theme_info->template : $theme_info->stylesheet . ',' . $theme_info->template;
		$file_paths  = glob( get_theme_root() . '/{' . $theme_part . '}' . $path, GLOB_BRACE );

		if ( ! empty( $file_paths ) ) {
			foreach ( $file_paths as &$file_path ) {
				$file_path = str_replace( [
					get_stylesheet_directory(),
					get_template_directory()
				], '', $file_path );
			}
		}

		return $file_paths;
	}

endif;


/**
 * Include All Base Files
 */
foreach ( get_glob_folders_path( '/base/*/*.php' ) as $file_path ) {
	require_once( get_theme_file_path( $file_path ) );
}