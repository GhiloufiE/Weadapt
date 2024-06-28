<?php

/**
 * Get Articles / Case Studies Count
 */
if ( ! function_exists( 'get_post_meta_count' ) ) :

	function get_post_meta_count( $post_ID = 0, $arg_post_types = [], $singular_text = 'Article', $plural_text = 'Articles' ) {
		$post_ID   = ! empty( $post_ID ) ? $post_ID : get_the_ID();
		$post_types = ! empty( $arg_post_types ) ? $arg_post_types : [ 'article', 'blog', 'course', 'event' ];
		$post_type = get_post_type( $post_ID );

		$args = array(
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'post_type'      => get_allowed_post_types( $post_types ),
			'fields'         => 'ids',
			'meta_query'     => [ [
				'key'   => 'relevant_main_theme_network',
				'value' => $post_ID,
			] ],
			'ignore_sticky_posts' => true,
			'theme_query'         => true, // multisite fix
		);

		if ( $post_type === 'organisation' ) {
			$args['meta_query'] = [
				'key'      => 'relevant_organizations',
				'value'    => sprintf( ':"%d";', $post_ID ),
				'compare'  => 'LIKE'
			];
		}

		if ( in_array( 'forum', $post_types ) && count( $post_types ) === 1 ) {
			$args['meta_query'] = [
				'key'      => 'forum',
				'value'    => $post_ID
			];
		}

		$query = new WP_Query( $args );
		$count = $query->post_count;

		return sprintf( _n( "%s $singular_text", "%s $plural_text", $count, 'weadapt' ), $count );
	}

endif;