<?php

/**
 * Badge 5 - Avid Explorer!
 *
 * Granted to a user who spends the most time on the site (20 most frequently logged in?).
 *
 * For debuging use: error_log( print_r( $variable, true ) );
 */
if ( ! function_exists( 'badge_avid_explorer_login' ) ) :

	function badge_avid_explorer_login( $user_login, $user ) {
		update_user_meta( $user->ID, 'last_login', time() );
	}

endif;

add_action( 'wp_login', 'badge_avid_explorer_login', 10, 2 );


if ( ! function_exists( 'badge_avid_explorer_logged_in' ) ) :

	function badge_avid_explorer_logged_in() {

		// Check Logged In User
		if ( ! is_user_logged_in() ) return;

		// Check Badge ID
		if ( empty( $badge_ID = get_badge_id( 'avid-explorer' ) ) ) return;

		// Check User ID
		if ( empty( $user_ID = get_current_user_id() ) ) return;

		// Check Last Login
		if ( empty( $last_login = get_user_meta( $user_ID, 'last_login', true ) ) ) return;

		$avid_explorer_time = 60 * 60 * 24 * 3; // 5 days
		$time_diff          = time() - $last_login;

		// Check Time Diff
		if ( $time_diff < $avid_explorer_time ) return;


		$frequently_logged_in = get_user_meta( $user_ID, 'frequently_logged_in', true );

		if ( empty( $frequently_logged_in ) ) {
			$frequently_logged_in = [];
		}

		$frequently_logged_in[] = $time_diff;

		if ( count( $frequently_logged_in ) > 20 ) {
			$frequently_logged_in = array_slice( $frequently_logged_in, count( $frequently_logged_in ) - 20 );
		}

		update_user_meta( $user_ID, 'last_login', 0 );
		update_user_meta( $user_ID, 'frequently_logged_in', $frequently_logged_in );

		if ( count( $frequently_logged_in ) >= 20 ) {
			set_badge_id( $user_ID, $badge_ID );
		}
	}

endif;

add_action( 'template_redirect', 'badge_avid_explorer_logged_in', 200 );