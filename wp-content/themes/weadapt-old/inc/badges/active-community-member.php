<?php

/**
 * Badge 8 - Active Community Member!
 *
 * Granted to a user who is a member of more than 5 themes or networks on the website.
 *
 * For debuging use: error_log( print_r( $variable, true ) );
 */
if ( ! function_exists( 'badge_active_community_member_join' ) ) :

	function badge_active_community_member_join( $user_ID, $post_ID, $type ) {

		// Check Badge ID
		if ( empty( $badge_ID = get_badge_id( 'active-community-member' ) ) ) return;

		// Check Type
		if ( ! in_array( $type, ['theme', 'network'] ) ) return;


		$user = new WP_User( $user_ID );

		// Check User
		if ( $user->exists() ) {
			$join_IDs = get_followed_posts( ['theme', 'network'], $user_ID );

			if ( count( $join_IDs ) >= 5 ) {
				set_badge_id( $user_ID, $badge_ID );
			}
			else {
				delete_badge_id( $user_ID, $badge_ID );
			}
		}
	}

endif;

add_action( 'theme_join', 'badge_active_community_member_join', 10, 3 );