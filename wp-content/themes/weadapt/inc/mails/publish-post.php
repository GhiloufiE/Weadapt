
<?php

    // // Exclude administrators from the contributors list
    // $valid_contributors = array();

    // if (!empty($contributors)) {
    //     foreach ($contributors as $contributor_id) {
    //         $user = get_userdata($contributor_id);
    //         if ($user && !in_array('administrator', $user->roles)) {
    //             $valid_contributors[] = $contributor_id;
    //         }
    //     }
    // }

    // // Add the author to the valid contributors list if they are not an administrator
    // $author_user = get_userdata($author_id);
    // if ($author_user && !in_array('administrator', $author_user->roles)) {
    //     $valid_contributors[] = $author_id;
    // }

    // if (!empty($valid_contributors)) {
    //     $subject = sprintf(
    //         __('Your %s has been published on %s', 'weadapt'),
    //         ucfirst($post->post_type),
    //         get_bloginfo('name')
    //     );

    //     $message = __('Your content has now been reviewed and published. It will be shared on our social media channels where relevant. Please do re-share! ', 'weadapt') . '<br><br>';
    //     $message .= esc_html($post->post_title) . '<br>';
    //     $message .= esc_html($post->post_excerpt) . '<br><br>';

    //     if (!empty($post_author_IDs = get_field('people_creator', $post_ID))) {
    //         $post_author_ID = $post_author_IDs[0];
    //         $post_author = new WP_User($post_author_ID);
    //         $author_organisations = get_field('organisations', $post_author);

    //         if (!empty($author_organisations)) {
    //             $message .= sprintf(
    //                 'by %s from %s',
    //                 $post_author->display_name,
    //                 get_the_title($author_organisations[0])
    //             );
    //         } else {
    //             $message .= sprintf(
    //                 'by %s',
    //                 $post_author->display_name
    //             );
    //         }

    //         $message .= '<br>';
    //     }

    //     $message .= sprintf(
    //         ' — <a href="%s">%s</a>',
    //         get_permalink($post_ID),
    //         __('See it', 'weadapt')
    //     ) . '<br>';
    //     $message .= sprintf(
    //         ' — <a href="%s">%s</a>',
    //         get_edit_post_link($post_ID),
    //         __('Edit it', 'weadapt')
    //     );

    //     theme_mail_save_to_db(
    //         $valid_contributors,
    //         $subject,
    //         $message
    //     );
    //     send_email_immediately($valid_contributors, $subject, $message);

    //     // Update the custom field to mark the notification as sent
    //     update_post_meta($post_ID, '_notification_sent', true);
    // }

    // Notify Editors of theme/network/microsite and weADAPT Admin
    // $users = array_merge(get_blog_administrators(false, 1), get_blog_editors());

    // // Theme/Network Editors
    // if (!empty($main_theme_network = get_field('relevant_main_theme_network', $post_ID))) {
    //     if (!empty($main_theme_network_editors = get_field('people_editors', $main_theme_network))) {
    //         $users = array_merge($users, $main_theme_network_editors);
    //     }
    // }
    // $users = array_unique($users);

  

/**
 * Mail Notification on Publish Post
 *
 * For debugging use: error_log( print_r( $variable, true ) );
 */

 function theme_publish_post($post) {

    // $contributors = get_field('people_contributors', $post);

    // // Check if the notification has already been sent
    // $notification_sent = get_post_meta($post, '_notification_sent', true);
    //  if ($notification_sent) {
    //     error_log("Notification already sent for post ID: " . $post);
    //     return;
    // } 

 
}

add_action('new_to_publish', 'theme_publish_post', 50);
add_action('pending_to_publish', 'theme_publish_post', 50);
add_action('draft_to_publish', 'theme_publish_post', 50);
add_action('auto-draft_to_publish', 'theme_publish_post', 50);
add_action('future_to_publish', 'theme_publish_post', 50);
add_action('private_to_publish', 'theme_publish_post', 50);
add_action('trash_to_publish', 'theme_publish_post', 50);