<?php

/**
 * Badge 7 - Conversationalist!
 *
 * Granted to a user who has made more than 5 comments.
 *
 * For debuging use: error_log( print_r( $variable, true ) );
 */
if ( ! function_exists( 'badge_conversationalist_change_comment' ) ) :

	function badge_conversationalist_change_comment( $comment_id, $comment_data ) {

		// Check Badge ID
		if ( empty( $badge_ID = get_badge_id( 'conversationalist' ) ) ) return;

		$comment = get_comment( $comment_id );
		$user_ID = ! empty( $comment->user_id ) ? $comment->user_id : 0;

		if ( ! empty( $comment->user_id ) ) {
			$user = new WP_User( $user_ID );

			// Check User
			if ( $user->exists() ) {
				$comments_count = get_comments( [
					'author__in' => [$user_ID],
					'count'      => true,
					'status'     => 'approve'
				] );

				if ( $comments_count >= 5 ) {
					set_badge_id( $user_ID, $badge_ID );
				}
				else {
					delete_badge_id( $user_ID, $badge_ID );
				}
			}
		}
	}

endif;

add_action( 'wp_set_comment_status', 'badge_conversationalist_change_comment', 10, 2 );
add_action( 'edit_comment', 'badge_conversationalist_change_comment', 10, 2 );
add_action( 'deleted_comment', 'badge_conversationalist_change_comment', 10, 2 );
add_action( 'wp_insert_comment', 'badge_conversationalist_change_comment', 10, 2 );
add_action( 'trashed_comment', 'badge_conversationalist_change_comment', 10, 2 );
add_action( 'untrashed_comment', 'badge_conversationalist_change_comment', 10, 2 );
add_action( 'spammed_comment', 'badge_conversationalist_change_comment', 10, 2 );
add_action( 'unspammed_comment', 'badge_conversationalist_change_comment', 10, 2 );