<?php

/**
 * Init theme base scripts
 */
require_once(get_theme_file_path('/base/init.php'));


/**
 * Include All Inc Files
 */
foreach (get_glob_folders_path('/inc/*/*.php') as $file_path) {
    require_once(get_theme_file_path($file_path));
}

/**
 * Register Gutenberg Blocks
 */
if (!function_exists('register_acf_blocks')) :

    function register_acf_blocks()
    {
        foreach (get_glob_folders_path('/parts/gutenberg/*/register.php') as $file_path) {
            require_once(get_theme_file_path($file_path));
        }
    }

endif;

if (function_exists('acf_register_block_type')) {
    add_action('acf/init', 'register_acf_blocks');
}

// Hook to run the function on post update
add_action('acf/save_post', 'compare_and_display_acf_group_field_values', 20);

function compare_and_display_acf_group_field_values($post_id)
{
    // Check if it's an autosave or a real save
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    // Check if the post type is the one you want to target
    if (('network' === get_post_type($post_id)) || ('theme' === get_post_type($post_id))) {
        // Get the ACF group field values before the update
        $people_values = get_field('people', $post_id);
        $editors_values = $people_values['editors'];
        if (is_array($editors_values)) {
            foreach ($editors_values as $editor) {
                $user_meta = get_userdata($editor);
                $user_roles = $user_meta->roles;
                if (!in_array('author', $user_roles)) {
                    $user_meta->add_role('author');
                }
            }
        }
        $people_values = get_field('people', $post_id);
        $contributors_values = $people_values['contributors'];

        if (is_array($contributors_values)) {
            foreach ($contributors_values as $contributor) {
                $user_data = get_userdata($contributor);

                $user_data->add_role('contributor');
            }
        }
    }
}

function grant_edit_user_roles_to_admins()
{
    $admin_role = get_role('administrator');
    $admin_role->add_cap('manage_network_users');
}
add_action('init', 'grant_edit_user_roles_to_admins');



function toast_resizable_sidebar()
{ ?>
    <style>
        .components-form-token-field__suggestions-list {
            max-height: 250px;
        }
    </style>

<?php }
add_action('admin_head', 'toast_resizable_sidebar');




/* function process_comment_notification($comment_id) {
    $comment = get_comment($comment_id);
    $post = get_post($comment->comment_post_ID);
    $post_id = $post->ID;
    $theme_id = get_post_meta($post_id, 'relevant_main_theme_network', true);

    global $wpdb;

    $table_name = $wpdb->prefix . 'wa_join';

    $query = $wpdb->prepare("SELECT user_id FROM $table_name WHERE join_id = %d", $theme_id);
    $results = $wpdb->get_results($query);

    $user_emails = array();
    if ($results) {
        foreach ($results as $result) {
            $user_emails[] = get_userdata($result->user_id)->user_email;
        }
    }

    $post_link = get_permalink($post_id);
    $article_title = get_the_title($post_id);

    foreach ($user_emails as $recipient_email) {
        $subject = 'New Comment on a Topic You Follow';
        $message = "There's a new comment on a topic you follow in a discussion you follow. <br><br>
        You can view the comment and join the discussion by clicking on the link below:<br>
        <a href='$post_link'>$article_title</a>";
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'Bcc: ' . $recipient_email,
        );
        wp_mail($recipient_email, $subject, $message, $headers);
    }
} */

function notify_admin_on_pending_comment($comment_id, $comment_approved)
{
    if ($comment_approved == 0) {
        $comment = get_comment($comment_id);
        $post = get_post($comment->comment_post_ID);
        $admin_email = get_option('admin_email');
        $subject = 'New Comment Awaiting Approval';
        $message = sprintf(
            'A new comment on the post "%s" is awaiting your approval. %s',
            $post->post_title,
            admin_url('comment.php?action=editcomment&c=' . $comment_id)
        );
        wp_mail($admin_email, $subject, $message);
    }
}


function replace_howdy($wp_admin_bar)
{
    $my_account = $wp_admin_bar->get_node('my-account');
    $greeting = str_replace('Howdy,', 'Hello,', $my_account->title);
    $wp_admin_bar->add_node(array(
        'id' => 'my-account',
        'title' => $greeting,
    ));
}
add_filter('admin_bar_menu', 'replace_howdy', 25);


// Enqueue the custom JavaScript file
function enqueue_custom_scripts()
{
    wp_enqueue_script('custom-script', get_template_directory_uri() . '/js/custom-script.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');

// Add JavaScript code to add low-quality image warning boxes
function add_low_quality_image_warning_script()
{
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function getCookie(name) {
                let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
                return match ? match[2] : null;
            }

            function addLowQualityWarning(imageLink) {
                const container = document.createElement('div');
                const element = document.createElement('p');
                container.classList.add('low-quality-image-text');
                element.innerText = 'This image is in low quality. Click here to view the high-res version';
                container.appendChild(element);
                imageLink.parentNode.appendChild(container);
            }

            function removeLowQualityWarnings() {
                const warnings = document.querySelectorAll('.low-quality-image-text');
                warnings.forEach(warning => warning.remove());
            }

            const lowQualityEnabled = getCookie('weadapt-low-quality-images') === '1';

            if (lowQualityEnabled) {
                const images = document.querySelectorAll('.wp-block-image a');
                images.forEach(imageLink => addLowQualityWarning(imageLink));
            }

            const checkbox = document.getElementById('low-quality-images');
            checkbox.addEventListener('change', function() {
                document.cookie = 'weadapt-low-quality-images=' + (this.checked ? '1' : '0') + '; path=/';
                removeLowQualityWarnings();
                if (this.checked) {
                    const images = document.querySelectorAll('.wp-block-image a');
                    images.forEach(imageLink => addLowQualityWarning(imageLink));
                }
            });
        });
    </script>
<?php
}
add_action('wp_footer', 'add_low_quality_image_warning_script');


function forum_new_post_notification($post_id)
{
    global $wpdb;
    $batch_size = 50;
    
    error_log("forum_new_post_notification triggered for post ID: $post_id");

    if (get_post_type($post_id) !== 'forum') {
        error_log("Post ID $post_id is not of type 'forum'. Exiting function.");
        return;
    }

    if (!get_field('send_notification_to_members', $post_id) ||
        get_post_meta($post_id, 'forum_notification_sent', true) ||
        get_post_status($post_id) !== 'publish' ||
        wp_is_post_revision($post_id)) {
        error_log("Post ID $post_id does not meet notification criteria. Exiting function.");
        return;
    }

    $forum_post_id = (int) get_post_meta($post_id, 'forum', true);
    if (!$forum_post_id) {
        error_log("No valid forum_post_id found for post ID $post_id. Exiting function.");
        return;
    }

    $meta_values = get_field('relevant_main_theme_network', $forum_post_id);
    if (!$meta_values) {
        error_log("No meta_values found for forum_post_id $forum_post_id. Exiting function.");
        return;
    }

    if (!is_array($meta_values)) {
        $meta_values = array($meta_values);
    }

    error_log("Processing notification for post ID $post_id and forum_post_id $forum_post_id.");

    $theme_name = get_the_title($forum_post_id);
    $post_title = get_the_title($post_id);
    $post_excerpt = wp_strip_all_tags(wp_trim_words(get_the_excerpt($post_id), 100));
    $post_link = get_permalink($post_id);
    $site_name = get_bloginfo('name');
    $subject = "New forum discussion in the $theme_name theme on weADAPT";

    $table_name = $wpdb->prefix . 'wa_join';
    $user_ids = $wpdb->get_col($wpdb->prepare(
        "SELECT user_id FROM $table_name WHERE join_id IN (%s)",
        implode(',', $meta_values)
    ));

    if (empty($user_ids)) {
        error_log("No user IDs found for post ID $post_id. Exiting function.");
        return;
    }

    $total_users = count($user_ids);
    $batches = ceil($total_users / $batch_size);

    error_log("Notifying $total_users users in $batches batches for post ID $post_id.");

    for ($i = 0; $i < $batches; $i++) {
        $batch_user_ids = array_slice($user_ids, $i * $batch_size, $batch_size);

        foreach ($batch_user_ids as $user_id) {
            $user_info = get_userdata($user_id);
            if ($user_info) {
                $display_name = $user_info->display_name;
                $message = "Hi $display_name,<br><br>";
                $message .= "We wanted to inform you of a new Forum Discussion in the <b>$theme_name</b> theme you are a member of.<br><br>
                Title: $post_title<br>
                Content: $post_excerpt<br><br>
                Reply to the conversation and engage with other members here:<br>
                <a href='$post_link'>$post_title</a><br><br>
                Thank you for your continued participation and contributions to our community.<br><br>
                If you do not want to receive any more notifications, you can unsubscribe from this theme by following this link and clicking on 'Unsubscribe Theme':<br>
                <a href='#'>$theme_name</a><br><br>
                Best Regards,<br>
                $site_name";

                error_log("Sending notification to user ID $user_id for post ID $post_id.");
                send_email_immediately(array($user_id), $subject, $message);
            } else {
                error_log("User data not found for user ID $user_id. Skipping email.");
            }
        }
         sleep(1); 
    }

    update_post_meta($post_id, 'forum_notification_sent', true);
    error_log("Notification sent and post meta updated for post ID $post_id.");
}
add_action('acf/save_post', 'forum_new_post_notification', 10, 1);


function handle_create_post()
{
    global $wpdb;
    
    // Nonce verification
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'create_post_nonce')) {
        wp_send_json_error('Nonce verification failed');
        return;
    }

    if (isset($_POST['post_title']) && isset($_POST['post_description']) && isset($_POST['post_type'])) {
        $post_title = sanitize_text_field($_POST['post_title']);
        $post_description = sanitize_textarea_field($_POST['post_description']);
        $post_type = sanitize_text_field($_POST['post_type']);

        $post_data = array(
            'post_title' => $post_title,
            'post_content' => $post_description,
            'post_status' => 'pending',
            'post_author' => get_current_user_id(),
            'post_type' => ($post_type === 'theme') ? 'article' : $post_type,
            'meta_input' => array(),
        );

        if ($post_type == 'forum' && isset($_POST['forum'])) {
            $forum_id = intval($_POST['forum']);
            error_log("Processing forum post with forum ID: $forum_id");

            $forum_true_id = $wpdb->get_var($wpdb->prepare("SELECT forum_id FROM {$wpdb->prefix}theme_forum_relationship WHERE theme_id = %d", $forum_id));
            if ($forum_true_id !== null) {
                $post_data['meta_input']['forum'] = $forum_true_id;
                error_log("Mapped forum ID: $forum_true_id");
            } else {
                error_log("No forum ID found for theme forum relationship with theme ID: $forum_id");
            }

            $network_true_id = $wpdb->get_var($wpdb->prepare("SELECT forum_id FROM {$wpdb->prefix}network_forum_relationship WHERE network_id = %d", $forum_id));
            if ($network_true_id !== null) {
                $post_data['meta_input']['network_forum'] = $network_true_id;
                error_log("Mapped network forum ID: $network_true_id");
            } else {
                error_log("No network forum ID found for network forum relationship with network ID: $forum_id");
            }
        }

        if ($post_type == 'theme' && isset($_POST['forum'])) {
            $forum_id = intval($_POST['forum']);
            error_log("Processing theme post with forum ID: $forum_id");

            $forum_true_id = $wpdb->get_var($wpdb->prepare("SELECT forum_id FROM {$wpdb->prefix}theme_forum_relationship WHERE theme_id = %d", $forum_id));
            if ($forum_true_id !== null) {
                $post_data['meta_input']['relevant_main_theme_network'] = $forum_true_id;
                error_log("Mapped relevant main theme network ID: $forum_true_id");
            } else {
                error_log("No relevant main theme network ID found for theme forum relationship with theme ID: $forum_id");
            }
        }

        // Insert post
        $post_id = wp_insert_post($post_data);

        if (is_wp_error($post_id)) {
            error_log("Error creating post: " . $post_id->get_error_message());
            wp_send_json_error('Error creating post: ' . $post_id->get_error_message());
        } else {
            error_log("Post created successfully with ID: $post_id");
            if (in_array($post_type, ['forum', 'theme'])) {
                notify_admins_of_pending_posts($post_id, $post_type);
            }
            wp_send_json_success('Post created successfully');
        }
    } else {
        error_log("Missing required POST fields");
        wp_send_json_error('Missing required POST fields');
    }
}
add_action('admin_post_nopriv_create_post', 'handle_create_post');
add_action('admin_post_create_post', 'handle_create_post');
function notify_admins_of_pending_posts($post_id, $post_type)
{
    global $wpdb;


    $post_title = get_the_title($post_id);
    $post_link = get_edit_post_link($post_id);
    $site_name = get_bloginfo('name');
    $subject = '';
    $message = '';

    // Construct the email based on post type
    if ($post_type == 'forum') {
        $forum_id = get_field('forum', $post_id);
        if (!$forum_id) {
            return;
        }
        $forum_name = get_the_title($forum_id);
        $subject = sprintf(__('Content has been submitted for review on [%s]', 'weadapt'), get_bloginfo('name'));

        $message = "A new forum topic titled <b>$post_title</b> in the forum <b>$forum_name</b> is pending review.<br><br>";

        $message .= sprintf(' — <a href="%s">%s</a>', get_permalink($post_id), __('See it', 'weadapt')) . '<br>';
        $message .= sprintf(' — <a href="%s">%s</a>', get_edit_post_link($post_id), __('Publish / Edit / Delete it', 'weadapt')) . '<br><br>';

        $message .= "Best Regards,<br>
        $site_name,<br>";
    } elseif ($post_type == 'theme') {
        $theme_id = get_field('relevant_main_theme_network', $post_id);
        if (!$theme_id) {
            return;
        }
        $theme_name = get_the_title($theme_id);
        $subject = "New theme post pending review";
        $message = "A new post titled <b>$post_title</b> in the theme <b>$theme_name</b> is pending review.<br><br>
        <a href='$post_link'>$post_title</a><br>";

        // Adding links to view and edit the post
        $message .= sprintf(' — <a href="%s">%s</a>', get_permalink($post_id), __('See it', 'weadapt')) . '<br>';
        $message .= sprintf(' — <a href="%s">%s</a>', get_edit_post_link($post_id), __('Publish / Edit / Delete it', 'weadapt')) . '<br><br>';

        $message .= "Best Regards,<br>
        $site_name,<br>";
    } else {
        return;
    }

    // Query the database for administrators and their names
    $admins_info = $wpdb->get_results("
        SELECT user_email, display_name
        FROM {$wpdb->users} u
        INNER JOIN {$wpdb->usermeta} um ON u.ID = um.user_id
        WHERE um.meta_key = '{$wpdb->prefix}capabilities'
        AND um.meta_value LIKE '%administrator%'
    ", ARRAY_A);

    if (empty($admins_info)) {
        return;
    }

    $headers = array('Content-Type: text/html; charset=UTF-8');

    foreach ($admins_info as $admin) {
        $admin_email = $admin['user_email'];
        $admin_name = $admin['display_name'];
        $personalized_message = sprintf(__('Dear %s,', 'weadapt'), esc_html($admin_name)) . "<br><br>" . $message;
        wp_mail($admin_email, $subject, $personalized_message, $headers);
        theme_mail_save_to_db(array($admin_email), $subject, $personalized_message);
    }
}
function restrict_editors_to_own_posts($query)
{
    if (is_admin() && $query->is_main_query() && current_user_can('contributor') && !current_user_can('edit_others_posts')) {
        global $user_ID;
        $query->set('author', $user_ID);
        add_action('admin_notices', 'editor_notice');
    }
}
add_action('pre_get_posts', 'restrict_editors_to_own_posts');

function editor_notice()
{
    if (current_user_can('editor') && !current_user_can('edit_others_posts')) {
        echo '<div class="notice notice-info is-dismissible">
                <p>You are only able to view and manage your own posts.</p>
              </div>';
    }
}
function create_forum_post_on_theme_creation($new_status, $old_status, $post)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'theme_forum_relationship';

    if ($post->post_type == 'theme' && $old_status == 'auto-draft' && $new_status == 'publish') {

        $forum_post = array(
            'post_title'    => $post->post_title,
            'post_content'  => 'This is a forum linked to the theme: ' . $post->post_title,
            'post_status'   => 'publish',
            'post_type'     => 'forums'
        );
        $forum_post_id = wp_insert_post($forum_post);
        if ($forum_post_id != 0) {
            $wpdb->insert(
                $table_name,
                array(
                    'theme_id' => $post->ID,
                    'forum_id' => $forum_post_id
                ),
                array(
                    '%d',
                    '%d'
                )
            );
        }
    }
}
function notify_admin_on_edit($new_status, $old_status, $post)
{
    $ignored_old_statuses = array('auto-draft', 'inherit', 'new', 'draft', 'future', 'pending', 'trash');
    if (in_array($old_status, $ignored_old_statuses)) {
        return;
    }

    if (($old_status === 'publish' && in_array($new_status, array('pending', 'draft'))) ||
        ($old_status === 'draft' && $new_status === 'pending')
    ) {

        $transient_key = 'notify_admin_on_edit_' . $post->ID;

        if (get_post_meta($post->ID, '_notify_admin_on_edit_sent', true) || get_transient($transient_key)) {
            return;
        }
        global $wpdb;
        $admin_emails = $wpdb->get_col("SELECT DISTINCT user_email FROM $wpdb->users u 
                                        JOIN $wpdb->usermeta um ON u.ID = um.user_id 
                                        WHERE um.meta_key = '{$wpdb->prefix}capabilities' 
                                        AND um.meta_value LIKE '%\"administrator\"%'");

        $website_name = get_bloginfo('name');
        $summary = $post->post_excerpt ? $post->post_excerpt : wp_trim_words($post->post_content, 55, '...');
        $author_id = $post->post_author;
        $author_info = get_userdata($author_id);

        $subject = sprintf(__('Content has been submitted for review on %s', 'your-text-domain'), $website_name);
        $message = sprintf(
            __('%1$s %2$s (%3$s) has sent you content for review.', 'your-text-domain'),
            $author_info->first_name,
            $author_info->last_name,
            $author_info->user_login
        );
        $message .= '<br>';
        $message .= __('Content:', 'your-text-domain') . ' ' . get_the_title($post->ID) . '<br>';
        $message .= __('Summary:', 'your-text-domain') . ' ' . wp_strip_all_tags($summary) . '<br>';
        $message .= '<a href="' . get_permalink($post->ID) . '">' . __('Go to the content', 'your-text-domain') . '</a><br>';
        $message .= '<a href="' . get_edit_post_link($post->ID) . '">' . __('Publish/Edit', 'your-text-domain') . '</a>';

        foreach ($admin_emails as $admin_email) {
            wp_mail($admin_email, $subject, $message);
        }

        theme_mail_save_to_db($admin_emails, $subject, $message);
        set_transient($transient_key, true, 10);
        update_post_meta($post->ID, '_notify_admin_on_edit_sent', true);
    }
}
add_action('transition_post_status', 'notify_admin_on_edit', 10, 3);


add_action('save_post', function ($post_id) {
    delete_post_meta($post_id, '_notify_admin_on_edit_sent');
});



//forum notification



function get_editors_by_theme($theme)
{
    $args = array(
        'role'    => 'editor',
        'meta_key' => 'user_theme',
        'meta_value' => $theme,
    );
    $user_query = new WP_User_Query($args);
    $editors = $user_query->get_results();
    return $editors;
}

function notify_editors_after_publish($post_id, $new_theme)
{
    $post = get_post($post_id);
    if (!$post) {
        return;
    }
    $post_type_forum_topic = get_post_type($post_id);
    if ($post_type_forum_topic == 'forum') {
        $forum_id = get_post_meta($post_id, 'forum', true);
        $theme_ids = get_post_meta($forum_id, 'relevant_main_theme_network', true);
        $author = get_post_meta($post_id, 'author', true);
        $author = is_array($author) ? $author : array($author);
        $users = array();
        $published_for_the_first_time = (strtotime($post->post_date) >= (time() - 60));
        $notification_sent = get_post_meta($post_id, '_notification_sent', true);

        if ($notification_sent) {
            return;
        }
        if (is_array($theme_ids)) {
            foreach ($theme_ids as $theme_network) {
                if ($main_theme_network_editors = get_field('people_editors', $theme_network)) {
                    if ($published_for_the_first_time) {
                        $admins = get_blog_administrators(false, 1);
                        $users = array_merge($users, $main_theme_network_editors, $admins);
                    } else {
                        $users = array_merge($users, $main_theme_network_editors);
                    }
                }
            }
        }
        $users = array_unique($users);
        if (!empty($users)) {
            if ($post->post_type == 'article' || $post->post_type == 'event' || $post->post_type == 'organisation') {
                $subject = sprintf(
                    __('An %s has been published on %s', 'weadapt'),
                    ucfirst($post->post_type),
                    get_bloginfo('name')
                );
            } else {
                $subject = sprintf(
                    __('A %s has been published on %s', 'weadapt'),
                    ucfirst($post->post_type),
                    get_bloginfo('name')
                );
            }
            $message = esc_html($post->post_title) . '<br>' . esc_html($post->post_excerpt) . '<br><br>';

            $post_excerpt = get_the_excerpt($post);
            $post_excerpt = wp_strip_all_tags($post_excerpt);
            $post_excerpt = mb_strimwidth($post_excerpt, 0, 100, '...');
            $message .= sprintf(
                __('Summary: %s', 'weadapt'),
                esc_html($post_excerpt)
            ) . '<br><br>';
            if ($post_author_ID = get_post_meta($post_id, 'author', true)) {

                $post_author = get_userdata($post_author_ID);
                $author_organisations = get_field('organisations', $post_author);

                if ($author_organisations) {
                    $message .= sprintf('by %s from %s', $post_author->display_name, get_the_title($author_organisations[0]));
                } else {
                    $message .= sprintf('by %s', $post_author->display_name);
                }
                $message .= '<br>';
            }

            $message .= sprintf(' — <a href="%s">%s</a>', get_permalink($post_id), __('See it', 'weadapt')) . '<br>';
            $message .= sprintf(' — <a href="%s">%s</a>', get_edit_post_link($post_id), __('Publish / Edit / Delete it', 'weadapt'));

            theme_mail_save_to_db($users, $subject, $message);
            send_email_immediately($users, $subject, $message);
        }

        $valid_contributors = get_field('people_contributors', $post_id) ?: array();
        $valid_contributors = array_merge($author, $valid_contributors);
        if (!empty($valid_contributors)) {
            $subject = sprintf(
                __('Your %s has been published on %s', 'weadapt'),
                ucfirst($post->post_type),
                get_bloginfo('name')
            );

            $message = __('Your content has now been reviewed and published. It will be shared on our social media channels where relevant. Please do re-share! ', 'weadapt') . '<br><br>';
            $message .= esc_html($post->post_title) . '<br>';
            $message .= esc_html($post->post_excerpt) . '<br><br>';

            if (!empty($people_creator)) {
                $post_author_ID = $people_creator[0];
                $post_author = new WP_User($post_author_ID);
                $author_organisations = get_field('organisations', $post_author);

                if ($author_organisations) {
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
                get_permalink($post_id),
                __('See it', 'weadapt')
            ) . '<br>';
            $message .= sprintf(
                ' — <a href="%s">%s</a>',
                get_edit_post_link($post_id),
                __('Edit it', 'weadapt')
            );

            theme_mail_save_to_db(
                $valid_contributors,
                $subject,
                $message
            );
            send_email_immediately($valid_contributors, $subject, $message);
            update_post_meta($post_id, '_notification_sent', true);
        }
    }
    // Your %s content has been published 
    else {
        $notification_sent = get_post_meta($post_id, '_notification_sent', true);
        $published_for_the_first_time = (strtotime($post->post_date) >= (time() - 300));
        if ($notification_sent) {
            return;
        }
        $users = [];

        if (is_array($main_theme_network = get_field('relevant_main_theme_network', $post_id))) {
            $users = []; 
            if (is_array($main_theme_network = get_field('relevant_main_theme_network', $post_id))) {

                foreach ($main_theme_network as $theme_network) {
                    if ($main_theme_network_editors = get_field('people_editors', $theme_network)) {
            
                        if ($published_for_the_first_time) {
                            $admins = get_blog_administrators(false, 1);
                            if (is_array($admins)) {
                                $admins = array_filter($admins, function ($user_id) {
                                    return user_can($user_id, 'administrator');
                                });
                                $users = array_merge($users, $main_theme_network_editors, $admins);
                            }
                        } else {
                            $users = array_merge($users, $main_theme_network_editors);
                        }
                    }
                }
            }

            $users = array_unique($users); 
        }

        $users = array_unique($users);
        if ($published_for_the_first_time) {
            $valid_contributors = get_field('people_contributors', $post_id) ?: array();
            $people_creator = get_field('people_creator', $post_id) ?: array();
            $valid_contributors = array_merge($valid_contributors, $people_creator);

            if (!empty($valid_contributors)) {
                $subject = sprintf(
                    __('Your %s has been published on %s', 'weadapt'),
                    ucfirst($post->post_type),
                    get_bloginfo('name')
                );

                $message = __('Your content has now been reviewed and published. It will be shared on our social media channels where relevant. Please do re-share! ', 'weadapt') . '<br><br>';
                $message .= esc_html($post->post_title) . '<br>';
                $message .= esc_html($post->post_excerpt) . '<br><br>';

                if (!empty($people_creator)) {
                    $post_author_ID = $people_creator[0];
                    $post_author = new WP_User($post_author_ID);
                    $author_organisations = get_field('organisations', $post_author);

                    if ($author_organisations) {
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
                    get_permalink($post_id),
                    __('See it', 'weadapt')
                ) . '<br>';
                $message .= sprintf(
                    ' — <a href="%s">%s</a>',
                    get_edit_post_link($post_id),
                    __('Edit it', 'weadapt')
                );

                theme_mail_save_to_db(
                    $valid_contributors,
                    $subject,
                    $message
                );
                send_email_immediately($valid_contributors, $subject, $message);
                update_post_meta($post_id, '_notification_sent', true);
            }

            // an article has been published on weadapt
            if (!empty($users)) {
                if ($post->post_type == 'article' || $post->post_type == 'event' || $post->post_type == 'organisation') {
                    $subject = sprintf(
                        __('An %s has been published on %s', 'weadapt'),
                        ucfirst($post->post_type),
                        get_bloginfo('name')
                    );
                } else {
                    $subject = sprintf(
                        __('A %s has been published on %s', 'weadapt'),
                        ucfirst($post->post_type),
                        get_bloginfo('name')
                    );
                }

                $message = esc_html($post->post_title) . '<br>' . esc_html($post->post_excerpt) . '<br><br>';
                if ($published_for_the_first_time) {
                    if ($post_author_IDs = get_field('people_creator', $post_id)) {
                        $post_author_ID = $post_author_IDs[0];
                        $post_author = new WP_User($post_author_ID);
                        $author_organisations = get_field('organisations', $post_author);

                        if ($author_organisations) {
                            $message .= sprintf('by %s from %s', $post_author->display_name, get_the_title($author_organisations[0]));
                        } else {
                            $message .= sprintf('by %s', $post_author->display_name);
                        }
                        $message .= '<br>';
                    }

                    $message .= sprintf(' — <a href="%s">%s</a>', get_permalink($post_id), __('See it', 'weadapt')) . '<br>';
                    $message .= sprintf(' — <a href="%s">%s</a>', get_edit_post_link($post_id), __('Publish / Edit / Delete it', 'weadapt'));

                    theme_mail_save_to_db($users, $subject, $message);
                    send_email_immediately($users, $subject, $message);
                }
            }
        }
        // related to your theme/network 
        if ($published_for_the_first_time) {
            $related_themes_networks = get_field('relevant_themes_networks', $post);     
            if (!empty($related_themes_networks)) {
                error_log('related $', $related_themes_networks );
                foreach ($related_themes_networks as $theme_network_ID) {
                    $theme_network_editors = get_field('people_editors', $theme_network_ID);
                    if (!empty($theme_network_editors)) {
                        $theme_name = get_the_title($theme_network_ID);
                        $theme_editors_user_ids = [];

                        foreach ($theme_network_editors as $editor_id) {
                            $editor_data = get_userdata($editor_id);
                            if ($editor_data && isset($editor_data->user_email)) {
                                $theme_editors_user_ids[] = $editor_id;
                            }
                        }
                        $theme_editors_user_ids = array_unique($theme_editors_user_ids);

                        if (!empty($theme_editors_user_ids)) {
                            $subject = sprintf(
                                __('Content has been published on %s which is related to your theme / network', 'weadapt'),
                                get_bloginfo('name')
                            );

                            $post_author_IDs = get_field('people_creator', $post);
                            $post_author = !empty($post_author_IDs) ? new WP_User($post_author_IDs[0]) : false;

                            if (!empty($post_author)) {
                                $message = sprintf(
                                    __('Content has been published by <a href="%s">%s %s (%s)</a> on %s which is related to your theme / network: %s.', 'weadapt'),
                                    get_author_posts_url($post_author->ID),
                                    esc_attr($post_author->first_name),
                                    esc_attr($post_author->last_name),
                                    esc_attr($post_author->user_login),
                                    get_bloginfo('name'),
                                    esc_html($theme_name)
                                ) . '<br><br>';
                            } else {
                                $message = sprintf(
                                    __('Content has been published on %s which is related to your theme / network: %s.', 'weadapt'),
                                    get_bloginfo('name'),
                                    esc_html($theme_name)
                                ) . '<br><br>';
                            }

                            $post_excerpt = get_the_excerpt($post);
                            $post_excerpt = wp_strip_all_tags($post_excerpt);
                            $post_excerpt = mb_strimwidth($post_excerpt, 0, 100, '...');

                            $message .= __('You may like to create a link to it from your Theme / Network or discuss it in a Learning Forum.', 'weadapt') . '<br><br>';
                            $message .= sprintf(
                                __('Content: %s', 'weadapt'),
                                esc_html($post->post_title)
                            ) . '<br>';
                            $message .= sprintf(
                                __('Summary: %s', 'weadapt'),
                                esc_html($post_excerpt)
                            ) . '<br><br>';
                            $message .= sprintf(
                                '<a href="%s">%s</a>',
                                get_permalink($post),
                                __('Go to the content', 'weadapt')
                            );
                            theme_mail_save_to_db(
                                $theme_editors_user_ids,
                                $subject,
                                $message
                            );
                            send_email_immediately($theme_editors_user_ids, $subject, $message);
                        }
                    }
                }
            }

            set_transient('notified_post_' . $post_id, true, 60);
            update_post_meta($post_id, '_notification_sent', true);
        }
    }
}

add_action('notify_editors_after_publish', 'notify_editors_after_publish', 10, 2);



function update_theme_meta($post_id, $post = null, $update = null)
{
    if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
        return;
    }

    if (!$post) {
        $post = get_post($post_id);
    }

    if (!is_mailed_post_type($post->post_type)) {
        return;
    }

    $old_theme = get_post_meta($post_id, '_relevant_main_theme_network_old', true);
    $new_themes = get_field('relevant_main_theme_network', $post_id);

    $old_people_contributor = get_post_meta($post_id, 'people_contributors', true);
    $new_people_contributor = get_field('people_contributors', $post_id);

    $old_relevent_theme = get_post_meta($post_id, 'relevant_themes_networks', true);
    $new_relevent_theme = get_field('relevant_themes_networks', $post_id);

    if ($old_theme !== $new_themes || $old_people_contributor !== $new_people_contributor) {
        if ($old_theme !== $new_themes) {
            update_post_meta($post_id, '_relevant_main_theme_network_old', $new_themes);
        }
        if ($old_people_contributor !== $new_people_contributor) {
            update_post_meta($post_id, 'people_contributors', $new_people_contributor);
        }
        if ($old_relevent_theme !== $new_relevent_theme) {
            update_post_meta($post_id, 'relevant_themes_networks', $new_relevent_theme);
        }
    }

    if ($post->post_status === 'publish' && ($old_theme !== $new_themes || $old_people_contributor !== $new_people_contributor || $old_relevent_theme !== $new_relevent_theme)) {
        delete_transient('notified_post_' . $post_id);
        do_action('notify_editors_after_publish', $post_id, $new_themes);
    }
}

add_action('acf/save_post', 'update_theme_meta', 10, 3);

function create_network_forum_relationship_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'network_forum_relationship';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        forum_id mediumint(9) NOT NULL,
        network_id mediumint(9) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function migrate_forum_to_network_relationship() {
    global $wpdb;
    if (!isset($_POST['forum_migration_nonce']) || !wp_verify_nonce($_POST['forum_migration_nonce'], 'forum_migration_action')) {
        error_log('Nonce verification failed.');
        return;
    }
    $network_posts = $wpdb->get_results("
        SELECT p.ID 
        FROM {$wpdb->posts} p
        WHERE p.post_type = 'network'
    ");

    foreach ($network_posts as $post) {
        $network_id = $post->ID;
        error_log('Processing network post ID: ' . $network_id);
        $network_post = get_post($network_id);

        if ($network_post) {
            $new_forum_post = array(
                'post_title'    => $network_post->post_title,
                'post_content'  => $network_post->post_content,
                'post_status'   => 'publish',
                'post_author'   => $network_post->post_author,
                'post_type'     => 'forums'
            );

            $new_forum_id = wp_insert_post($new_forum_post);

            if (is_wp_error($new_forum_id)) {
            } else {
                

                $wpdb->insert(
                    "{$wpdb->prefix}network_forum_relationship",
                    array(
                        'forum_id' => $new_forum_id,
                        'network_id' => $network_id
                    ),
                    array(
                        '%d',
                        '%d'
                    )
                );
            }
        }
    }
}