<?php

/**
 * Get User Biographical Info
 */
function get_user_excerpt( $user_ID, $chars = 80 ) {
	$user_description = get_user_meta( $user_ID, 'description', true );

	if ( $chars === -1 ) {
		return $user_description;
	}
	else {
		return mb_strimwidth( wp_strip_all_tags( $user_description ), 0, $chars, '...' );
	}
}