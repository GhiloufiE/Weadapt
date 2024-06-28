<?php

add_filter( 'cpt_archive_template_folder', function() {
	return 'grid';
} );

// Changed 'filter by' post types instead of categories (template Profile)
add_filter( 'show_post_types_instead_of_categories', function() {
	if ( get_page_id_by_template( 'profile' ) === get_queried_object_id() || is_author() ) {
		return true;
	}
} );

// Hide related content in single/blog/content.php
add_filter( 'show_related_single_blog_content', '__return_false' );

// Hide Categories Filter
add_filter( 'hide_categories_archive_filter', '__return_true' );


// Disabled google fonts, use custom instead.
add_filter( 'enqueue_google_fonts_url', '__return_false' );
add_filter( 'use_google_fonts', '__return_false' );

add_action( 'wp_enqueue_scripts', function() {
	$theme_path = str_replace( get_home_url( 1 ), get_home_url(), get_theme_file_uri() );

	$styles = "
	@font-face {
		font-family: 'NeueHaas Display';
		src: url('$theme_path/assets/fonts/NeueHaasGroteskDisplayPro65Medium.woff2') format('woff2'),
			 url('$theme_path/assets/fonts/NeueHaasGroteskDisplayPro65Medium.woff') format('woff');
		font-weight: 700;
		font-display: swap;
	}

	@font-face {
		font-family: 'NeueHaas';
		src: url('$theme_path/assets/fonts/NeueHaasGroteskTextPro65Medium.woff2') format('woff2'),
			 url('$theme_path/assets/fonts/NeueHaasGroteskTextPro65Medium.woff') format('woff');
		font-weight: 500;
		font-display: swap;
	}

	@font-face {
		font-family: 'NeueHaas';
		src: url('$theme_path/assets/fonts/NeueHaasGroteskTextPro55Roman.woff2') format('woff2'),
			 url('$theme_path/assets/fonts/NeueHaasGroteskTextPro55Roman.woff') format('woff');
		font-weight: 400;
		font-display: swap;
	}
	";

	wp_add_inline_style( 'main', $styles );
}, 50 );