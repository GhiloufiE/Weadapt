<?php

/**
 * Add Theme Support
 */
add_action( 'after_setup_theme', function() {
	add_theme_support( 'html5', [ 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'script', 'style' ] );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-font-sizes', [
		[
			'name' => __( 'Normal', 'weadapt' ),
			'size' => 16,
			'slug' => 'normal'
		],
		[
			'name' => __( 'Large', 'weadapt' ),
			'size' => 24,
			'slug' => 'large'
		]
	] );
	add_editor_style( 'assets/css/style-editor.css' );
	add_theme_support( 'editor-color-palette', settings_colors() );
} );


/**
 * Remove Gutenberg SVG Icons
 */
add_action( 'after_setup_theme', function() {
	remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
	remove_action( 'in_admin_header', 'wp_global_styles_render_svg_filters' );
}, 10, 0);


/**
 * Css Js Type Validator Fix
 */
add_action( 'template_redirect', function(){
	ob_start( function( $buffer ){
		$buffer = str_replace(' />', '>', $buffer);

		return $buffer;
	} );
} );


/**
 * Disable the Emoji's
 */
add_action( 'init', function() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

	// Remove from TinyMCE
	add_filter( 'tiny_mce_plugins', function( $plugins ) {
		if ( is_array( $plugins ) ) {
			return array_diff( $plugins, [ 'wpemoji' ] );
		}
		else {
			return [];
		}
	} );
} );


/**
 * Add Gutenberg Colors
 */
function settings_colors( $format = 'support' ) {
	$colors = wp_json_file_decode( get_theme_file_path( '/colors.json' ) );

	if ( $format === 'support' ) {
		$arr = [];

		foreach ( $colors as $key => $color ) {
			$slug = strtolower( trim( preg_replace( '/[^A-Za-z0-9-]+/', '-', $key ) ) );

			$arr[] = [
				'name'  => __( $key, 'weadapt' ),
				'slug'  => $slug,
				'color' => $color,
			];
		}

		return $arr;
	}
	else if ( $format === 'styles' ) {
		$html = '<style>';

		foreach ( $colors as $key => $color ) {
			$slug = strtolower( trim( preg_replace( '/[^A-Za-z0-9-]+/', '-', $key ) ) );
			$html .= ".has-$slug-color{color: var(--color--$slug);}";
			$html .= ".has-$slug-background-color{background-color:var(--color--$slug);}";
		}

		$html .= '</style>';

		echo $html;
	}
}

/**
 * Show Reusable Blocks in Menu
 */
add_action( 'admin_menu', function () {
	if ( is_super_admin() ) {
		add_menu_page( __( 'Reusable Blocks', 'weadapt' ), __( 'Reusable Blocks', 'weadapt' ), 'edit_posts', 'edit.php?post_type=wp_block', '', 'dashicons-controls-repeat', 22 );
	}
} );