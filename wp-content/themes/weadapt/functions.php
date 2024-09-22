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


function get_admin_info()
{
    global $wpdb;
    $admin_info = get_transient('cached_admin_info');
    if ($admin_info === false) {
        $admin_info = $wpdb->get_results("
            SELECT user_email, display_name, ID
            FROM {$wpdb->users} u
            INNER JOIN {$wpdb->usermeta} um ON u.ID = um.user_id
            WHERE um.meta_key = '{$wpdb->prefix}capabilities'
            AND um.meta_value LIKE '%administrator%' ", ARRAY_A);
        set_transient('cached_admin_info', $admin_info, WEEK_IN_SECONDS);
    }

    return $admin_info;
}

if (! function_exists('use_an_or_a')) {
    function use_an_or_a($word)
    {
        $first_letter = strtolower(substr($word, 0, 1));
        $vowels = ['a', 'e', 'i', 'o', 'u'];
        return in_array($first_letter, $vowels) ? 'an' : 'a';
    }
}

function forum_new_post_notification($post_id)
{
    global $wpdb;
    $batch_size = 50;

    error_log("forum_new_post_notification triggered for post ID: $post_id");

    if (get_post_type($post_id) !== 'forum') {
        error_log("Post ID $post_id is not of type 'forum'. Exiting function.");
        return;
    }

    if (
        !get_field('send_notification_to_members', $post_id) ||
        get_post_meta($post_id, 'forum_notification_sent', true) ||
        get_post_status($post_id) !== 'publish' ||
        wp_is_post_revision($post_id)
    ) {
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
                send_email_immediately(array($user_id), $subject, $message, $post_id);
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


function restrict_contributor_posts_access($query)
{
    if (is_admin() && $query->is_main_query() && current_user_can('contributor') && !current_user_can('edit_others_posts')) {
        global $user_ID;
        $query->set('author', $user_ID);

        $post_type = isset($_GET['post_type']) ? $_GET['post_type'] : '';
        if (in_array($post_type, ['members', 'stakeholders'])) {
            wp_redirect(admin_url());
            exit;
        }
    }
}
add_action('pre_get_posts', 'restrict_contributor_posts_access');

function hide_menu_items_and_plugin_alerts()
{
    if (current_user_can('contributor') && !current_user_can('edit_others_posts')) {
        remove_menu_page('edit.php?post_type=members');
        remove_menu_page('edit.php?post_type=stakeholders');

        remove_action('admin_notices', 'update_nag', 3);
        remove_action('network_admin_notices', 'update_nag', 3);
    }
}
add_action('admin_menu', 'hide_menu_items_and_plugin_alerts', 999);
function hide_plugin_update_rows_for_contributors()
{
    if (current_user_can('contributor') && !current_user_can('edit_others_posts')) {
        echo '<style>
                .notice {
                    display: none;
                }
              </style>';
    }
}
add_action('admin_head', 'hide_plugin_update_rows_for_contributors');

function editor_notice()
{
    if (current_user_can('editor') && !current_user_can('edit_others_posts')) {
        echo '<div class="notice notice-info is-dismissible">
                <p>You are only able to view and manage your own posts.</p>
              </div>';
    }
}
function create_forum_post_on_theme_creation($new_status, $old_status, $post) {
    if ($post->post_type !== 'theme' || $old_status !== 'auto-draft' || $new_status !== 'publish') {
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix .'theme_forum_relationship';

    $existing_forum = $wpdb->get_var($wpdb->prepare(
        "SELECT forum_id FROM $table_name WHERE theme_id = %d", 
        $post->ID
    ));

    if ($existing_forum) {
        return;
    }

    $forum_post = array(
        'post_title'    => $post->post_title,
        'post_content'  => 'This is a forum linked to the theme: ' . $post->post_title,
        'post_status'   => 'publish',
        'post_type'     => 'forums'
    );

    $forum_post_id = wp_insert_post($forum_post);

    if ($forum_post_id) {
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
add_action('transition_post_status', 'create_forum_post_on_theme_creation', 10, 3);


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
        $admins = get_blog_administrators(false, 1);
        if ($admins) {
            $users = array_merge($users, $admins);
        }
        $users = array_unique($users);
        if (!empty($users)) {
            $subject = sprintf(
                __('A Forum has been published on ', 'weadapt')
            );

            // Fetching publish_to meta and adding blog names to the subject
            $publish_to = get_post_meta($post_id, 'publish_to', true);
            if (is_array($publish_to)) {
                $blog_names = [];
                foreach ($publish_to as $blog_id) {
                    $blog_name = get_blog_details($blog_id)->blogname;
                    $blog_names[] = $blog_name;
                }
                $subject .= implode(', ', $blog_names);
            } else {
                $subject .= get_bloginfo('name');
            }

            $message = '<p>' . $subject . '</p>';
            $message .= '<strong><p>' . esc_html($post->post_title) . '</p></strong>';
            $message .= '<p>' . esc_html($post->post_excerpt) . '</p>';

            $post_excerpt = get_the_excerpt($post);
            $post_excerpt = wp_strip_all_tags($post_excerpt);
            $post_excerpt = mb_strimwidth($post_excerpt, 0, 100, '...');

            $message .= '<p>' . sprintf(__('Summarys: %s', 'weadapt'), esc_html($post_excerpt)) . '</p>';
            $message .= '<p> <a href="' . get_permalink($post_id) . '">View Discussions</a></p>';
            $message .= "<br>Best Regards,<br>WeAdapt";
            if ($post_author_ID = get_post_meta($post_id, 'author', true)) {
                $post_author = get_userdata($post_author_ID);
                $author_organisations = get_field('organisations', $post_author);

                if ($author_organisations) {
                    $message .= '<p>' . sprintf(__('By %s from %s', 'weadapt'), $post_author->display_name, get_the_title($author_organisations[0])) . '</p>';
                } else {
                    $message .= '<p>' . sprintf(__('By %s', 'weadapt'), $post_author->display_name) . '</p>';
                }
            }
            theme_mail_save_to_db($users, $subject, $message);
            send_email_immediately($users, $subject, $message, $post_id);
        }

        $valid_contributors = get_field('people_contributors', $post_id) ?: array();
        $valid_contributors = array_merge($author, $valid_contributors);
        if (!empty($valid_contributors)) {
            $subject = sprintf(
                __('Your %s has been published on %s', 'weadapt'),
                ucfirst($post->post_type),
                get_bloginfo('name')
            );

            $message = __('Your content has been reviewed and is now published. We will share it on our social media channels where relevant. Please feel free to share it with your network as well! ', 'weadapt') . '<br><br>';
            $message .= esc_html($post->post_title);
            $message .= esc_html($post->post_excerpt);

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
                '  <a href="%s">%s</a>',
                get_permalink($post_id),
                __('View your content', 'weadapt')
            ) . '<br>';
            $message .= sprintf(
                '  <a href="%s">%s</a>',
                get_edit_post_link($post_id),
                __('Edit it', 'weadapt')
            );
            $message .= "<br>Best Regards,<br>WeAdapt";

            theme_mail_save_to_db(
                $valid_contributors,
                $subject,
                $message
            );
            send_email_immediately($valid_contributors, $subject, $message, $post_id);
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
            $post_author_ID = get_post_field('post_author', $post_id);
            error_log('this is the post author'. $post_author_ID);
            $valid_contributors = array_merge($valid_contributors, $people_creator, array($post_author_ID));            if (!empty($valid_contributors)) {
            $subject = sprintf(
                    __('Your %s has been published on %s', 'weadapt'),
                    ucfirst($post->post_type),
                    get_bloginfo('name')
                );
                $post_excerpt = get_the_excerpt($post);
                $post_excerpt = wp_strip_all_tags($post_excerpt);
                $post_excerpt = mb_strimwidth($post_excerpt, 0, 100, '...');
                $message = __('Your content has now been reviewed and published. It will be shared on our social media channels where relevant. Please do re-share! ', 'weadapt') . '<br><br>';
                $message .= '<strong>' . __('Title: ', 'weadapt') . '</strong>' . esc_html($post->post_title);
                $message .= sprintf(__('<br> <br> <strong>Summary : </strong>  %s  ', 'weadapt'), esc_html($post_excerpt));
                $message .= esc_html($post->post_excerpt);
                if (!empty($people_creator)) {
                    $post_author_ID = $people_creator[0];
                    $post_author = new WP_User($post_author_ID);
                    $author_organisations = get_field('organisations', $post_author);
                }
                $message .= sprintf(
                    '  <a href="%s">%s</a>',
                    get_permalink($post_id),
                    __('View your content', 'weadapt')
                ) . '<br>';
                $message .= sprintf(
                    '  <a href="%s">%s</a>',
                    get_edit_post_link($post_id),
                    __('Edit it', 'weadapt')
                );
                $message .= "<br>Best Regards,<br>WeAdapt";

                theme_mail_save_to_db(
                    $valid_contributors,
                    $subject,
                    $message
                );
                send_email_immediately($valid_contributors, $subject, $message, null);
                update_post_meta($post_id, '_notification_sent', true);
            }

            // an article has been published on weadapt
            $admins = get_blog_administrators(false, 1);
            if ($admins) {
                $users = array_merge($users, $admins);
            }
            $users = array_unique($users);
            if (!empty($users)) {
                $article_or_an = use_an_or_a($post->post_type);

                $subject = sprintf(
                    __('%s %s has been published on ', 'weadapt'),
                    ucfirst($article_or_an),
                    ucfirst($post->post_type)
                );

                // Fetching the 'publish_to' meta field
                $publish_to = get_post_meta($post_id, 'publish_to', true);
                if (is_array($publish_to)) {
                    $blog_names = [];
                    foreach ($publish_to as $blog_id) {
                        $blog_name = get_blog_details($blog_id)->blogname;
                        $blog_names[] = $blog_name;
                    }
                    $subject .= implode(', ', $blog_names);
                } else {
                    $subject .= get_bloginfo('name');
                }
                $message  = esc_html($subject) .  '<br> <br> <strong>' . __('Title: ', 'weadapt') . '</strong>' .  esc_html($post->post_title) . '<br><br>';
                $post_excerpt = get_the_excerpt($post);
                $post_excerpt = wp_strip_all_tags($post_excerpt);
                $post_excerpt = mb_strimwidth($post_excerpt, 0, 100, '...');

                $message .= sprintf(__('<strong> Summary: </strong>%s', 'weadapt'), esc_html($post_excerpt));
                if ($published_for_the_first_time) {
                    if ($post_author_IDs = get_field('people_creator', $post_id)) {
                        $post_author_ID = $post_author_IDs[0];
                        $post_author = new WP_User($post_author_ID);
                        $author_organisations = get_field('organisations', $post_author);

                        if ($author_organisations) {
                            $message .= sprintf('Published by %s from %s', $post_author->display_name, get_the_title($author_organisations[0]));
                        } else {
                            $message .= sprintf('Published by %s', $post_author->display_name);
                        }
                    }

                    $message .= sprintf('  <a href="%s">%s</a>', get_permalink($post_id), __('View the content', 'weadapt')) . '<br>';
                    $message .= sprintf('  <a href="%s">%s</a>', get_edit_post_link($post_id), __('Edit / Delete it', 'weadapt'));
                    $message .= "<br>Best Regards,<br>WeAdapt";
                    theme_mail_save_to_db($users, $subject, $message);
                    send_email_immediately($users, $subject, $message, $post_id);
                } 
            } 
        }
        // related to your theme/network 
        if ($published_for_the_first_time) {
            $related_themes_networks = get_field('relevant_themes_networks', $post);
            if (!empty($related_themes_networks)) {
                error_log('related $', $related_themes_networks);
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
                                );
                            } else {
                                $message = sprintf(
                                    __('Content has been published on %s which is related to your theme / network: %s.', 'weadapt'),
                                    get_bloginfo('name'),
                                    esc_html($theme_name)
                                );
                            }

                            $post_excerpt = get_the_excerpt($post);
                            $post_excerpt = wp_strip_all_tags($post_excerpt);
                            $post_excerpt = mb_strimwidth($post_excerpt, 0, 100, '...');

                            $message .= __('You may like to create a link to it from your Theme / Network or discuss it in a Learning Forum.', 'weadapt') . '<br><br>';
                            $message .= sprintf(
                                __('Content: %s', 'weadapt'),
                                esc_html($post->post_title)
                            );
                            $message .= sprintf(
                                __('Summary: %s', 'weadapt'),
                                esc_html($post_excerpt)
                            );
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
                            send_email_immediately($theme_editors_user_ids, $subject, $message, $post_id);
                        }
                    }
                }
            }

            set_transient('notified_post_' . $post_id, true, 60);
            update_post_meta($post_id, '_notification_sent', true);
        }
    }
    error_log("Finished processing post ID: $post_id");
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

function create_network_forum_relationship_table()
{
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

function migrate_forum_to_network_relationship()
{
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
add_action('admin_menu', 'register_user_migration_menu_page');

function register_user_migration_menu_page()
{
    add_menu_page(
        'User to Member Migration',   // Page title
        'User Migration',             // Menu title
        'manage_options',             // Capability
        'user-migration',             // Menu slug
        'user_migration_menu_page_html', // Callback function
        'dashicons-admin-users',      // Icon
        21                            // Position (appears after the Forum Migration menu)
    );
}

function user_migration_menu_page_html()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['migrate_and_convert'])) {
        migrate_and_convert_user_to_member();
    }

    if (isset($_POST['update_migrations'])) {
        update_migrated_user_locations();
    }

    if (isset($_POST['delete_migrated_members'])) {
        delete_migrated_members();
        echo '<div class="notice notice-success is-dismissible"><p>All migrated members have been deleted successfully.</p></div>';
    }

?>
    <div class="wrap">
        <h1>User to Member Migration</h1>
        <form method="post" action="">
            <?php wp_nonce_field('user_migration_action', 'user_migration_nonce'); ?>
            <input type="submit" name="migrate_and_convert" class="button button-primary" value="Migrate and Convert">
            <input type="submit" name="update_migrations" class="button button-secondary" value="Update Migrations">
            <input type="submit" name="delete_migrated_members" class="button button-tertiary" value="Delete Migrated Members">
        </form>
    </div>
<?php
}



function migrate_and_convert_user_to_member()
{
    global $wpdb;

    // Verify nonce
    if (!isset($_POST['user_migration_nonce']) || !wp_verify_nonce($_POST['user_migration_nonce'], 'user_migration_action')) {
        error_log('Nonce verification failed.');
        return;
    }

    // Query the database directly for users who match the criteria in wp_usermeta
    $users = $wpdb->get_results("
        SELECT u.ID, u.user_login 
        FROM {$wpdb->users} u
        INNER JOIN {$wpdb->usermeta} um ON u.ID = um.user_id
        WHERE um.meta_key = 'primary_blog' AND um.meta_value = '11'
    ");

    if (empty($users)) {
        echo '<div class="notice notice-error is-dismissible"><p>No users found to migrate.</p></div>';
        return;
    }

    $migration_done = false;
    $migration_count = 0;  // Counter for successful migrations
    $conversion_done = false;

    foreach ($users as $user) {
        $user_id = $user->ID;
        $username = $user->user_login;

        error_log('Processing user ID: ' . $user_id);

        // Check if a member post already exists for this username
        $existing_member = $wpdb->get_var($wpdb->prepare("
            SELECT ID 
            FROM {$wpdb->posts} 
            WHERE post_type = 'members' 
            AND post_title = %s
        ", $username));

        if ($existing_member) {
            error_log('User ' . $username . ' is already migrated with member post ID: ' . $existing_member);
            continue;
        }

        // Fetch user meta directly
        $address_country = get_user_meta($user_id, 'address_country', true);
        $address_city = get_user_meta($user_id, 'address_city', true);
        $address_county = get_user_meta($user_id, 'address_county', true);

        // Ensure the address fields are strings, not arrays
        $address_country = is_array($address_country) ? implode(', ', $address_country) : $address_country;
        $address_city = is_array($address_city) ? implode(', ', $address_city) : $address_city;
        $address_county = is_array($address_county) ? implode(', ', $address_county) : $address_county;

        // Create a new member post with the user details
        $new_member_post = array(
            'post_title'    => $username,
            'post_status'   => 'publish',
            'post_author'   => $user_id,
            'post_type'     => 'members'
        );

        // Insert the new member post
        $new_member_id = wp_insert_post($new_member_post);

        if (is_wp_error($new_member_id)) {
            error_log('Failed to create member post for user ID: ' . $user_id . '. Error: ' . $new_member_id->get_error_message());
        } else {
            error_log('Created member post ID: ' . $new_member_id . ' for user ID: ' . $user_id);

            // Insert user meta into the member post as post meta
            update_post_meta($new_member_id, 'user_id', $user_id);
            update_post_meta($new_member_id, 'username', $username);
            update_post_meta($new_member_id, 'address_country', $address_country);
            update_post_meta($new_member_id, 'address_city', $address_city);
            update_post_meta($new_member_id, 'address_county', $address_county);

            // Store the ACF field value correctly as an array
            $acf_value = array('11');
            add_post_meta($new_member_id, 'publish_to', $acf_value, true);
            add_post_meta($new_member_id, '_publish_to', 'field_6374a3364bb73', true);

            error_log('Inserted meta data and ACF field for member post ID: ' . $new_member_id);

            // Set the migration flag to true
            $migration_done = true;
            $migration_count++;  // Increment the successful migration counter

            // Address conversion section
            $existing_location = get_post_meta($new_member_id, 'location_members', true);

            if (empty($existing_location) && $address_country && $address_city && $address_county) {
                // Prepare the query URL, making sure that the variables are strings
                $query_url = "https://nominatim.openstreetmap.org/search.php?county=" . urlencode($address_county) . "&country=" . urlencode($address_country) . "&format=jsonv2";
                error_log('Query URL: ' . $query_url);

                $response = wp_remote_get($query_url);
                if (!is_wp_error($response)) {
                    $body = wp_remote_retrieve_body($response);
                    $data = json_decode($body, true);

                    if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                        $lat = floatval($data[0]['lat']);
                        $lng = floatval($data[0]['lon']);

                        $acf_value = array(
                            'zoom' => 14,
                            'lat' => $lat,
                            'lng' => $lng,
                        );

                        // Store the location data in the post meta
                        add_post_meta($new_member_id, 'location_members', $acf_value, true);
                        add_post_meta($new_member_id, '_location_members', 'field_65fcef62959d4', true);

                        error_log('Converted and saved location for member post ID: ' . $new_member_id);
                        $conversion_done = true;
                    }
                }
            }
        }
    }

    if ($migration_done) {
        echo '<div class="notice notice-success is-dismissible"><p>' . $migration_count . ' users migrated successfully.</p></div>';
    } else {
        echo '<div class="notice notice-info is-dismissible"><p>All users are already migrated. No new migrations were made.</p></div>';
    }

    if ($conversion_done) {
        echo '<div class="notice notice-success is-dismissible"><p>Address conversion to location completed successfully.</p></div>';
    } else {
        echo '<div class="notice notice-info is-dismissible"><p>All addresses are already converted. No new conversions were made.</p></div>';
    }
}






function delete_migrated_members()
{
    global $wpdb;

    // Get all member posts
    $member_posts = $wpdb->get_results("
        SELECT ID 
        FROM {$wpdb->posts} 
        WHERE post_type = 'members'
    ");

    if (empty($member_posts)) {
        echo '<div class="notice notice-error is-dismissible"><p>No migrated members found to delete.</p></div>';
        return;
    }

    foreach ($member_posts as $post) {
        $post_id = $post->ID;

        delete_post_meta($post_id, 'user_id');
        delete_post_meta($post_id, 'username');
        delete_post_meta($post_id, 'address_country');
        delete_post_meta($post_id, 'address_city');
        delete_post_meta($post_id, 'address_county');

        wp_delete_post($post_id, true);
        error_log('Deleted member post ID: ' . $post_id . ' and associated post meta.');
    }

    echo '<div class="notice notice-success is-dismissible"><p>All migrated members and their associated post meta have been deleted successfully.</p></div>';
}

function update_migrated_user_locations()
{
    global $wpdb;

    // Verify nonce
    if (!isset($_POST['user_migration_nonce']) || !wp_verify_nonce($_POST['user_migration_nonce'], 'user_migration_action')) {
        error_log('Nonce verification failed.');
        return;
    }

    // Query the database directly for users who match the criteria in wp_usermeta
    $users = $wpdb->get_results("
        SELECT u.ID, u.user_login 
        FROM {$wpdb->users} u
        INNER JOIN {$wpdb->usermeta} um ON u.ID = um.user_id
        WHERE um.meta_key = 'primary_blog' AND um.meta_value = '11'
    ");

    if (empty($users)) {
        echo '<div class="notice notice-error is-dismissible"><p>No users found to update.</p></div>';
        return;
    }

    $update_done = false;  // Track if any updates were made
    $update_count = 0;  // Counter for updates

    foreach ($users as $user) {
        $user_id = $user->ID;
        $username = $user->user_login;

        // Check if a member post exists for this username
        $existing_member_id = $wpdb->get_var($wpdb->prepare("
            SELECT ID 
            FROM {$wpdb->posts} 
            WHERE post_type = 'members' 
            AND post_title = %s
        ", $username));

        if ($existing_member_id) {
            // Fetch user's current address metadata
            $user_address_country = get_user_meta($user_id, 'address_country', true);
            $user_address_city = get_user_meta($user_id, 'address_city', true);
            $user_address_county = get_user_meta($user_id, 'address_county', true);

            // Ensure the address fields are strings, not arrays
            $user_address_country = is_array($user_address_country) ? implode(', ', $user_address_country) : $user_address_country;
            $user_address_city = is_array($user_address_city) ? implode(', ', $user_address_city) : $user_address_city;
            $user_address_county = is_array($user_address_county) ? implode(', ', $user_address_county) : $user_address_county;

            // Fetch the member's stored address metadata
            $member_address_country = get_post_meta($existing_member_id, 'address_country', true);
            $member_address_city = get_post_meta($existing_member_id, 'address_city', true);
            $member_address_county = get_post_meta($existing_member_id, 'address_county', true);

            // Check if the user's address is different from the member's address
            if ($user_address_country !== $member_address_country || $user_address_city !== $member_address_city || $user_address_county !== $member_address_county) {
                error_log('User address changed for user ID: ' . $user_id);

                // Update the member's address metadata
                update_post_meta($existing_member_id, 'address_country', $user_address_country);
                update_post_meta($existing_member_id, 'address_city', $user_address_city);
                update_post_meta($existing_member_id, 'address_county', $user_address_county);

                // Update the member's location based on the new address
                if ($user_address_country && $user_address_city && $user_address_county) {
                    $query_url = "https://nominatim.openstreetmap.org/search.php?county=" . urlencode($user_address_county) . "&country=" . urlencode($user_address_country) . "&format=jsonv2";
                    $response = wp_remote_get($query_url);

                    if (!is_wp_error($response)) {
                        $body = wp_remote_retrieve_body($response);
                        $data = json_decode($body, true);

                        if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                            $lat = floatval($data[0]['lat']);
                            $lng = floatval($data[0]['lon']);

                            $acf_value = array(
                                'zoom' => 14,
                                'lat' => $lat,
                                'lng' => $lng,
                            );

                            // Store the updated location data in the post meta
                            update_post_meta($existing_member_id, 'location_members', $acf_value);
                            update_post_meta($existing_member_id, '_location_members', 'field_65fcef62959d4', true);

                            error_log('Updated location for member post ID: ' . $existing_member_id);
                            $update_done = true;
                            $update_count++;
                        }
                    }
                }
            } else {
                error_log('No address change for user ID: ' . $user_id);
            }
        }
    }

    if ($update_done) {
        echo '<div class="notice notice-success is-dismissible"><p>' . $update_count . ' users updated successfully.</p></div>';
    } else {
        echo '<div class="notice notice-info is-dismissible"><p>No updates were necessary. All users have the correct address and location.</p></div>';
    }
}
function create_theme_forum_relationship_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'theme_forum_relationship';

    // Define the charset and collation for the table
    $charset_collate = $wpdb->get_charset_collate();

    // SQL statement for creating the table
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        theme_id bigint(20) unsigned NOT NULL,
        forum_id bigint(20) unsigned NOT NULL,
        PRIMARY KEY (id),
        KEY theme_id (theme_id),
        KEY forum_id (forum_id)
    ) $charset_collate;";

    // Use dbDelta to create the table
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function insert_theme_forum_relationship() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'theme_forum_relationship';

    // Create the table if it doesn't exist
    create_theme_forum_relationship_table();

    // Retrieve all published themes
    $themes = get_posts(array(
        'post_type' => 'theme',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));
    error_log('Themes: ' . count($themes)); // Logging the count of themes

    foreach ($themes as $theme) {
        // Retrieve all forums related to the current theme by its ID
        $forums = get_posts(array(
            'post_type' => 'forums',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'relevant_main_theme_network',
                    'value' => $theme->ID,
                    'compare' => '='
                )
            )
        ));

        // Log for debugging
        error_log('Processing Theme ID: ' . $theme->ID . ', Found ' . count($forums) . ' forums');

        // Insert each forum found into the database table
        foreach ($forums as $forum) {
            $wpdb->insert(
                $table_name,
                array(
                    'theme_id' => $theme->ID,
                    'forum_id' => $forum->ID
                ),
                array(
                    '%d',
                    '%d'
                )
            );
            // Logging each insert for debugging
            error_log('Inserted relationship for Theme ID: ' . $theme->ID . ' and Forum ID: ' . $forum->ID);
        }
    }
}
// Add a page under the "Tools" menu
function add_theme_forum_relationship_page() {
    add_management_page(
        'Theme Forum Relationship', // Page title
        'Theme Forum Relationship', // Menu title
        'manage_options',           // Capability required
        'theme-forum-relationship', // Menu slug
        'render_theme_forum_relationship_page' // Callback function
    );
}
add_action('admin_menu', 'add_theme_forum_relationship_page');

function render_theme_forum_relationship_page() {
    ?>
    <div class="wrap">
        <h1>Insert Theme-Forum Relationship</h1>
        <p>Click the button below to insert the theme-forum relationships into the database.</p>
        <button id="insert-theme-forum-button" class="button button-primary">Insert Relationships</button>
        <div id="insert-theme-forum-result"></div>

        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('#insert-theme-forum-button').on('click', function() {
                    $('#insert-theme-forum-result').html('<p>Processing...</p>');

                    $.post(ajaxurl, {
                        action: 'insert_theme_forum_relationship'
                    }, function(response) {
                        $('#insert-theme-forum-result').html('<p>' + response.data + '</p>');
                    });
                });
            });
        </script>
    </div>
    <?php
}

// Register the AJAX action for logged-in users
function handle_ajax_insert_theme_forum_relationship() {
    insert_theme_forum_relationship();
    wp_send_json_success('Theme-forum relationships have been inserted successfully.');
}
add_action('wp_ajax_insert_theme_forum_relationship', 'handle_ajax_insert_theme_forum_relationship');

