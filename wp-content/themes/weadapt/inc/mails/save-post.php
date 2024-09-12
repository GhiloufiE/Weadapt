<?php
function theme_save_post($post_ID, $post, $update)
{
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'update-post_' . $post_ID)) {
        return;
    }

    if ('case-study' === $post->post_type || 'solutions-portal' === $post->post_type) {
        delete_transient('map_locations');
    }

    $previous_status = get_post_meta($post_ID, '_previous_status', true);

    $transient_key = 'email_sent_' . $post_ID;

    if ('forum' === $post->post_type && 'pending' === $post->post_status && (!$update || 'pending' !== $previous_status)) {
        if (!get_transient($transient_key)) {
            $users = get_blog_administrators(false, 1);

            if (
                !empty($forum_ID = get_field('forum', $post_ID)) &&
                !empty($theme_network_ID = get_field('relevant_main_theme_network', $forum_ID)) &&
                !empty($theme_network_editors = get_field('people_editors', $theme_network_ID))
            ) {
                $users = array_merge($users, $theme_network_editors);
            }

            $users = array_unique($users);

            if (!empty($users)) {
                $subject = sprintf(__('Content has been submitted for review on [%s]', 'weadapt'), get_bloginfo('name'));

                $message = sprintf(__('Dear %s,', 'weadapt'), 'User') . '<br><br>';

                if (!empty($theme_network_ID)) {
                    $message .= sprintf(
                        __('A forum topic has been submitted for review on <a href="%s">%s</a> in the Theme/Network name <a href="%s">%s</a>: ', 'weadapt'),
                        get_bloginfo('url'),
                        get_bloginfo('name'),
                        get_permalink($theme_network_ID),
                        get_the_title($theme_network_ID)
                    )  ;
                } else {
                    $message .= sprintf(
                        __('A forum topic has been submitted for review on <a href="%s">%s</a>: ', 'weadapt'),
                        get_bloginfo('url'),
                        get_bloginfo('name')
                    )  ;
                }

                $message .= sprintf(
                    '<a href="%s">%s</a><br>',
                    get_edit_post_link($post->ID),
                    esc_html($post->post_title)
                );

                if (!empty($post_author_IDs = get_field('people_creator', $post_ID))) {
                    $post_author_ID = $post_author_IDs[0];
                    $post_author = new WP_User($post_author_ID);
                    $author_organisations = get_field('organisations', $post_author);

                    if (!empty($author_organisations)) {
                        $message .= sprintf(
                            'by %s from %s',
                            $post_author->display_name,
                            get_the_title($author_organisations[0])
                        );
                        $message .= "<br>Best Regards,<br>WeAdapt";
                    } else {
                        $message .= sprintf(
                            'by %s',
                            $post_author->display_name
                        );
                        $message .= "<br>Best Regards,<br>WeAdapt";
                    }
                }

                theme_mail_save_to_db($users, $subject, $message);
                send_email_immediately($users, $subject, $message, $post_ID);
                set_transient($transient_key, true, HOUR_IN_SECONDS);
            }
        }
    }

    if (is_mailed_post_type($post->post_type) && in_array($post->post_status, ['pending', 'draft'])) {
        $previous_status = get_post_meta($post_ID, '_previous_status', true);
        error_log('Post ID: ' . $post_ID . ' | Old Status: ' . $previous_status . ' | New Status: ' . $post->post_status);
    
        $transient_key = 'notify_admin_on_edit_' . $post->ID;
    
        // Check if the post is newly created (no previous status)
        if (empty($previous_status) && $post->post_status === 'pending') {
            // New post submitted for review
            if (!get_transient($transient_key)) {
                $current_user = wp_get_current_user();
                $users = get_blog_administrators(false, 1);
        
                // Dynamically create the subject based on the post type
                if (in_array($post->post_type, ['article', 'event', 'organisation'])) {
                    $subject = sprintf(
                        __('A new %s has been submitted for review on %s', 'weadapt'),
                        ucfirst($post->post_type),
                        get_bloginfo('name')
                    );
                } else {
                    $subject = sprintf(
                        __('A new %s has been submitted for review on %s', 'weadapt'),
                        ucfirst($post->post_type),
                        get_bloginfo('name')
                    );
                }
        
                $message = sprintf(
                    __(' <strong>%s %s</strong> has submitted new content for review.', 'weadapt'),
                    esc_attr($current_user->user_firstname),
                    esc_attr($current_user->user_lastname)
                );
                $message .= '<br><br>';
                $message .= sprintf(__('<strong>Title:</strong> %s', 'weadapt'), esc_html($post->post_title));
                $message .= sprintf('<a href="%s">%s</a>', get_permalink($post->ID), __('Visit the content', 'weadapt'));
                $message .= "<br>Best Regards,<br>WeAdapt";
        
                foreach ($users as $user) {
                    send_email_immediately($user, $subject, $message, $post_ID);
                    error_log('Email sent to ' . $user . ' with subject ' . $subject);
                }
        
                set_transient($transient_key, true, 30);
            }
        }
    
        // Check if the post is being resubmitted for review (was published and now pending)
        elseif ($previous_status === 'publish' && $post->post_status === 'pending') {
            
            if (!get_transient($transient_key)) {
                $current_user = wp_get_current_user();
                $users = get_blog_administrators(false, 1);
    
                $subject = sprintf(__('Content has been resubmitted for review on [%s]', 'weadapt'), get_bloginfo('name'));
    
                $message = sprintf(
                    __(' <strong> %s %s </strong> has resubmitted the content for review.', 'weadapt'),
                    esc_attr($current_user->user_firstname),
                    esc_attr($current_user->user_lastname)
                );
                $author_info = get_userdata($post->post_author);
                $message .= '<br><br>';
                $message .= sprintf(__('<strong>Title:</strong> %s', 'weadapt'), esc_html($post->post_title));
                $message .= '<br>';
                $message .= sprintf(__('This content was originally created by <strong>%s</strong>.', 'weadapt'), esc_html($author_info->display_name));
                $message .= '<br><br>';
                $message .= sprintf('<a href="%s">%s</a>', get_permalink($post->ID), __('Visit the content', 'weadapt'));
                $message .= '<a href="' . get_edit_post_link($post->ID) . '">' . __('Publish/Edit', 'weadapt') . '</a>';
                $message .= "<br>Best Regards,<br>WeAdapt";
    
                foreach ($users as $user) {
                    send_email_immediately($user, $subject, $message, $post_ID);
                    error_log('Email sent to ' . $user . ' with subject ' . $subject);
                }
    
                set_transient($transient_key, true, HOUR_IN_SECONDS);
            }
        }
    }

    update_post_meta($post_ID, '_previous_status', $post->post_status);
}
add_action('save_post', 'theme_save_post', 50, 3);









function on_pending_organisation($ID, $post)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    $transient_key = 'email_sent_' . $ID;

    if (get_transient($transient_key)) {
        return;
    }

    $users = array();
    $administrator = get_blog_administrators(true, 0);
    $users = array_merge($users, $administrator);
    $users = array_unique($users);
    $current_user = wp_get_current_user();
    if (!empty($users)) {
        $subject = sprintf(__('An Organisation is pending publication on [%s]', 'weadapt'), get_bloginfo('name'));
        $message = sprintf(
            __('%s %s (<a href="%s">%s</a>) has sent you an Organisation for review.', 'weadapt'),
            esc_attr($current_user->user_firstname),
            esc_attr($current_user->user_lastname),
            get_author_posts_url($current_user->ID),
            esc_attr($current_user->user_login)
        ) . '<br><br>';

        $message .= sprintf(__('Organisation: %s', 'weadapt'), esc_html($post->post_title)) ;
        $message .= sprintf(__('Summary: %s', 'weadapt'), esc_html($post->post_excerpt))  ;
        $message .= sprintf('<a href="%s">%s</a>', get_permalink($ID), __('Go to the organisation', 'weadapt'));

        theme_mail_save_to_db($users, $subject, $message);
        send_email_immediately($users, $subject, $message, $post_ID);
        set_transient($transient_key, true, HOUR_IN_SECONDS);
    }
}

add_action('pending_organisation', 'on_pending_organisation', 10, 2);
function on_publish_post($new_status, $old_status, $post)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }


    if ('pending' === $new_status && 'pending' !== $old_status) {
        if ('organisation' === $post->post_type) {
            do_action('pending_organisation', $post->ID, $post);
        }
    }
}

add_action('transition_post_status', 'on_publish_post', 10, 3);
