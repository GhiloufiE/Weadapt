<?php

/**
 * Disable standard notifications
 */
add_filter( 'notify_moderator', '__return_false' );
add_filter( 'comment_notification_recipients', function( $emails, $comment_id ) {
	return [];
}, 10, 2 );


/**
 * Mail Notification on Comment Post
 *
 * For debuging use: error_log( print_r( $variable, true ) );
 */
function theme_comment_post( $comment_ID, $comment_approved, $commentdata ) {

	// Docx comment #8: goes to Editor of theme and weADAPT Admin
	$users   = get_blog_administrators( false, 1 );
	$comment = get_comment( $comment_ID );
	$post    = get_post( $comment->comment_post_ID );
	$post_ID = $post->ID;


	// Theme Network
	$theme_network_ID = get_field( 'relevant_main_theme_network', $post_ID );

	switch ( $post->post_type) {
		case 'forum':
			if ( ! empty( $forum_ID = get_field( 'forum', $post_ID ) ) ) {
				$theme_network_ID = get_field( 'relevant_main_theme_network', $forum_ID );
			}
			break;
	}

	if (
		! empty( $theme_network_ID ) &&
		! empty( $theme_network_editors = get_field( 'people_editors', $theme_network_ID ) )
	) {
		$users = array_merge( $users, $theme_network_editors );
	}

	$users   = array_unique( $users );

	if ( ! empty( $users ) ) {
		$subject = __( 'A new comment has been created', 'weadapt' );
		$message = sprintf( __( 'A new comment has been posted on the content: %s', 'weadapt' ),
			get_the_title( $post_ID )
		) . '<br>';

		$user_ID              = ! empty( $commentdata['user_ID'] ) ? intval( $commentdata['user_ID'] ) : 0;
		$post_author          = new WP_User( $user_ID );
		$author_organisations = get_field( 'organisations', $post_author );

		if ( ! empty( $author_organisations ) ) {
			$message .= sprintf( 'by %s from %s',
				$commentdata['comment_author'],
				get_the_title( $author_organisations[0] )
			);
		}
		else {
			$message .= sprintf( 'by %s',
				$commentdata['comment_author'],
			);
		}

		$message .= '<br>';

		$message .= sprintf( ' — <a href="%s">%s</a>',
			admin_url( "comment.php?action=editcomment&c={$comment_ID}" ),
			__( 'Publish / Edit / Delete it', 'weadapt' )
		);

		theme_mail_save_to_db(
			$users,
			$subject,
			$message
		);
	}
}

add_action( 'comment_post', 'theme_comment_post', 50, 3 );