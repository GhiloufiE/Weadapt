<?php

/**
 * Template Redirect
 */
add_action( 'template_redirect', function() {

	// Frontend Redirect URL
	$frontend_redirect_url = get_theme_network_option( 'options_network_post_types_' . get_current_blog_id() . '_frontend_redirect_url' );

	if (
		! empty( $frontend_redirect_url ) &&
		! current_user_can( 'publish_posts' ) &&
		( ! defined( 'IS_DEVELOPMENT' ) || ! IS_DEVELOPMENT )
	) {
		wp_redirect( $frontend_redirect_url . $_SERVER['REQUEST_URI'], 301 );
	}


	// Author Page Fix
	global $wp_query;

	if ( ! empty( $wp_query->query_vars['author_name'] ) ) {
		global $authordata;

		if ( null === $authordata ) {
			$current_user_data = get_user_by( 'slug', esc_attr( $wp_query->query_vars['author_name'] ) );

			if ( ! empty( $current_user_data->data ) ) {
				$wp_query->queried_object    = $current_user_data;
				$wp_query->queried_object_id = $current_user_data->data->ID;

				$wp_query->is_404     = false;
				$wp_query->is_author  = true;
				$wp_query->is_archive = true;

				$authordata = $current_user_data;
			}
		}
	}


	// Pulish To Fix
	if (
		is_singular( 'post' ) ||
		( ( is_page_template( 'page-templates/profile.php' ) || is_page_template( 'page-templates/edit-profile.php' ) ) && ! is_user_logged_in() )
	) {
		wp_redirect( home_url( '/error-404' ), 301 );
	}

	if ( is_singular() ) {
		global $post;

		$publish_to = get_field( 'publish_to', $post->ID );

		if ( ! empty( $publish_to ) ) {
			if ( ! in_array( get_current_blog_id(), $publish_to ) ) {
				wp_redirect( home_url( '/error-404' ), 301 );
			}
		}
	}
} );