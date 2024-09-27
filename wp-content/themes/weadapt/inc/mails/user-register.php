<?php

/**
 * Mail Notification on Register User
 *
 * For debuging use: error_log( print_r( $variable, true ) );
 */
add_action('custom_user_meta_saved', 'theme_user_register_after_meta', 10, 2);

function theme_user_register_after_meta($user_id, $userdata) {
    $users = array_merge(get_blog_administrators(false, 1), get_blog_editors());
    include_once ABSPATH . 'wp-content/plugins/front-end-pm-pro/freemius/templates/account/billing.php';

    function get_country_name_from_code($country_code) {
    global $countries;
    
    return isset($countries[$country_code]) ? $countries[$country_code] : $country_code;
    }
    // Fetch additional user meta fields
    $user_organisation_id = get_user_meta($user_id, 'organisation', true);
    $user_country_code = get_user_meta($user_id, 'address_country', true);
    $user_city = get_user_meta($user_id, 'address_city', true);
    $user_country_name = get_country_name_from_code($user_country_code);
    $user_organisation = !empty($user_organisation_id) ? get_the_title($user_organisation_id) : '';
    error_log('User Organisation: ' . print_r($user_organisation, true));
    error_log('User Country: ' . print_r($user_country_name, true));
    error_log('User City: ' . print_r($user_city, true));

    if (!empty($users)) {
        $subject = sprintf(__('A new user account has been created on [%s]', 'weadapt'), get_bloginfo('name'));

        $message = sprintf(__('A new user account has been created: %s %s (%s) on [%s]', 'weadapt'),
            esc_html($userdata['first_name']),
            esc_html($userdata['last_name']),
            esc_html($userdata['user_login']),
            get_bloginfo('name')
        ) . '<br><br>';

        if (!empty($user_organisation)) {
            $message .= sprintf(__('Organisation: %s', 'weadapt'), esc_html($user_organisation)) . '<br>';
        }

        if (!empty($user_city)) {
            $message .= __('City:', 'weadapt') . ' ' . esc_html($user_city) . '<br>';
        }
        
        if (!empty($user_country_name)) {
            $message .= __('Country:', 'weadapt') . ' ' . esc_html($user_country_name) . '<br><br>';
        }

        $message .= sprintf('<a href="%s">%s</a><br>',
            get_author_posts_url($user_id),
            __('View user profile', 'weadapt')
        );

        $message .= sprintf(' <a href="%s">%s</a>',
            add_query_arg('user_id', $user_id, self_admin_url('user-edit.php')),
            __('Edit/delete user', 'weadapt')
        );

        theme_mail_save_to_db($users, $subject, $message);
        send_email_immediately($users, $subject, $message, null);
    }
}