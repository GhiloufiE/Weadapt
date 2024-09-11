<?php
function send_email_immediately($user_ids, $subject, $message) {
    $user_ids = (array) $user_ids;
    $image_url = get_theme_file_uri('/assets/images/email.png');
    $button_style = generate_button_style();
    $buttons = extract_buttons_from_message($message, $button_style);
    $button_container = generate_button_container($buttons);
    $message = insert_buttons_into_message($message, $button_container);
    $html_template = generate_email_template($image_url, $message);

    $headers = array('Content-Type: text/html; charset=UTF-8');
    send_emails_to_users($user_ids, $subject, $html_template, $headers);
}

function generate_button_style() {
    return 'style="background-color: #002D75; border: 1px solid #002D75; color: #FFFFFB; text-decoration: none; display: inline-block; font-size: 16px; padding: 10px 20px; border-radius: 5px; margin-left: 1rem;"';
}

function extract_buttons_from_message(&$message, $button_style) {
    $buttons = [];
    $message = preg_replace_callback('/<a\s+href=["\']([^"\']+)["\']>(.*?)<\/a>/i', function ($matches) use ($button_style, &$buttons) {
        $url = esc_url($matches[1]);
        $text = esc_html($matches[2]);
        $buttons[] = '<a href="' . $url . '" ' . $button_style . '>' . $text . '</a>';
        return ''; 
    }, $message);
    return $buttons;
}

function generate_button_container($buttons) {
    return '<div style="display: flex; gap: 10px; margin-top: 30px; margin-bottom:10px;">' . implode('', $buttons) . '</div>';
}

function insert_buttons_into_message($message, $button_container) {
    return preg_replace('/(Best Regards,)/i', $button_container . '<br>$1', $message);
}

function generate_email_template($image_url, $message) {
    return '<!doctype html>
<html lang="en">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style media="all" type="text/css">
    </style>
  </head>
<body style="font-family: Inter, sans-serif; background-color: #f4f5f6; margin: 0; padding: 0;">
    <table role="presentation" class="body" style="width: 100%; background-color: #f4f5f6;">
      <tr>
        <td></td>
        <td class="container" style="max-width: 600px; margin: 0 auto; padding-top: 24px;">
          <div class="content" style="box-sizing: border-box; padding: 0;">
            <span class="preheader" style="display: none;">' . $message . '</span>
            <table class="main" style="background: #ffffff; border: 1px solid #eaebed; border-radius: 16px; width: 100%;">
              <tr>
                <td class="wrapper" style="padding: 24px;">
                  <div style="text-align: center; margin-bottom: 24px;">
                      <img src="' . esc_url($image_url) . '" alt="Email Image" style="max-width: 40%; margin-bottom: 12px;" />
                      <div style="background-color: #141E1B; height: 2px; margin-bottom: 12px;"></div>
                  </div>
                  <p>Dear [USER_NAME],</p>
                  <p>' . $message . '</p>
                </td>
              </tr>
            </table>
          </div>
        </td>
        <td></td>
      </tr>
    </table>
  </body>
</html>';
}

function send_emails_to_users($user_ids, $subject, $html_template, $headers) {
    foreach ($user_ids as $user_id) {
        $user_info = get_userdata($user_id);
        if ($user_info) {
            $recipient = $user_info->user_email;
            $user_name = $user_info->display_name;
            $personalized_template = str_replace('[USER_NAME]', esc_html($user_name), $html_template);
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