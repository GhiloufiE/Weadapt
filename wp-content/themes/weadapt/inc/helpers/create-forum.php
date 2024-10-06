<?php
function handle_create_post() {
    global $wpdb;
    $blog_id = get_current_blog_id();
    
    // Log blog ID at the start
    error_log("Starting post creation for blog ID: {$blog_id}");
    
    // Verify nonce
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'create_post_nonce')) {
        error_log("Nonce verification failed for blog ID: {$blog_id}");
        wp_send_json_error('Nonce verification failed');
        return;
    }
    error_log("Nonce verification passed for blog ID: {$blog_id}");
    
    // Check required fields
    if (!isset($_POST['post_title']) || !isset($_POST['post_description']) || !isset($_POST['post_type'])) {
        error_log("Missing required POST fields for blog ID: {$blog_id}");
        wp_send_json_error('Missing required POST fields');
        return;
    }
    error_log("Required fields present for blog ID: {$blog_id}");
    
    // Sanitize inputs
    $post_title = sanitize_text_field($_POST['post_title']);
    $post_description = sanitize_textarea_field($_POST['post_description']);
    $post_type = sanitize_text_field($_POST['post_type']);
    
    error_log("Sanitized inputs for blog ID: {$blog_id}. Title: {$post_title}, Post Type: {$post_type}");
    
    $post_data = array(
        'post_title'   => $post_title,
        'post_content' => $post_description,
        'post_status'  => 'pending',
        'post_author'  => get_current_user_id(),
        'post_type'    => ($post_type === 'theme') ? 'article' : $post_type,
        'meta_input'   => array(),
    );
    
    // Handle selected forum if present
    $selected_forum = null;
    if (isset($_POST['selected_forum']) && !empty($_POST['selected_forum'])) {
        $selected_forum = intval($_POST['selected_forum']); 
        $post_data['meta_input']['forum'] = $selected_forum;
        error_log("Selected forum set: {$selected_forum} for blog ID: {$blog_id}");
    } else {
        error_log("No forum selected for blog ID: {$blog_id}");
    }

    // Handle specific post type logic for 'forum' or 'theme'
    $network_true_id = null;
    $forum_true_id = null;

    if ($post_type == 'forum' && isset($_POST['forum'])) {
        $forum_id = intval($_POST['forum']);
        $forum_true_id = $wpdb->get_var($wpdb->prepare("SELECT forum_id FROM {$wpdb->prefix}theme_forum_relationship WHERE theme_id = %d", $forum_id));
        if ($forum_true_id !== null) {
            $post_data['meta_input']['forum'] = $forum_true_id;
            error_log("Forum true ID found: {$forum_true_id} for blog ID: {$blog_id}");
        } else {
            error_log("No forum true ID found for forum ID: {$forum_id} in blog ID: {$blog_id}");
        }

        $network_true_id = $wpdb->get_var($wpdb->prepare("SELECT forum_id FROM wp_network_forum_relationship WHERE network_id = %d", $forum_id));
        if ($network_true_id !== null) {
            $post_data['meta_input']['network_forum'] = $network_true_id;
            error_log("Network forum true ID found: {$network_true_id} for blog ID: {$blog_id}");
        } else {
            error_log("No network forum true ID found for forum ID: {$forum_id} in blog ID: {$blog_id}");
        }
    }

    if ($post_type == 'theme' && isset($_POST['forum'])) {
        $forum_id = intval($_POST['forum']);
        $forum_true_id = $wpdb->get_var($wpdb->prepare("SELECT forum_id FROM {$wpdb->prefix}theme_forum_relationship WHERE theme_id = %d", $forum_id));
        if ($forum_true_id !== null) {
            $post_data['meta_input']['relevant_main_theme_network'] = $forum_true_id;
            error_log("Theme forum true ID set for blog ID: {$blog_id}, Forum ID: {$forum_true_id}");
        }
    }

    // Insert the post
    $post_id = wp_insert_post($post_data);
    if (is_wp_error($post_id)) {
        error_log("Error creating post for blog ID: {$blog_id}: " . $post_id->get_error_message());
        wp_send_json_error('Error creating post: ' . $post_id->get_error_message());
        return;
    }
    error_log("Post created successfully with post ID: {$post_id} for blog ID: {$blog_id}");

    // Update ACF and meta fields with user data
    $post_author_id = get_current_user_id();
    $group_field_value = get_field('field_637f1ee7327b4', $post_id);
    if (!is_array($group_field_value)) {
        $group_field_value = [];
        error_log("Group field value initialized for blog ID: {$blog_id}, post ID: {$post_id}");
    }

    $group_field_value['creator'] = $post_author_id;
    $group_field_value['publisher'] = $post_author_id;
    $group_field_value['contributors'] = $post_author_id;
    update_field('field_637f1ee7327b4', $group_field_value, $post_id);
    error_log("Updated group field for blog ID: {$blog_id}, post ID: {$post_id}");

    // Update forum or network meta fields
    if ($selected_forum !== null) {
        update_field('field_653b5c7e6d5f5', $selected_forum, $post_id);
        error_log("Updated selected forum field with ID: {$selected_forum} for blog ID: {$blog_id}");
    }
    if ($network_true_id !== null) {
        update_field('field_653b5c7e6d5f5', $network_true_id, $post_id);
        error_log("Updated network forum field with ID: {$network_true_id} for blog ID: {$blog_id}");
    } elseif ($forum_true_id !== null) {
        update_field('field_653b5c7e6d5f5', $forum_true_id, $post_id);
        error_log("Updated forum field with ID: {$forum_true_id} for blog ID: {$blog_id}");
    }

    // Store blog ID in post
    update_field('field_6374a3364bb73', $blog_id, $post_id);
    error_log("Updated blog ID field for post ID: {$post_id}, blog ID: {$blog_id}");

    // Notify admins if post type is 'forum' or 'theme'
    if (in_array($post_type, ['forum', 'theme'])) {
        notify_admins_of_pending_posts($post_id, $post_type);
        error_log("Admins notified for pending post ID: {$post_id}, post type: {$post_type}");
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

    $message .= sprintf('<a href="%s">%s</a>', esc_url(get_permalink($post_id)), __('See it', 'weadapt')) ;
    $message .= sprintf(' <a href="%s">%s</a>', esc_url($post_link), __('Publish / Edit / Delete it', 'weadapt'))  ;
    $message .= "Best Regards,<br>$site_name<br>";

    // Retrieve administrators' user IDs, emails, and names directly from the database
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
        $admin_name = $admin['display_name'];
        $personalized_message = "". $message;
        send_email_immediately($admin_id, $subject, $personalized_message, $post_id);
        theme_mail_save_to_db(array($admin['user_email']), $subject, $personalized_message); // Save to DB using email
    }
}