<?php

/**
 * Get User Full Name
 */
function get_user_name( $user_id ) {
	$user_data = get_userdata( $user_id );

	return ! empty( $user_data ) ? get_userdata( $user_id )->display_name : '';
}
