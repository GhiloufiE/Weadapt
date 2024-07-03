<?php
function theme_save_post( $post_ID, $post, $update ) {
    if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-post_' . $post_ID ) ) {
        return;
    }

    // Fix case-study map transient
    if ( 'case-study' === $post->post_type || 'solutions-portal' === $post->post_type ) {
        delete_transient( 'map_locations' );
    }

    // Notify about 'forum' post type with pending status
    if ( 'forum' === $post->post_type && in_array( $post->post_status, ['pending'] ) ) {
        $users = get_blog_administrators( false, 1 );

        if ( ! empty( $forum_ID = get_field( 'forum', $post_ID ) ) &&
            ! empty( $theme_network_ID = get_field( 'relevant_main_theme_network', $forum_ID ) ) &&
            ! empty( $theme_network_editors = get_field( 'people_editors', $theme_network_ID ) ) ) {
            $users = array_merge( $users, $theme_network_editors );
        }

        $users = array_unique( $users );

        if ( ! empty( $users ) ) {
            $subject = sprintf( __( 'Content has been submitted for review on [%s]', 'weadapt' ), get_bloginfo( 'name' ) );

            $message = sprintf( __( 'Dear %s,', 'weadapt' ), 'User' ) . '<br><br>';

            if ( ! empty( $theme_network_ID ) ) {
                $message .= sprintf( __( 'A forum topic has been submitted for review on <a href="%s">%s</a> in the Theme/Network name <a href="%s">%s</a>: ', 'weadapt' ),
                    get_bloginfo( 'url' ),
                    get_bloginfo( 'name' ),
                    get_permalink( $theme_network_ID ),
                    get_the_title( $theme_network_ID )
                ) . '<br><br>';
            } else {
                $message .= sprintf( __( 'A forum topic has been submitted for review on <a href="%s">%s</a>: ', 'weadapt' ),
                    get_bloginfo( 'url' ),
                    get_bloginfo( 'name' )
                ) . '<br><br>';
            }

            $message .= sprintf('<a href="%s">%s</a><br>',
                get_edit_post_link($post->ID),
                esc_html($post->post_title)
            );

            if ( ! empty( $post_author_IDs = get_field( 'people_creator', $post_ID ) ) ) {
                $post_author_ID = $post_author_IDs[0];
                $post_author = new WP_User( $post_author_ID );
                $author_organisations = get_field( 'organisations', $post_author );

                if ( ! empty( $author_organisations ) ) {
                    $message .= sprintf( 'by %s from %s',
                        $post_author->display_name,
                        get_the_title( $author_organisations[0] )
                    );
                } else {
                    $message .= sprintf( 'by %s',
                        $post_author->display_name
                    );
                }
            }

            theme_mail_save_to_db( $users, $subject, $message );
            send_email_immediately($users, $subject, $message);

            // Prevent further notifications for this post type and status
            return;
        }
    }

    // General content submission review notification
    if ( is_mailed_post_type( $post->post_type ) && in_array( $post->post_status, ['pending'] ) ) {
        $current_user = wp_get_current_user();

        $users = array_merge( get_blog_administrators( false, 1 ), get_blog_editors() );
        $users = array_unique( $users );

        if ( ! empty( $users ) ) {
            $subject = sprintf( __( 'Content has been submitted for review on [%s]', 'weadapt' ), get_bloginfo( 'name' ) );

            $message = sprintf( __( '%s %s (<a href="%s">%s</a>) has sent you content for review.', 'weadapt' ),
                esc_attr( $current_user->user_firstname ),
                esc_attr( $current_user->user_lastname ),
                get_author_posts_url($current_user->ID),
                esc_attr( $current_user->user_login )
            ) . '<br><br>';

            $message .= sprintf( __( 'Content: %s', 'weadapt' ), esc_html( $post->post_title ) ) . '<br>';
            $message .= sprintf( __( 'Summary: %s', 'weadapt' ), esc_html( $post->post_excerpt ) ) . '<br><br>';
            $message .= sprintf( '<a href="%s">%s</a>', get_permalink( $post_ID ), __( 'Go to the content', 'weadapt' ) );

            $draft_tags = wp_get_post_terms( $post_ID, 'tags', ['hide_empty' => false] );

            if ( ! empty( $draft_tags ) ) {
                $message_tags = [];

                foreach ( $draft_tags as $term ) {
                    if ( false === get_field( 'status', $term ) ) {
                        $message_tags[] = sprintf( __( '<a href="%s">%s</a> (ID %s)', 'weadapt' ),
                            add_query_arg( array(
                                'taxonomy' => $term->taxonomy,
                                'tag_ID' => $term->term_id,
                            ), admin_url( 'term.php' ) ),
                            esc_html( $term->name ),
                            intval( $term->term_id )
                        );
                    }
                }

                if ( ! empty( $message_tags ) ) {
                    $message .= '<br><br>' . __( 'Draft Tags:', 'weadapt' ) . '<br>' . implode( '<br>', $message_tags );
                }
            }

            theme_mail_save_to_db( $users, $subject, $message );
            send_email_immediately($users, $subject, $message);
        }
    }
}

add_action( 'save_post', 'theme_save_post', 50, 3 );
function send_email_immediately($user_ids, $subject, $message) {
    foreach ($user_ids as $user_id) {
        $user_info = get_userdata($user_id);
        if ($user_info) {
            $recipient = $user_info->user_email;
            $headers = array('Content-Type: text/html; charset=UTF-8');
            $sent = wp_mail($recipient, $subject, $message, $headers);
        } else {
           return;
        }
    }
}


function on_pending_organisation( $ID, $post ) {
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
        return;
    }

    $published_to = get_field( 'publish_to', $ID );
    $users = array();

    if ( ! empty( $published_to ) ) {
        foreach ( $published_to as $blog_id ) {
            $blog_editors = get_users( [
                'blog_id' => $blog_id,
                'exclude' => [1],  // Exclude 'Developers' User
                'theme_query' => true, // multisite fix
                'role' => 'author',
                'fields' => 'ID'
            ] );
            $users = array_merge( $users, $blog_editors );
        }
    }

    $administrator = get_blog_administrators( true, 0 );
    $users = array_merge( $users, $administrator );
    $users = array_unique( $users );

    $users = apply_filters( 'wp_mail_users', $users );

    if ( ! empty( $users ) ) {
        $subject = sprintf( __( 'An Organisation is pending publication on [%s]', 'weadapt' ), get_bloginfo( 'name' ) );
        $message = sprintf( __( '%s %s (<a href="%s">%s</a>) has sent you an Organisation for review.', 'weadapt' ),
            esc_attr( $current_user->user_firstname ),
            esc_attr( $current_user->user_lastname ),
            get_author_posts_url( $current_user->ID ),
            esc_attr( $current_user->user_login )
        ) . '<br><br>';

        $message .= sprintf( __( 'Organisation: %s', 'weadapt' ), esc_html( $post->post_title ) ) . '<br>';
        $message .= sprintf( __( 'Summary: %s', 'weadapt' ), esc_html( $post->post_excerpt ) ) . '<br><br>';
        $message .= sprintf( '<a href="%s">%s</a>', get_permalink( $ID ), __( 'Go to the organisation', 'weadapt' ) );

        theme_mail_save_to_db( $users, $subject, $message );
		send_email_immediately($users, $subject, $message);
    }
}

add_action( 'pending_organisation', 'on_pending_organisation', 10, 2 );

function on_publish_post( $new_status, $old_status, $post ) {
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
        return;
    }

    if ( 'pending' === $old_status && 'publish' === $new_status ) {
        if ( 'organisation' === $post->post_type ) {
            do_action( 'pending_organisation', $post->ID, $post );
        }
    }
}

add_action( 'transition_post_status', 'on_publish_post', 10, 3 );
