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
                send_email_immediately($users, $subject, $message);
                set_transient($transient_key, true, HOUR_IN_SECONDS);
            }
        }
    }

    if (is_mailed_post_type($post->post_type) && 'pending' === $post->post_status && (!$update || 'pending' !== $previous_status)) {

        if ('organisation' === $post->post_type) {
            return;
        }
        if (!get_transient($transient_key)) {
            $current_user = wp_get_current_user();
            $current_blog = get_current_blog_id();
            if ($current_blog === 1) {
                $users = array_merge(get_blog_administrators(false, 1), get_blog_editors());
            } else {
                $users = get_blog_administrators(false, 1);
            }

            $users = array_unique($users);

            if (!empty($users)) {
                $subject = sprintf(__('Content has been submitted for review on [%s]', 'weadapt'), get_bloginfo('name'));

                $message = sprintf(
                    __('%s %s has sent you content for review.', 'weadapt'),
                    esc_attr($current_user->user_firstname),
                    esc_attr($current_user->user_lastname)
                ) . '<br><br>';
                

                $message .= sprintf(__('Content: %s', 'weadapt'), esc_html($post->post_title))  ;
                $message .= sprintf(__('Summary: %s', 'weadapt'), esc_html($post->post_excerpt)) ;
                $message .= sprintf('<a href="%s">%s</a>', get_permalink($post_ID), __('Visit the content', 'weadapt'));
                $message .= "<br>Best Regards,<br>WeAdapt";
                $draft_tags = wp_get_post_terms($post_ID, 'tags', ['hide_empty' => false]);

                if (!empty($draft_tags)) {
                    $message_tags = [];

                    foreach ($draft_tags as $term) {
                        if (false === get_field('status', $term)) {
                            $message_tags[] = sprintf(
                                __('<a href="%s">%s</a> (ID %s)', 'weadapt'),
                                add_query_arg(array(
                                    'taxonomy' => $term->taxonomy,
                                    'tag_ID' => $term->term_id,
                                ), admin_url('term.php')),
                                esc_html($term->name),
                                intval($term->term_id)
                            );
                        }
                    }

                    if (!empty($message_tags)) {
                        $message .= '<br><br>' . __('Draft Tags:', 'weadapt') . '<br>' . implode('<br>', $message_tags);
                    }
                }

                theme_mail_save_to_db($users, $subject, $message);
                send_email_immediately($users, $subject, $message);

                set_transient($transient_key, true, HOUR_IN_SECONDS);
            }
        }
    }

    update_post_meta($post_ID, '_previous_status', $post->post_status);
}
add_action('save_post', 'theme_save_post', 50, 3);
function send_email_immediately($user_ids, $subject, $message) {
    if (!is_array($user_ids)) {
        $user_ids = (array) $user_ids;  
    }
    $image_url = get_theme_file_uri('/assets/images/email.png');

    // Define button style
    $button_style = 'style="background-color: #002D75; border: 1px solid #002D75; color: #FFFFFB; text-decoration: none; display: inline-block; font-size: 16px; padding: 10px 20px; border-radius: 5px;"';

    // Collect buttons in an array
    $buttons = [];

    // Replace <a> tags with styled buttons and collect them
    $message = preg_replace_callback('/<a\s+href=["\']([^"\']+)["\']>(.*?)<\/a>/i', function ($matches) use ($button_style, &$buttons) {
        $url = esc_url($matches[1]);
        $text = esc_html($matches[2]);
        $buttons[] = '<a href="' . $url . '" ' . $button_style . '>' . $text . '</a>';
        return ''; // Remove the original link
    }, $message);

    // Wrap all buttons in a flex container
    $button_container = '<div style="display: flex; gap: 10px; margin-top: 30px; margin-bottom:10px;">' . implode('', $buttons) . '</div>';

    // Insert the buttons before "Best Regards,"
    $message = preg_replace('/(Best Regards,)/i', $button_container . '<br>$1', $message);

    // HTML template for the email with placeholders
    $html_template = '<!doctype html>
<html lang="en">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style media="all" type="text/css">
@media all {
  .btn-primary table td:hover {
    background-color: #ec0867 !important;
  }
}
@media only screen and (max-width: 640px) {
  .main p,
.main td,
.main span {
    font-size: 16px !important;
  }
  .wrapper {
    padding: 8px !important;
  }
  .content {
    padding: 0 !important;
  }
  .container {
    padding: 0 !important;
    padding-top: 8px !important;
    width: 100% !important;
  }
  .main {
    border-left-width: 0 !important;
    border-radius: 0 !important;
    border-right-width: 0 !important;
  }
  .btn table {
    max-width: 100% !important;
    width: 100% !important;
  }
  .btn a {
    font-size: 16px !important;
    max-width: 100% !important;
    width: 100% !important;
  }
}
@media all {
  .ExternalClass {
    width: 100%;
  }
  .ExternalClass,
.ExternalClass p,
.ExternalClass span,
.ExternalClass font,
.ExternalClass td,
.ExternalClass div {
    line-height: 100%;
  }
  .apple-link a {
    color: inherit !important;
    font-family: inherit !important;
    font-size: inherit !important;
    font-weight: inherit !important;
    line-height: inherit !important;
    text-decoration: none !important;
  }
  #MessageViewBody a {
    color: inherit;
    text-decoration: none;
    font-size: inherit;
    font-family: inherit;
    font-weight: inherit;
    line-height: inherit;
  }
}
    </style>
  </head>
<body style="font-family: Inter, sans-serif!important; -webkit-font-smoothing: antialiased; font-size: 16px; line-height: 1.3; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background-color: #f4f5f6; margin: 0; padding: 0;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f4f5f6; width: 100%;" width="100%" bgcolor="#f4f5f6">
      <tr>
        <td style="font-family: Inter, sans-serif; font-size: 16px; vertical-align: top;" valign="top">&nbsp;</td>
        <td class="container" style="font-family: Inter, sans-serif; font-size: 16px; vertical-align: top; max-width: 600px; padding: 0; padding-top: 24px; width: 600px; margin: 0 auto;" width="600" valign="top">
          <div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 600px; padding: 0;">
            <!-- START CENTERED WHITE CONTAINER -->
            <span class="preheader" style="color: transparent; display: none; height: 0; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all; visibility: hidden; width: 0;"> ' . $message . '</span>
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #ffffff; border: 1px solid #eaebed; border-radius: 16px; width: 100%;" width="100%">
              <!-- START MAIN CONTENT AREA -->
              <tr>
                <td class="wrapper" style="font-family: Inter, sans-serif; font-size: 16px; vertical-align: top; box-sizing: border-box; padding: 24px;" valign="top">
                  <!-- Add Image Dynamically from Theme -->
                  <div style="text-align: center; margin-bottom: 24px;"> <!-- Centering the image -->
                      <img class="first-grand-arrow"
                           src="' . esc_url($image_url) . '" 
                           alt="Email Image" 
                           style="max-width: 40%; height: auto; display: block; margin: 0 auto; margin-bottom: 12px;" /> <!-- Added margin-bottom to the image -->
                      <div style="background-color: #141E1B; height: 2px; width: 100%; margin-bottom: 12px; border-radius:5px;"></div> <!-- Centered border -->
                  </div>
                  <p style="font-family: Inter, sans-serif; font-size: 16px; font-weight: normal; margin: 0; margin-bottom: 16px;">Dear [USER_NAME],</p>
                  <p style="font-family: Inter, sans-serif; font-size: 16px; font-weight: normal; margin: 0; margin-bottom: 16px;">' . $message . '</p>
                   <!-- Insert additional signature or footer here -->
                </td>
              </tr>
              <!-- END MAIN CONTENT AREA -->
              </table>
            <!-- START FOOTER -->
            <!-- Footer content here -->
            <!-- END FOOTER -->
          </div>
        </td>
        <td style="font-family: Inter, sans-serif; font-size: 16px; vertical-align: top;" valign="top">&nbsp;</td>
      </tr>
    </table>
  </body>
</html>
';

    // Email headers
    $headers = array('Content-Type: text/html; charset=UTF-8');

    foreach ($user_ids as $user_id) {
        $user_info = get_userdata($user_id);
        error_log("Sending email to $user_id");
        if ($user_info) {
            $recipient = $user_info->user_email;
            $user_name = $user_info->display_name;

            // Replace placeholder with actual user name
            $personalized_template = str_replace('[USER_NAME]', esc_html($user_name), $html_template);

            // Send email with the HTML template
            $sent = wp_mail($recipient, $subject, $personalized_template, $headers);

            if (!$sent) {
                error_log("Failed to send email to $recipient with subject $subject.");
            } else {
                error_log("Email sent to $recipient with subject $subject.");
            }
        } else {
            error_log("User with ID $user_id not found.");
        }
    }
}









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
        send_email_immediately($users, $subject, $message);
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
