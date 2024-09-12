<?php

/**
 * Mail Notification on Register User
 *
 * For debuging use: error_log( print_r( $variable, true ) );
 */

function theme_user_register( $user_id, $userdata ) {
    // Docx comment #1: goes to the microsite Editor (if user is joining a microsite) and weADAPT Admin
    $users = array_merge( get_blog_administrators( false, 1 ), get_blog_editors() );

    if ( ! empty( $users ) ) {
        $subject = sprintf( __( 'A new user account has been created on [%s]', 'weadapt' ), get_bloginfo( 'name' ) );

        $message = sprintf( __( 'A new user account has been created: %s %s (%s) on [%s] ', 'weadapt' ),
            esc_html( $userdata['first_name'] ),
            esc_html( $userdata['last_name'] ),
            esc_html( $userdata['user_login'] ),
            get_bloginfo( 'name' )
        ) . '<br><br>';

        $message .= sprintf( '<a href="%s">%s</a><br>',
            get_author_posts_url( $user_id ),
            __( 'View user profile', 'weadapt' )
        );

        $message .= sprintf( ' <a href="%s">%s</a>',
            add_query_arg( 'user_id', $user_id, self_admin_url( 'user-edit.php' ) ),
            __( 'Edit/delete user', 'weadapt' )
        );

        // Save email to DB
        theme_mail_save_to_db( $users, $subject, $message );

        // Debug log for email sending
        error_log( 'Attempting to send email to: ' . print_r( $users, true ) );
        error_log( 'Email subject: ' . $subject );
        error_log( 'Email message: ' . $message );
		wp_mail($users,$subject,$message);
        send_email_immediately($users, $subject, $message,null);
    }
}
add_action( 'user_register', 'theme_user_register', 50, 2 );