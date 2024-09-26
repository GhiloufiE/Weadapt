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

add_action('comment_post', 'notify_admin_on_pending_comment', 10, 2);

function notify_admin_on_pending_comment($comment_id, $comment_approved)
{
	
    if ($comment_approved == 0) {
		$users   = get_blog_administrators( false, 1 );
        $comment = get_comment($comment_id);
        $post = get_post($comment->comment_post_ID);

        

        $subject = 'New Comment Awaiting Approval on "' . $post->post_title . '" - ' . get_bloginfo('name');
        $post_excerpt = wp_strip_all_tags(wp_trim_words($comment->comment_content, 100, '...'));

        $approve_url = admin_url('comment.php?action=approve&c=' . $comment_id);
        $edit_url = admin_url('comment.php?action=editcomment&c=' . $comment_id);

        $message = '<p>A new comment is awaiting approval on the post: <strong>' . esc_html($post->post_title) . '</strong></p>';
        $message .= '<p>Comment Summary: ' . esc_html($post_excerpt) . '</p>';
        $message .= '<p>Review and take action:</p>';
        $message .= '<ul>
                        <a href="' . esc_url($approve_url) . '">Approve</a>
                        <a href="' . esc_url($edit_url) . '">Edit</a>
                    </ul>';
        $message .= '<br>Best Regards,<br>' . esc_html(get_bloginfo('name')) . '<br>';
        foreach ($users as $user) {
                send_email_immediately($user, $subject, $message, null);
            }
        
    }
}
add_action('comment_unapproved_to_approved', 'notify_user_comment_approved', 10, 1);

function notify_user_comment_approved($comment) {
    $comment_id = $comment->comment_ID;
    $comment_author_email = $comment->comment_author_email;
    $post_title = get_the_title($comment->comment_post_ID);
    $comment_link = get_comment_link($comment_id);
    $user = get_user_by('email', $comment_author_email);
    if (!$user) {
        error_log("User with email " . $comment_author_email . " not found.");
        return;
    }

    $user_id = $user->ID;
    $subject = 'Comment Approved on ' . get_bloginfo('name');
    $message = sprintf(
    'Dear %s,<br><br>Your comment on the post "<strong>%s</strong>" has been approved and is now live. You can view it by clicking the link below:<br><br><a href="%s" style="color: #0073aa; text-decoration: none;">View Your Comment</a><br><br>',
    $user->display_name, 
    $post_title,
    $comment_link
);
$message .= "Thank you for contributing to our community!<br><br>Best Regards,<br>" . get_bloginfo('name');
    $message .= "<br><br>Best Regards,<br>" . get_bloginfo('name');
    send_email_immediately($user_id, $subject, $message, null);
}
function force_comment_moderation($approved, $commentdata) {
    return 0; 
}
add_filter('pre_comment_approved', 'force_comment_moderation', 10, 2);
