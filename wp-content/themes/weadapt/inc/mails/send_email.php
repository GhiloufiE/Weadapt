<?php
function send_email_immediately($user_ids, $subject, $message, $post_id) {
    $user_ids = (array) $user_ids;
    
    $publish_to = get_post_meta($post_id, 'publish_to', true);

    if (is_array($publish_to)) {
        $blog_image_map = [
            'weADAPT' => get_theme_file_uri('/assets/images/weadapt.webp'),
            'Adaptation At Altitude' => get_theme_file_uri('/assets/images/adaptation-alt.webp'),
            'Can-Adapt' => get_theme_file_uri('/assets/images/can-adapt.webp'),
            'Adaptation Without Borders' => get_theme_file_uri('/assets/images/adaptation-without-borders.webp'),
            'Water Adaptation Community' => get_theme_file_uri('/assets/images/weadapt.webp'),
            'MAIA' => get_theme_file_uri('/assets/images/maia.webp'),
            'Agora' => get_theme_file_uri('/assets/images/agora.webp'),
        ];

        $image_urls = [];
        $blog_names = [];

        foreach ($publish_to as $blog_id) {
            $blog_name = get_blog_details($blog_id)->blogname;
            $blog_names[] = $blog_name;

            if (isset($blog_image_map[$blog_name])) {
                $image_urls[] = $blog_image_map[$blog_name];
            } else {
                $image_urls[] = get_theme_file_uri('/assets/images/weadapt.png');
            }
        }
    } else {
        $image_urls[] = get_theme_file_uri('/assets/images/weadapt.png');
    }

    $button_style = generate_button_style();
    $buttons = extract_buttons_from_message($message, $button_style);
    $button_container = generate_button_container($buttons);
    $message = insert_buttons_into_message($message, $button_container);
    $html_template = generate_email_template($image_urls, $message);

    $headers = array('Content-Type: text/html; charset=UTF-8');
    send_emails_to_users($user_ids, $subject, $html_template, $headers);
}

function generate_button_style() {
    return 'style="background-color: #002D75; border: 1px solid #002D75; color: #FFFFFB; text-decoration: none; display: inline-block; font-size: 16px; padding: 10px 20px; border-radius: 5px; margin-right:1rem;"';
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

function generate_email_template($image_urls, $message) {
  // Ensure the images are displayed at a max-width of 40% and do not stretch
  $image_size_style = '
     max-width: 30%;  
 ';
    
  // Create a container for the images that expands horizontally
  $image_html = '<div class="image-wrapper" style="text-align: center; display: flex; flex-wrap:wrap;  justify-content: space-around; width: 100%; max-width: 100%;">';
  
  foreach ($image_urls as $image_url) {
      $image_html .= '<img src="' . esc_url($image_url) . '" alt="Blog Image" style="' . $image_size_style . '" />';
  }
  
  $image_html .= '</div>';

  return '<!doctype html>
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
        max-width: 640px; /* Ensure width expands to fit the content */
        overflow-x: hidden; /* Prevent horizontal scrolling */
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
    .image-wrapper {
        display: block !important; /* Ensure images stack on mobile */
        width: 100% !important;
        text-align: center !important;
    }
    .image-wrapper img {
        max-width: 50% !important; /* Reduce image size on mobile */
        width: 100% !important;
        margin-bottom: 8px !important;
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
    .wrapper {
      width: 100%; /* Make the wrapper as wide as the image wrapper */
      max-width: 100%; 
      padding: 20px; /* Remove extra padding to ensure full-width */
       
    }
  }
</style>
</head>
<body style="font-family: Inter, sans-serif; background-color: #f4f5f6; margin: 0; padding: 0; width: 100%;">
<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body" style="width: 100%; background-color: #f4f5f6;">
  <tr>
    <td style="font-family: Inter, sans-serif; font-size: 16px;">&nbsp;</td>
    <td class="container" style="width: auto; margin: 0 auto; padding: 0; padding-top: 24px; display:flex; max-width: 640px;">
      <div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; width: auto; padding: 0;">
        <span class="preheader" style="display: none;">' . $message . '</span>
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="main" style="background: #ffffff; border: 1px solid #eaebed; border-radius: 16px; width: 100%; max-width: 640px;">
          <tr>
            <td class="wrapper">
              <div style="text-align: center; margin-bottom: 24px;">'
                . $image_html . 
                '<div style="background-color: #141E1B; height: 2px; margin-bottom: 12px; width: 100%; border-radius:5px;"></div>
              </div>
              <p style="font-family: Inter, sans-serif; font-size: 16px;">Dear [USER_NAME],</p>
              <p style="font-family: Inter, sans-serif; font-size: 16px;">' . $message . '</p>
            </td>
          </tr>
        </table>
      </div>
    </td>
    <td style="font-family: Inter, sans-serif; font-size: 16px;">&nbsp;</td>
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
