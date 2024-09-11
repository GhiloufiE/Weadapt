<?php

/**
 * Get Post People
 */
function get_post_people( $post_ID = 0, $field = '', $is_for_mail = false ) {
	$post_people = [];

	if ( ! empty( $post_ID ) && ! empty( $field ) ) {
		$people = get_field( 'people', $post_ID );

		if ( ! empty( $people[$field] ) ) {
			$post_people = $people[$field];
		}

		if ( $is_for_mail && ! empty( $post_people ) ) {

			// If this is the development environment, we don't need to send emails to users.
			if ( defined( 'WP_ENV' ) && WP_ENV === 'development' ) {
				$post_people = [1];
			}
		}
	}

	return $post_people;
}