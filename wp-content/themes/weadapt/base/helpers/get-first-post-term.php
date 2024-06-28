<?php

/**
 * Get First Post Term
 */
if ( ! function_exists( 'get_first_post_term' ) ) :

	function get_first_post_term( $post_ID = 0 ) {
		if ( empty( $post_ID ) ) {
			return [];
		}

		$terms = wp_get_object_terms( $post_ID, 'tags', [
			'orderby'     => 'none',
			'theme_query' => true // multisite fix
		] );

		$term  = [];

		if ( $terms ) {
			$term  = array_shift( $terms );
		}

		return $term;
	}

endif;