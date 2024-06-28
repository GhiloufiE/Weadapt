<?php

/**
 * Badge 3 - Passionate Learner!
 *
 * Granted to a user who downloaded/read 5 articles from the website.
 *
 * For debuging use: error_log( print_r( $variable, true ) );
 */
if ( ! function_exists( 'badge_passionate_learner_post_views' ) ) :

	function badge_passionate_learner_post_views() {

		// Check Logged In User
		if ( ! is_user_logged_in() ) return;

		// Check Badge ID
		if ( empty( $badge_ID = get_badge_id( 'passionate-learner' ) ) ) return;

		// Check is Post
		if ( ! is_singular( ['article', 'blog', 'course', 'event', 'case-study'] ) ) return;

		// Check Post/User IDs
		if ( empty( $post_ID = get_queried_object_id() ) || empty( $user_ID = get_current_user_id() ) ) return;

		// Check Post Revision
		if ( wp_is_post_revision( $post_ID ) ) return;


		$viewed       = [];
		$views_cookie = '';

		if ( ! empty( $_COOKIE[ USER_COOKIE . '_last_viewed' ] ) ) {
			$views_cookie = $_COOKIE[ USER_COOKIE . '_last_viewed' ];
		}

		if ( ! empty( $views_cookie ) ) {
			$viewed = array_map( 'intval', explode( ',', $views_cookie ) );

			if ( count( $viewed ) >= 5 ) {
				$viewed = array_slice( $viewed, count( $viewed ) - 5 );
			}
		}

		// Check if Exist
		if ( ! empty( $views_cookie ) && in_array( $post_ID, $viewed ) ) return;

		$viewed[] = $post_ID;

		setcookie(
			USER_COOKIE . '_last_viewed',
			implode( ',', $viewed ),
			[
				'expires'  => strtotime( "+1 month" ),
				'path'     => COOKIEPATH,
				'domain'   => COOKIE_DOMAIN,
				'secure'   => is_ssl(),
				'httponly' => true,
				'samesite' => 'Strict'
			]
		);

		if ( count( $viewed ) >= 5 ) {
			set_badge_id( $user_ID, $badge_ID );
		}

	}

endif;

add_action( 'template_redirect', 'badge_passionate_learner_post_views', 200 );