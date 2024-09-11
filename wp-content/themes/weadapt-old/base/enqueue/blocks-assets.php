<?php

/**
 * Load Inline Styles
 */
if ( ! function_exists( 'load_inline_styles' ) ) :

	function load_inline_styles( $path, $key, $file_name = 'style' ) {
		global $styles_arr;
		global $filter_arr;

		$theme_info = wp_get_theme();
		$theme_root = get_theme_root();
		$replaces   = [ realpath( "$theme_root/$theme_info->stylesheet" ) ];

		if ( $theme_info->stylesheet !== $theme_info->template ) {
			$replaces[] = realpath( "$theme_root/$theme_info->template" );
		}

		if ( false !== realpath( $path ) ) {
			$path = str_replace( $replaces, '', realpath( $path ) );
		}

		$file = realpath( get_theme_file_path( "$path/$file_name.css" ) );

		if ( ! in_array( $key, $styles_arr ) && file_exists( $file ) && ! in_array( $key, $filter_arr ) ) {
			$style = file_get_contents( $file );

			if ( ! empty($style) ) {
				$style = check_styles_urls( $style );

				array_push( $styles_arr, $key );

				?><style><?php echo $style; ?></style><?php
			}
		}
	}

endif;


/**
 * Load Inline Dependencies
 */
if ( ! function_exists( 'load_inline_dependencies' ) ) :

	function load_inline_dependencies( $path = 'parts', $key = '', $file_name = 'style' ) {
		load_inline_styles( $path, $key, $file_name );
	}

endif;


/**
 * Load Inline Styles Plugin
 */
if ( ! function_exists( 'load_inline_styles_plugin' ) ) :

	function load_inline_styles_plugin( $key ) {
		load_inline_styles( '/assets/css/plugins/', $key, $key );
	}

endif;


/**
 * Load Inline Styles Shared
 */
if ( ! function_exists( 'load_inline_styles_shared' ) ) :

	function load_inline_styles_shared( $key ) {
		load_inline_styles( "/assets/css/shared/$key", $key );
	}

endif;


/**
 * Load Inline Styles Gutenberg Bloks
 */
add_filter( 'the_content', function( $content ) {
	preg_match_all( '<div data-key="(.+?)".+?\/div>', $content, $matches );

	$keys = array_unique( $matches[1] );

	if ( ! empty( $keys ) ) {
		foreach ( $keys as $key ) {
			ob_start();

			load_inline_styles( "/parts/gutenberg/$key", $key );

			$html = ob_get_clean();

			$key_el  = '/<div data-key="' . $key . '".+?\/div>/';
			$content = preg_replace( $key_el, $html, $content, 1 );
			$content = preg_replace( $key_el, '', $content );
		}
	}

	return $content;
} );