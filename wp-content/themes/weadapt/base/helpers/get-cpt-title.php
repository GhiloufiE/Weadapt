<?php

/**
 * Get Custom Post Type Title
 */
function get_cpt_title( $post_type_name = '' ) {
	$post_types = apply_filters( 'theme_cpt', array() );

	if ( ! empty( $post_types ) ) {
		foreach ( $post_types as $post_type ) {
			if ( $post_type['post_type'] === $post_type_name ) {
				return $post_type['multiple_name'];
			}
		}
	}

	return '';
}