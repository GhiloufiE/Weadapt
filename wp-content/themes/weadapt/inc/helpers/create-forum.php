<?php
function handle_create_post()
{
    global $wpdb;
    $blog_id = get_current_blog_id();
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'create_post_nonce')) {
        wp_send_json_error('Nonce verification failed');
        return;
    }
    if (!isset($_POST['post_title']) || !isset($_POST['post_description']) || !isset($_POST['post_type'])) {
        wp_send_json_error('Missing required POST fields');
        return;
    }
    $post_title = sanitize_text_field($_POST['post_title']);
    $post_description = sanitize_textarea_field($_POST['post_description']);
    $post_type = sanitize_text_field($_POST['post_type']);
    $post_data = array(
        'post_title'   => $post_title,
        'post_content' => $post_description,
        'post_status'  => 'pending',
        'post_author'  => get_current_user_id(),
        'post_type'    => ($post_type === 'theme') ? 'article' : $post_type,
        'meta_input'   => array(),
    );

    if (isset($_POST['selected_forum']) && !empty($_POST['selected_forum'])) {
        $selected_forum = intval($_POST['selected_forum']);
        $post_data['meta_input']['forum'] = $selected_forum;
    }

    $network_true_id = null;
    $forum_true_id = null;
    $selected_forum_true_id = null;
    $meta_key = 'forum';
    if ($post_type == 'forum' && isset($_POST['forum'])) {
        $selected_forum = intval($_POST['selected_forum']);
        $selected_forum_true_id = $wpdb->get_var($wpdb->prepare("SELECT forum_id FROM {$wpdb->prefix}theme_forum_relationship WHERE theme_id = %d", $selected_forum));
        if ($selected_forum_true_id === null) {
            $selected_forum_true_id = $wpdb->get_var($wpdb->prepare("SELECT forum_id FROM {$wpdb->prefix}network_forum_relationship WHERE network_id = %d", $selected_forum));
            if ($selected_forum_true_id !== null) {
                $meta_key = 'network_forum';
            }
        }
        if ($selected_forum_true_id !== null) {
            $post_data['meta_input'][$meta_key] = $selected_forum_true_id;
        }
        $forum_id = intval($_POST['forum']);
        $forum_true_id = $wpdb->get_var($wpdb->prepare("SELECT forum_id FROM {$wpdb->prefix}theme_forum_relationship WHERE theme_id = %d", $forum_id));
        if ($forum_true_id !== null) {
            $post_data['meta_input']['forum'] = $forum_true_id;
        }

        $network_true_id = $wpdb->get_var($wpdb->prepare("SELECT forum_id FROM {$wpdb->prefix}network_forum_relationship WHERE network_id = %d", $forum_id));
        if ($network_true_id !== null) {
            $post_data['meta_input']['network_forum'] = $network_true_id;
        }
    }
    if ($post_type == 'theme' && isset($_POST['forum'])) {
        $forum_id = intval($_POST['forum']);
        $forum_true_id = $wpdb->get_var($wpdb->prepare("SELECT forum_id FROM {$wpdb->prefix}theme_forum_relationship WHERE theme_id = %d", $forum_id));
        if ($forum_true_id !== null) {
            $post_data['meta_input']['relevant_main_theme_network'] = $forum_true_id;
        }
    }
    $post_id = wp_insert_post($post_data);
    if (is_wp_error($post_id)) {
        wp_send_json_error('Error creating post: ' . $post_id->get_error_message());
        return;
    }
    $post_author_id = get_current_user_id();
    $group_field_value = get_field('field_637f1ee7327b4', $post_id);
    if (!is_array($group_field_value)) {
        $group_field_value = [];
    }

    $group_field_value['creator'] = $post_author_id;
    $group_field_value['publisher'] = $post_author_id;
    $group_field_value['contributors'] = $post_author_id;
    update_field('field_637f1ee7327b4', $group_field_value, $post_id);
    if ($network_true_id !== null) {
        update_field('field_653b5c7e6d5f5', $network_true_id, $post_id);
    } elseif ($forum_true_id !== null) {
        update_field('field_653b5c7e6d5f5', $forum_true_id, $post_id);
    }
    if ($selected_forum_true_id !== null) {
        update_field('field_653b5c7e6d5f5', $selected_forum_true_id, $post_id);
    }
    update_field('field_6374a3364bb73', $blog_id, $post_id);

    if (in_array($post_type, ['forum', 'theme'])) {
        notify_admins_of_pending_posts($post_id, $post_type);
    }

    wp_send_json_success('Post created successfully');
}
add_action('admin_post_nopriv_create_post', 'handle_create_post');
add_action('admin_post_create_post', 'handle_create_post');


function notify_admins_of_pending_posts($post_id, $post_type)
{
    global $wpdb;

    $site_name = get_bloginfo('name');
    $post_title = get_the_title($post_id);
    $post_link = get_edit_post_link($post_id);
    $subject = '';
    $message = '';

    if ($post_type === 'forum') {
        $forum_id = get_field('forum', $post_id);
        if (!$forum_id) {
            return;
        }
        $forum_name = get_the_title($forum_id);
        $subject = sprintf(__('New Forum Topic Awaiting Review on %s', 'weadapt'), $site_name);

        $message = sprintf(
            __('A new forum topic titled <b>%s</b> in the forum <b>%s</b> is pending review. ', 'weadapt'),
            esc_html($post_title),
            esc_html($forum_name)
        );
    } else {
        return;
    }

    $message .= sprintf('<a href="%s">%s</a>', esc_url(get_permalink($post_id)), __('See it', 'weadapt'));
    $message .= sprintf(' <a href="%s">%s</a>', esc_url($post_link), __('Publish / Edit / Delete it', 'weadapt'));
    $message .= "Best Regards,<br>$site_name<br>";

    $admins_info = $wpdb->get_results("
        SELECT u.ID as user_id, u.user_email, u.display_name
        FROM {$wpdb->users} u
        INNER JOIN {$wpdb->usermeta} um ON u.ID = um.user_id
        WHERE um.meta_key = 'wp_capabilities'
        AND um.meta_value LIKE '%administrator%'
    ", ARRAY_A);

    if (empty($admins_info)) {
        return;
    }

    foreach ($admins_info as $admin) {
        $admin_id = $admin['user_id'];
        $personalized_message = "" . $message;
        send_email_immediately($admin_id, $subject, $personalized_message, $post_id);
        theme_mail_save_to_db(array($admin['user_email']), $subject, $personalized_message); // Save to DB using email
    }
}

function forum_new_post_notification($post_id)
{
    global $wpdb;
    $batch_size = 50;

    error_log("forum_new_post_notification triggered for post ID: $post_id");

    if (get_post_type($post_id) !== 'forum') {
        error_log("Invalid post type for post ID: $post_id");
        return;
    }

    if (
        !get_field('send_notification_to_members', $post_id) ||
        get_post_meta($post_id, 'forum_notification_sent', true) ||
        get_post_status($post_id) !== 'publish' ||
        wp_is_post_revision($post_id)
    ) {
        return;
    }

    $forum_post_id = (int) get_post_meta($post_id, 'forum', true);
    if (!$forum_post_id) {
        error_log("No valid forum_post_id for post ID: $post_id");
        return;
    }

    $meta_values = get_field('relevant_main_theme_network', $forum_post_id);
    if (!$meta_values) {
        return;
    }

    if (!is_array($meta_values)) {
        $meta_values = array($meta_values);
    }
    $theme_name = get_the_title($forum_post_id);
    $post_title = get_the_title($post_id);
    $post_excerpt = wp_strip_all_tags(wp_trim_words(get_the_excerpt($post_id), 100));
    $post_link = get_permalink($post_id);
    $site_name = get_bloginfo('name');
    $subject = "New forum discussion in the $theme_name forum on weADAPT";

    $table_name = $wpdb->prefix . 'wa_join';
    $user_ids = $wpdb->get_col($wpdb->prepare(
        "SELECT user_id FROM $table_name WHERE join_id IN (%s)",
        implode(',', $meta_values)
    ));

    if (empty($user_ids)) {
        return;
    }

    $total_users = count($user_ids);
    $batches = ceil($total_users / $batch_size);
    for ($i = 0; $i < $batches; $i++) {
        $batch_user_ids = array_slice($user_ids, $i * $batch_size, $batch_size);

        foreach ($batch_user_ids as $user_id) {
            $user_info = get_userdata($user_id);
            if ($user_info) {
                $message = "
                            We wanted to inform you of a new forum post in the <b>$theme_name</b> theme you are a member of<br><br>
                           <b>Title:</b> $post_title<br>
                            <b>Content:</b> $post_excerpt<br><br>
                            Thank you for your continued participation and contributions to our community.<br><br>
                            Reply to the conversation and engage with other members here:
                            <a href='$post_link'>View the forum post</a><br><br>
                            Best Regards,<br>$site_name";
                send_email_immediately(array($user_id), $subject, $message, $post_id);
            } 
        }
        sleep(1);
    }

    update_post_meta($post_id, 'forum_notification_sent', true);
}
add_action('acf/save_post', 'forum_new_post_notification', 10, 1);