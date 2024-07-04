<?php
/**
 * Mail Notification on Publish Post
 *
 * For debugging use: error_log( print_r( $variable, true ) );
 */
function theme_publish_post($post) {
    if (!is_mailed_post_type($post->post_type)) {
        return;
    }

    $post_ID = $post->ID;
    $author_id = $post->post_author;
    $contributors = get_field('people_contributors', $post_ID);

    // Exclude administrators from the contributors list
    $valid_contributors = array();

    if (!empty($contributors)) {
        foreach ($contributors as $contributor_id) {
            $user = get_userdata($contributor_id);
            if ($user && !in_array('administrator', $user->roles)) {
                $valid_contributors[] = $contributor_id;
            }
        }
    }

    // Add the author to the valid contributors list if they are not an administrator
    $author_user = get_userdata($author_id);
    if ($author_user && !in_array('administrator', $author_user->roles)) {
        $valid_contributors[] = $author_id;
    }

    if (!empty($valid_contributors)) {
        $subject = sprintf(
            __('Your %s has been published on %s', 'weadapt'),
            ucfirst($post->post_type),
            get_bloginfo('name')
        );

        $message = __('Your content has now been reviewed and published. It will be shared on our social media channels where relevant. Please do re-share! ', 'weadapt') . '<br><br>';
        $message .= esc_html($post->post_title) . '<br>';
        $message .= esc_html($post->post_excerpt) . '<br><br>';

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
            } else {
                $message .= sprintf(
                    'by %s',
                    $post_author->display_name
                );
            }

            $message .= '<br>';
        }

        $message .= sprintf(
            ' — <a href="%s">%s</a>',
            get_permalink($post_ID),
            __('See it', 'weadapt')
        ) . '<br>';
        $message .= sprintf(
            ' — <a href="%s">%s</a>',
            get_edit_post_link($post_ID),
            __('Edit it', 'weadapt')
        );

        theme_mail_save_to_db(
            $valid_contributors,
            $subject,
            $message
        );
        send_email_immediately($valid_contributors, $subject, $message);
    }

    // Notify Editors of theme/network/microsite and weADAPT Admin
    $users = array_merge(get_blog_administrators(false, 1), get_blog_editors());

    // Theme/Network Editors
    if (!empty($main_theme_network = get_field('relevant_main_theme_network', $post_ID))) {
        if (!empty($main_theme_network_editors = get_field('people_editors', $main_theme_network))) {
            $users = array_merge($users, $main_theme_network_editors);
        }
    }
    $users = array_unique($users);

    if (!empty($users)) {
        $subject = sprintf(
            __('An %s has been published on %s', 'weadapt'),
            ucfirst($post->post_type),
            get_bloginfo('name')
        );

        $message = esc_html($post->post_title) . '<br>';
        $message .= esc_html($post->post_excerpt) . '<br><br>';

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
            } else {
                $message .= sprintf(
                    'by %s',
                    $post_author->display_name
                );
            }

            $message .= '<br>';
        }

        $message .= sprintf(
            ' — <a href="%s">%s</a>',
            get_permalink($post_ID),
            __('See it', 'weadapt')
        ) . '<br>';
        $message .= sprintf(
            ' — <a href="%s">%s</a>',
            get_edit_post_link($post_ID),
            __('Publish / Edit / Delete it', 'weadapt')
        );

        theme_mail_save_to_db(
            $users,
            $subject,
            $message
        );
        send_email_immediately($users, $subject, $message);
    }

    // Notify Editors of themes/networks ticked as ‘related’
    if (!empty($related_themes_networks = get_field('relevant_themes_networks', $post_ID))) {
        $relevant_users = [];

        foreach ($related_themes_networks as $theme_network_ID) {
            if (!empty($theme_network_editors = get_field('people_editors', $theme_network_ID))) {
                $relevant_users = array_merge($relevant_users, $theme_network_editors);
            }
        }

        $relevant_users = array_unique($relevant_users);

        if (!empty($relevant_users)) {
            $subject = sprintf(
                __('Content has been published on %s which is related to your theme / network', 'weadapt'),
                get_bloginfo('name')
            );

            $post_author_IDs = get_field('people_creator', $post_ID);
            $post_author = !empty($post_author_IDs) ? new WP_User($post_author_IDs[0]) : false;

            if (!empty($post_author)) {
                $message = sprintf(
                    __('Content has been published by <a href="%s">%s %s (%s)</a> on %s which is related to your theme / network.', 'weadapt'),
                    get_author_posts_url($post_author->ID),
                    esc_attr($post_author->user_firstname),
                    esc_attr($post_author->user_lastname),
                    esc_attr($post_author->user_login),
                    get_bloginfo('name')
                ) . '<br><br>';
            } else {
                $message = sprintf(
                    __('Content has been published on %s which is related to your theme / network.', 'weadapt'),
                    get_bloginfo('name')
                ) . '<br><br>';
            }

            $message .= __('You may like to create a link to it from your Theme / Network or discuss it in a Learning Forum.', 'weadapt') . '<br><br>';

            $message .= sprintf(
                __('Content: %s', 'weadapt'),
                esc_html($post->post_title)
            ) . '<br>';
            $message .= sprintf(
                __('Summary: %s', 'weadapt'),
                esc_html($post->post_excerpt)
            ) . '<br><br>';
            $message .= sprintf(
                '<a href="%s">%s</a>',
                get_permalink($post_ID),
                __('Go to the content', 'weadapt')
            );

            theme_mail_save_to_db(
                $relevant_users,
                $subject,
                $message
            );
            send_email_immediately($relevant_users, $subject, $message);
        }
    }
}

add_action('new_to_publish', 'theme_publish_post', 50);
add_action('pending_to_publish', 'theme_publish_post', 50);
add_action('draft_to_publish', 'theme_publish_post', 50);
add_action('auto-draft_to_publish', 'theme_publish_post', 50);
add_action('future_to_publish', 'theme_publish_post', 50);
add_action('private_to_publish', 'theme_publish_post', 50);
add_action('trash_to_publish', 'theme_publish_post', 50);
