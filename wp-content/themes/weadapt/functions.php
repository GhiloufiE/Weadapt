<?php

/**
 * Init theme base scripts
 */
require_once ( get_theme_file_path( '/base/init.php' ) );


/**
 * Include All Inc Files
 */
foreach ( get_glob_folders_path( '/inc/*/*.php' ) as $file_path ) {
	require_once( get_theme_file_path( $file_path ) );
}

/**
 * Register Gutenberg Blocks
 */
if ( ! function_exists( 'register_acf_blocks' ) ) :

	function register_acf_blocks() {
		foreach ( get_glob_folders_path( '/parts/gutenberg/*/register.php' ) as $file_path ) {
			require_once( get_theme_file_path( $file_path ) );
		}
	}

endif;

if ( function_exists( 'acf_register_block_type' ) ) {
	add_action( 'acf/init', 'register_acf_blocks' );
}

// Hook to run the function on post update
add_action('acf/save_post', 'compare_and_display_acf_group_field_values', 20);

function compare_and_display_acf_group_field_values($post_id) {
    // Check if it's an autosave or a real save
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    // Check if the post type is the one you want to target
  	if (('network' === get_post_type($post_id)) || ('theme' === get_post_type($post_id))) {
         // Get the ACF group field values before the update
		$people_values = get_field('people', $post_id);
		$editors_values = $people_values['editors'];
		if(is_array($editors_values)) {
			foreach($editors_values as $editor) {
				$user_meta = get_userdata($editor);
				$user_roles = $user_meta->roles;
				if (!in_array('author', $user_roles)) {
					$user_meta->add_role('author');
				}
		   }
		}
		$people_values = get_field('people', $post_id);
            $contributors_values = $people_values['contributors'];

            if(is_array($contributors_values)) {
                foreach($contributors_values as $contributor) {
                    $user_data = get_userdata($contributor);

                    $user_data->add_role('contributor');

               }
            }
    }


}

function grant_edit_user_roles_to_admins() {
    $admin_role = get_role('administrator');
    $admin_role->add_cap('manage_network_users');
}
add_action('init', 'grant_edit_user_roles_to_admins');



function toast_resizable_sidebar(){ ?>
  <style>
      .components-form-token-field__suggestions-list {
        max-height: 250px;}
  </style>
  
<?php }
add_action('admin_head','toast_resizable_sidebar');




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

function notify_admin_on_pending_comment($comment_id, $comment_approved) {
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

/* function my_custom_comment_status_transition($new_status, $old_status, $comment) {
    if ($old_status === 'unapproved' && $new_status === 'approved') {
        $comment_id = $comment->comment_ID;
        process_comment_notification($comment_id);
    }
}
function check_comment_post_type_on_post($comment_id, $approved, $commentdata) {
    if ($approved === 1) { 
        process_comment_notification($comment_id);
    }
}
add_action('transition_comment_status', 'my_custom_comment_status_transition', 10, 3);
add_action('comment_post', 'check_comment_post_type_on_post', 10, 3);
add_action('comment_post', 'notify_admin_on_pending_comment', 10, 2);


function forum_new_post_notification($post_id) {
    global $wpdb;
    
    $notification_sent = get_post_meta($post_id, 'forum_notification_sent', true);
    
    if ($notification_sent) {
        return;
    }
    
    if ( !wp_is_post_revision( $post_id ) ) {
        $query = $wpdb->prepare("SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE post_id = %d AND meta_key = 'forum'", $post_id);
        $results = $wpdb->get_results($query);

        if (!empty($results)) {
            $meta_value = $results[0]->meta_value;
            $post_title = get_the_title($post_id);
            $post_excerpt = get_the_excerpt($post_id);
            $post_link = get_permalink($post_id);
            $site_name = get_bloginfo('name');
            $table_name = $wpdb->prefix . 'wa_join';
            $query = $wpdb->prepare("SELECT user_id FROM $table_name WHERE join_id = %s", $meta_value);

            $results = $wpdb->get_results($query);

            $user_ids = array();
            foreach ($results as $result) {
                $user_ids[] = $result->user_id;
            }

            $user_emails = array();
            $current_user = array();
            if (!empty($user_ids)) {
                $user_ids_list = implode(',', $user_ids);
                $users_table_name = $wpdb->prefix . 'users';
                $query_user_emails = "SELECT ID, user_email, display_name FROM $users_table_name WHERE ID IN ($user_ids_list)";
                $user_emails_results = $wpdb->get_results($query_user_emails);
                
                foreach ($user_emails_results as $user) {
                    $recipient_email = $user->user_email;
                    $display_name = $user->display_name;
                    
                    
                    $subject = 'New Post on a Forum Topic You Follow';
                    $message = "Hi $display_name, </br></br></br>";
                    $message = "
                    <!DOCTYPE html>
                    <html>
                    <head>
                    </head>
                    <body>
                        <div style=' margin: 0 auto;'>
                            <h3 style='font-size: 22px; font-weight: bold;'>We wanted to inform you of a recent update in the forum discussions you've been following.</h3>
                            <p><strong>Title:</strong> $post_title</p>
                            <p><strong>Content:</strong> $post_excerpt</p>
                            <p>Feel free to explore the post and join the discussion with other members by clicking the link below:</p>
                            <p><a style='display: inline-block; padding: 10px 0px;  text-decoration: none; ' href='$post_link'>$post_title</a></p>
                            <p>Thank you for your continued participation and contributions to our community.</p>
                            <p>Best Regards,<br>$site_name</p>
                        </div>
                    </body>
                    </html>
                    ";

                    
                    $headers = array(
                        'Content-Type: text/html; charset=UTF-8',
                        'Bcc: ' . $recipient_email, 
                    );
                    
                    wp_mail($recipient_email, $subject, $message, $headers);
                }
            }
        }
        
        update_post_meta($post_id, 'forum_notification_sent', true);
    }
}

add_action('acf/save_post', 'forum_new_post_notification', 10, 1); */
function replace_howdy( $wp_admin_bar ) {
    $my_account = $wp_admin_bar->get_node( 'my-account' );
    $greeting = str_replace( 'Howdy,', 'Hello,', $my_account->title );
    $wp_admin_bar->add_node( array(
    'id' => 'my-account',
    'title' => $greeting,
    ) );
    }
    add_filter( 'admin_bar_menu', 'replace_howdy', 25 );


    // Enqueue the custom JavaScript file
function enqueue_custom_scripts() {
    wp_enqueue_script('custom-script', get_template_directory_uri() . '/js/custom-script.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');

// Add JavaScript code to add low-quality image warning boxes
function add_low_quality_image_warning_script() {
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
    
    $send_notification = get_field('send_notification_to_members', $post_id);
    if (!$send_notification) {
        return;
    }

    if (get_post_meta($post_id, 'forum_notification_sent', true)) {
        return;
    }
    if (!wp_is_post_revision($post_id)) {
        $isForumTopic = get_post_meta($post_id, 'forum', true);
        if ($isForumTopic) {
            $forum_post_id = (int) $isForumTopic;
        } else {
            return;
        }
        $meta_value = get_post_meta($forum_post_id, 'relevant_main_theme_network', true);
        if ($meta_value) {
            $theme_name = get_the_title($forum_post_id);

            $table_name = $wpdb->prefix . 'wa_join';
            $user_ids = $wpdb->get_col($wpdb->prepare("SELECT user_id FROM $table_name WHERE join_id = %s", $meta_value));

            if (!empty($user_ids)) {
                $users_table_name = $wpdb->prefix . 'users';
                $user_ids_placeholder = implode(',', array_fill(0, count($user_ids), '%d'));
                $query_user_emails = $wpdb->prepare("SELECT ID, user_email, display_name FROM $users_table_name WHERE ID IN ($user_ids_placeholder)", $user_ids);
                $user_emails_results = $wpdb->get_results($query_user_emails);

                $post_title = get_the_title($post_id);
                $post_excerpt = get_the_excerpt($post_id);
                $post_excerpt = wp_strip_all_tags($post_excerpt);
                $post_excerpt = mb_strimwidth($post_excerpt, 0, 100, '...');
                $post_link = get_permalink($post_id);
                $site_name = get_bloginfo('name');
                $theme_name = get_the_title($forum_post_id);

                $post_type = get_post_type($post_id);

                $post_type_labels = array(
                    'forum' => 'Forum Topic'
                );

                $post_type_label = isset($post_type_labels[$post_type]) ? $post_type_labels[$post_type] : 'Post';
                $subject = "New conversation in the $theme_name theme on weADAPT";

                foreach ($user_emails_results as $user) {
                    $recipient_email = $user->user_email;
                    $display_name = $user->display_name;

                    $message = "Hi $display_name, <br><br>";
                    $message .= "We wanted to inform you of a new conversation in the <b>$theme_name</b> theme you are a member of.<br><br>
                    Title: $post_title <br>
                    Content: $post_excerpt <br><br>
                    Reply to the conversation and engage with other members here:<br>
                    <a href='$post_link'>$post_title</a><br><br>
                    Thank you for your continued participation and contributions to our community.<br><br>
                    Best Regards,<br>
                    $site_name,<br>";

                    $headers = array('Content-Type: text/html; charset=UTF-8');
                    wp_mail($recipient_email, $subject, $message, $headers);
                }
            }
        }
        update_post_meta($post_id, 'forum_notification_sent', true);
    }
}

add_action('acf/save_post', 'forum_new_post_notification', 10, 1);


function handle_create_post()
{
    global $wpdb;
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
        );

        if ($post_type == 'forum' && isset($_POST['forum'])) {
            $forum_id = intval($_POST['forum']);
            $forum_true_id = $wpdb->get_var($wpdb->prepare("SELECT forum_id FROM {$wpdb->prefix}theme_forum_relationship WHERE theme_id = %d", $forum_id));
            $post_data['meta_input'] = array('forum' => $forum_true_id);
            error_log('Forum ID: ' . $forum_true_id);
        }

        if ($post_type == 'theme' && isset($_POST['forum'])) {
            $forum_id = intval($_POST['forum']);
            $forum_true_id = $wpdb->get_var($wpdb->prepare("SELECT forum_id FROM {$wpdb->prefix}theme_forum_relationship WHERE theme_id = %d", $forum_id));
            $post_data['meta_input'] = array('relevant_main_theme_network' => $forum_true_id);
            error_log('Forum ID: ' . $forum_true_id);

        }

        $post_id = wp_insert_post($post_data);

        if (is_wp_error($post_id)) {
            wp_send_json_error('Error creating post: ' . $post_id->get_error_message());
        } else {
            if (in_array($post_type, ['forum', 'theme'])) {
                notify_admins_of_pending_posts($post_id, $post_type);
            }
            wp_send_json_success('Post created successfully');
        }
    } else {
        wp_send_json_error('Missing required POST fields');
    }
}

add_action('admin_post_nopriv_create_post', 'handle_create_post');
add_action('admin_post_create_post', 'handle_create_post');
function notify_admins_of_pending_posts($post_id, $post_type)
{
    $post_title = get_the_title($post_id);
    $post_link = get_edit_post_link($post_id);
    $site_name = get_bloginfo('name');

    if ($post_type == 'forum') {
        $forum_id = get_field('forum', $post_id);
        $forum_name = get_the_title($forum_id);
        $subject = "New forum topic pending review";
        $message = "A new forum topic titled <b>$post_title</b> in the forum <b>$forum_name</b> is pending review.<br><br>
        <a href='$post_link'>$post_title</a><br><br>
        Best Regards,<br>
        $site_name,<br>";
    } elseif ($post_type == 'theme') {
        $theme_id = get_field('relevant_main_theme_network', $post_id);
        $theme_name = get_the_title($theme_id);
        $subject = "New forum post pending review";
        $message = "A new Forum post titled <b>$post_title</b> in the theme <b>$theme_name</b> is pending review.<br><br>
        <a href='$post_link'>$post_title</a><br><br>
        Best Regards,<br>
        $site_name,<br>";
    } else {
        return;
    }

    $offset = 0;
    $limit = 100;
    $admins = [];
    $args = array(
        'role' => 'administrator',
        'orderby' => 'ID',
        'order' => 'ASC',
        'number' => $limit,
        'offset' => $offset,
    );

    do {
        $batch_admins = get_users($args);
        foreach ($batch_admins as $admin) {
            if (in_array('administrator', $admin->roles)) {
                $admins[] = $admin;
            }
        }
        $offset += $limit;
        $args['offset'] = $offset;
    } while (count($batch_admins) == $limit);

    $headers = array('Content-Type: text/html; charset=UTF-8');
    foreach ($admins as $admin) {
        $admin_email = $admin->user_email;
        //wp_mail($admin_email, $subject, $message, $headers);
    }
}
function notify_admin_on_edit( $new_status, $old_status, $post ) {
    // Ignore intermediate statuses
    if ( $new_status === 'auto-draft' || $new_status === 'inherit' ) {
        return;
    }

    // Only notify if the post is submitted for review and was not already in review status
    if ( ( $new_status === 'pending' || $new_status === 'draft' ) && ( $old_status === 'publish' || $old_status === 'pending' ) ) {
        // Check if the notification has already been sent
        if ( get_post_meta( $post->ID, '_notify_admin_on_edit_sent', true ) ) {
            error_log('Duplicate email prevented for post ID: ' . $post->ID);
            return;
        }

        error_log('Post status changed from publish or pending to pending or draft, preparing to send email.'); // Debugging

        $admin_email = get_option( 'admin_email' );
        $revisions = wp_get_post_revisions( $post->ID );
        $latest_revision = reset( $revisions );
        $changes = '';

        if ( $latest_revision ) {
            if ( $latest_revision->post_title !== $post->post_title ) {
                $changes .= sprintf( __( 'Title changed from "%s" to "%s"', 'your-text-domain' ), $latest_revision->post_title, $post->post_title ) . '<br>';
            }
            if ( $latest_revision->post_content !== $post->post_content ) {
                $changes .= __( 'Content changed', 'your-text-domain' ) . '<br>';
                $changes .= __( 'Old Content:', 'your-text-domain' ) . '<br>';
                $changes .= nl2br( esc_html( $latest_revision->post_content ) ) . '<br><br>';
                $changes .= __( 'New Content:', 'your-text-domain' ) . '<br>';
                $changes .= nl2br( esc_html( $post->post_content ) ) . '<br><br>';
            }
            if ( $latest_revision->post_excerpt !== $post->post_excerpt ) {
                $changes .= sprintf( __( 'Excerpt changed from "%s" to "%s"', 'your-text-domain' ), $latest_revision->post_excerpt, $post->post_excerpt ) . '<br>';
            }
        }

        $subject = sprintf( __( 'A post has been edited: %s', 'your-text-domain' ), get_the_title( $post->ID ) );
        $message = sprintf( __( 'The post "%s" has been edited by the user.', 'your-text-domain' ), get_the_title( $post->ID ) ) . '<br>';
        
        if ( empty( $changes ) ) {
            $changes = __( 'No significant changes detected', 'your-text-domain' );
        }
        
        $message .= __( 'Changes:', 'your-text-domain' ) . '<br>' . $changes;

        // Debugging
        error_log('Admin email: ' . $admin_email);
        error_log('Email subject: ' . $subject);
        error_log('Email message: ' . $message);

        // Send the email
        if ( wp_mail( $admin_email, $subject, $message ) ) {
            error_log('Email sent successfully.');
            // Set a post meta to avoid duplicate emails for this post ID
            update_post_meta( $post->ID, '_notify_admin_on_edit_sent', true );
        } else {
            error_log('Failed to send email.');
        }
    } else {
        error_log('Post status did not meet conditions for sending email.');
        error_log('Old status: ' . $old_status . ', New status: ' . $new_status);
    }
}
add_action( 'transition_post_status', 'notify_admin_on_edit', 10, 3 );

function reset_notify_admin_on_edit( $post_id ) {
    // Reset the notification meta when the post is updated
    delete_post_meta( $post_id, '_notify_admin_on_edit_sent' );
}
add_action( 'save_post', 'reset_notify_admin_on_edit' );

function create_forum_post_on_theme_creation($new_status, $old_status, $post) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'theme_forum_relationship';

    if ($post->post_type == 'theme' && $old_status == 'auto-draft' && $new_status == 'publish') {
        error_log("Creating forum post for theme ID: {$post->ID}");

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
        error_log("Forum post created with ID: {$forum_post_id}");
    } else {
        error_log("No action taken. Old status: {$old_status}, New status: {$new_status}");
    }
}

add_action('transition_post_status', 'create_forum_post_on_theme_creation', 10, 3);

/* function insert_theme_forum_relationship() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'theme_forum_relationship';

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

// Adding the function to the 'init' action hook
add_action('init', 'insert_theme_forum_relationship'); */