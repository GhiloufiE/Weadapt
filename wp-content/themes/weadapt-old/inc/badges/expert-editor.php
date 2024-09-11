<?php

/**
 * Badge 4 - Expert Editor!
 *
 * Granted to a user who is an Editor on the website (based on our permissions).
 *
 * For debuging use: error_log( print_r( $variable, true ) );
 */
if ( ! function_exists( 'badge_expert_editor_profile_update' ) ) :

	function badge_expert_editor_profile_update( $user_ID, $old_user_data, $userdata ) {

		// Check Badge ID
		if ( empty( $badge_ID = get_badge_id( 'expert-editor' ) ) ) return;

		$has_editor_role = false;

		foreach ( get_sites() as $blog ) {

			$user_roles = get_userdata( $user_ID )->roles;

			if (
				in_array( 'author', $user_roles ) || // Author (Editor)
				in_array( 'editor', $user_roles )    // Editor (Microsite Editor)
			) {
				$has_editor_role = true;
			}
		}

		restore_current_blog();

		if ( $has_editor_role ) {
			set_badge_id( $user_ID, $badge_ID );
		}
		else {
			delete_badge_id( $user_ID, $badge_ID );
		}
	}

endif;

add_action( 'profile_update', 'badge_expert_editor_profile_update', 10, 3 );
add_action( 'theme_profile_update', 'badge_expert_editor_profile_update', 10, 3 );