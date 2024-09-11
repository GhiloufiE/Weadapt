<?php

/**
 * Badge 1 - Prolific Publisher!
 *
 * Granted to a user who published 5 articles on the website.
 *
 * For debuging use: error_log( print_r( $variable, true ) );
 */
if ( ! function_exists( 'badge_prolific_publisher_publish_post' ) ) :

	function badge_prolific_publisher_publish_post( $post ) {

		// Check Badge ID
		if ( empty( $badge_ID = get_badge_id( 'prolific-publisher' ) ) ) return;

		// Check Users
		if ( empty( $user_IDs = get_field( 'people_creator', $post->ID ) ) ) return;


		foreach ( $user_IDs as $user_ID ) {
			$user = new WP_User( $user_ID );

			// Check User
			if ( $user->exists() ) {
				$query = new WP_Query( [
					'post_type'      => [ 'article', 'blog', 'course', 'event', 'case-study' ],
					'post_status'    => [ 'publish' ],
					'posts_per_page' => 5,
					'fields'         => 'ids',
					'meta_query'     => [
						[
							'key'     => 'people_creator',
							'value'   => sprintf( ':"%d";', $user_ID ),
							'compare' => 'LIKE'
						],
					],
				] );

				if ( 5 === $query->found_posts ) {
					set_badge_id( $user_ID, $badge_ID );
				}
				else {
					delete_badge_id( $user_ID, $badge_ID );
				}
			}
		}
	}

endif;

add_action( 'new_to_publish', 'badge_prolific_publisher_publish_post', 50 );
add_action( 'pending_to_publish', 'badge_prolific_publisher_publish_post', 50 );
add_action( 'draft_to_publish', 'badge_prolific_publisher_publish_post', 50 );
add_action( 'auto-draft_to_publish', 'badge_prolific_publisher_publish_post', 50 );
add_action( 'future_to_publish', 'badge_prolific_publisher_publish_post', 50 );
add_action( 'private_to_publish', 'badge_prolific_publisher_publish_post', 50 );
add_action( 'trash_to_publish', 'badge_prolific_publisher_publish_post', 50 );