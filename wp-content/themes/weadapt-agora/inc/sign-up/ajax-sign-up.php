<?php


/**
 * Add Reset Password Attributes/Code
 */
function theme_popup_attributes()
{
	if (isset($_GET['action']) && $_GET['action'] === 'reset-password') {
		echo ' data-popup="reset-password"';

		add_action('popup-content', function () {
			echo get_part('components/popup/index', [
				'template' => 'reset-password',
				'is_active' => true
			]);
		});
	}
}


/**
 * Die Json Message
 */
function die_json_message($status = '', $message = '', $reload = false, $timeout = 0, $popup_trigger = '')
{
	$data = [
		'status' => $status,
		'message' => $message
	];

	if ($reload) {
		$data['reload'] = $reload;
	}

	if ($timeout) {
		$data['timeout'] = $timeout;
	}

	if ($popup_trigger) {
		$data['popupTrigger'] = $popup_trigger;
	}

	wp_die(json_encode($data));
}


/**
 * Auth User
 */
function theme_auth_user($user_login, $user_password, $redirect_url, $custom_message = '')
{
	if (is_email($user_login)) {
		$user_by_email = get_user_by('email', $user_login);

		if (!$user_by_email) {
			die_json_message('error', esc_html__('Email not recognised. Please try again.', 'weadapt'));
		}

		$user_name = $user_by_email->user_login;
	} else {
		$user_name = $user_login;
	}

	$user_signon = wp_signon([
		'user_login' => $user_name,
		'user_password' => $user_password,
		'remember' => true
	], is_ssl());

	if (is_wp_error($user_signon)) {
		die_json_message('error', esc_html__('Email or password not recognised. Please try again.', 'weadapt'));
	} else {
		if ($custom_message) {
			die_json_message('success', $custom_message, $redirect_url);
		} else {
			die_json_message('success', esc_html__('Log In successful, redirecting...', 'weadapt'), $redirect_url);
		}
	}

	die();
}


/**
 * Ajax Log In
 */
function theme_ajax_login()
{
	if (!wp_verify_nonce($_POST['ajax_login_nonce'], 'ajax-login-nonce')) {
		die_json_message('error', esc_html__('Sorry, the verification data does not match', 'weadapt'));
	} else {
		$user_name = sanitize_text_field(trim($_POST['user_name']));
		$user_pass = sanitize_text_field(trim($_POST['user_pass']));
		$redirect_url = esc_url(trim($_POST['redirect_to']));

		theme_auth_user($user_name, $user_pass, $redirect_url);
	}

	die();
}
add_action('wp_ajax_theme_ajax_login', 'theme_ajax_login');
add_action('wp_ajax_nopriv_theme_ajax_login', 'theme_ajax_login');


/**
 * Ajax Forgot
 */
function theme_ajax_forgot()
{
	require_once (ABSPATH . 'wp-includes/class-phpass.php');

	if (!wp_verify_nonce($_POST['ajax_forgot_nonce'], 'ajax-forgot-nonce')) {
		die_json_message('error', esc_html__('Sorry, the verification data does not match', 'weadapt'));
	} else {
		$user_login = sanitize_text_field(trim($_POST['user_name']));

		if (!$user_login) {
			die_json_message('error', esc_html__('Empty username or email address!', 'weadapt'));
		} else {
			if (is_email($user_login)) {
				$current_user = get_user_by('email', $user_login);
			} else {
				$current_user = get_user_by('login', $user_login);
			}

			if (empty($current_user->data)) {
				die_json_message('error', esc_html__('This user does not exist!', 'weadapt'));
			} else {
				// E-mail Message
				$key = get_password_reset_key($current_user);
				$site_name = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

				$message = __('Someone has requested a password reset for the following account:', 'weadapt') . "<br><br>";
				$message .= sprintf(__('Site Name: %s', 'weadapt'), $site_name) . "<br>";
				$message .= sprintf(__('Username: %s', 'weadapt'), $current_user->user_login) . "<br><br>";
				$message .= __('If this was a mistake, ignore this email and nothing will happen.', 'weadapt') . "<br><br>";
				$message .= __('To reset your password, visit the following address:', 'weadapt') . "<br><br>";
				$message .= home_url("?action=reset-password&key=$key&login=" . rawurlencode($current_user->user_login), 'login') . "<br><br>";

				$send = theme_mail(
					$current_user->user_email,
					sprintf('[%s] %s', get_bloginfo('name'), __('Password Reset', 'weadapt')),
					$message
				);

				if (!$send) {
					die_json_message('error', esc_html__('The email could not be sent. Your site may not be correctly configured to send emails.', 'weadapt'));
				} else {
					die_json_message('success', esc_html__('Thank you! We sent an e-mail to your inbox to reset your password. This action can take up to 30 seconds. Also check your spam folder.', 'weadapt'), false, 15000);
				}
			}
		}
	}

	die();
}
add_action('wp_ajax_theme_ajax_forgot', 'theme_ajax_forgot');
add_action('wp_ajax_nopriv_theme_ajax_forgot', 'theme_ajax_forgot');


/**
 * Ajax Create
 */
function theme_ajax_create()
{
	if (!wp_verify_nonce($_POST['ajax_create_nonce'], 'ajax-create-nonce')) {
		die_json_message('error', esc_html__('Sorry, the verification data does not match', 'weadapt'));
	} else {
		//reCaptcha
		// $google_recaptcha_secret_key = get_field( 'google_recaptcha_secret_key', 'options' );

		// if ( ! empty( $google_recaptcha_secret_key ) ) {
		// 	$recaptcha_response = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', [
		// 		'body' => [
		// 			'secret'   => $google_recaptcha_secret_key,
		// 			'response' => $_POST['g-recaptcha-response'],
		// 		],
		// 	]);

		// 	if ( is_wp_error( $recaptcha_response ) ) {
		// 		die_json_message( 'error', __( 'reCAPTCHA verification failed!', 'weadapt' ) );
		// 	}

		// 	$recaptcha_body   = wp_remote_retrieve_body( $recaptcha_response );
		// 	$recaptcha_result = json_decode($recaptcha_body, true);

		// 	if ( ! isset( $recaptcha_result['success'] ) || ! wp_validate_boolean( $recaptcha_result['success'] ) ) {
		// 		die_json_message( 'error', __( 'reCAPTCHA verification failed!', 'weadapt' ) );
		// 	}
		// }

		$user_first_name = sanitize_text_field(trim($_POST['user_first_name']), 1);
		$user_last_name = sanitize_text_field(trim($_POST['user_last_name']), 1);
		$user_name = sanitize_user(trim($_POST['user_name']), 1);
		$user_email = sanitize_email(trim($_POST['user_email']));
		$user_pass = sanitize_text_field(trim(wp_unslash($_POST['user_pass'])));
		$user_pass_confirm = sanitize_text_field(trim(wp_unslash($_POST['user_pass_confirm'])));
		$redirect_url = esc_url(trim($_POST['redirect_to']));
		$address_country = sanitize_text_field(trim($_POST['country']));
		$address_town_city = sanitize_text_field(trim($_POST['city']));
		$address_county = sanitize_text_field(trim($_POST['county']));
		if (isset($_POST['add_org']) && $_POST['add_org'] === '1') {
			$redirect_url = add_query_arg('account_created', 'true', home_url('profile'));
		}
		
		if ($user_pass !== $user_pass_confirm) {
			die_json_message('error', __("Passwords don't match!", 'weadapt'));
		}

		$user_ID = wp_insert_user(
			array(
				'first_name' => $user_first_name,
				'last_name' => $user_last_name,
				'user_login' => $user_name,
				'user_email' => $user_email,
				'user_pass' => $user_pass,
			));

		if (is_wp_error($user_ID)) {
			die_json_message('error', $user_ID->get_error_message());
		} else {
			$roles = isset($_POST['role']) ? $_POST['role'] : array();
			update_field('role', $roles, 'user_' . $user_ID);
			$selected_organisation = sanitize_text_field($_POST['organisation']);
			update_field('organisations', $selected_organisation, 'user_' . $user_ID);
			update_user_meta($user_ID, 'user_roles', $roles);
			update_user_meta($user_ID, 'organisation', sanitize_text_field($_POST['organisation']));
			update_user_meta($user_ID, 'address_country', sanitize_text_field($_POST['country']));
			update_user_meta($user_ID, 'address_city', sanitize_text_field($_POST['city']));
			update_user_meta($user_ID, 'address_county', sanitize_text_field($_POST['county']));
			error_log("Organisation saved for user: " . get_user_meta($user_ID, 'organisation', true));
			error_log("Country saved for user: " . get_user_meta($user_ID, 'address_country', true));
			error_log("City saved for user: " . get_user_meta($user_ID, 'address_city', true));
			migrate_user_to_member_relationship_single($user_ID);
			do_action('custom_user_meta_saved', $user_ID, array(
				'first_name' => $user_first_name,
				'last_name' => $user_last_name,
				'user_login' => $user_name,
				'user_email' => $user_email,
			));
			if (isset($_POST['mailchimp_subscribe']) && $_POST['mailchimp_subscribe'] === '1') {
				$api_key = '8b4651ed405334d02ccaa5870f9b2770-us22';
				$audience_id = '1d67a109f9';
				$data_center = substr($api_key, strpos($api_key, '-') + 1);
				$url = 'https://' . $data_center . '.api.mailchimp.com/3.0/lists/' . $audience_id . '/members';
				$data = [
					'email_address' => $user_email,
					'status'        => 'subscribed',
					'merge_fields'  => [
						'FNAME'   => $user_first_name,
						'LNAME'   => $user_last_name,
						'ADDRESS' => [
							'addr1'   => $address_town_city, 
							'city'    => $address_town_city,
							'state'   => $address_county, 
							'zip'     => '00000', 
							'country' => $address_country
						]
					]
				];

				$json_data = json_encode($data);
				error_log(print_r($json_data, true)); // Log the data to the PHP error log for inspection
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $api_key);
				curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_TIMEOUT, 10);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

				$result = curl_exec($ch);
				$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);

				if ($http_code != 200) {
					$response = json_decode($result, true);
					$error_detail = $response['detail'] ?? 'Unknown error occurred.';
					die_json_message('error', "Mailchimp Error: $error_detail");
				}
			}

			$new_user_additional_mail['subject'] = 'Welcome to the Agora Community Hub';
			$new_user_additional_mail['message'] = "<p>[" . get_bloginfo('name') . "] Login Details</p>" .
				"<p>Username: " . $user_name . "<br />Password: " . $user_pass . "</p><br />" .
				"<p>Dear " . $user_name . ",</p><br />" .
				"<p>Welcome to the  <a href=\"https://agoracommunity.org\">Agora Community Hub</a>!</p><br />" .
				"<p>This is a <a href=\"https://weadapt.org/microsites/\"> weADAPT microsite</a> which connects to the <a href=\"https://weadapt.org\"> weADAPT community and its content </a> to amplify the impact of your work.</p><br />" .
				"<p>Here you can <b>learn</b> about climate adaptation issues,<b>share</b> your knowledge and experience, <b>connect</b> with citizens, researchers, practitioners and decision-makers and create adaptation <b>action</b> around the world.</p>" .
				"<p>We encourage you to upload a profile image that portrays climate change adaptation challenges or opportunities in your region. Please fill in the information on your profile page as much as possible, as this will make it more easily searchable and will ensure you see content that is relevant to you.</p>" .
				"<p>Sharing information on the  <a href=\"https://agoracommunity.org\">Agora Community Hub</a> is simple - just follow these step-by-step guidelines for <a href=\"https://agoracommunity.org/agora-guidance/\">adding content</a>.</p>" .
				"<p>To keep up to date with the latest news and content you can join us on <a href=\"https://www.linkedin.com/company/adaptation-agora/\">LinkedIn</a>, <a href=\"https://www.linkedin.com/company/adaptation-agora\">Twitter/X</a> or <a href=\"https://www.facebook.com/profile.php?id=100090701292038\">Facebook</a>, and <a href=\"https://adaptationagora.eu/join-our-community/\">join the community</a>.</p><br />" .
				"<p>We use these outlets to share your content widely and give it the greatest visibility.</p><br />" .
				"<p>If you require assistance or would like training on the use of the platform, please get in touch with us at <a href=\"mailto:info@weadapt.org\">info@weadapt.org</a>.</p><br />" .
				"<p>We welcome your <a href=\"mailto:info@weadapt.org\">feedback</a>!</p><br />" .
				"<p>Kind regards,</p>" .
				"<p>The Agora Community Hub Team</p>";

			$send = theme_mail(
				$user_email,
				$new_user_additional_mail['subject'],
				$new_user_additional_mail['message']
			);

			if (!$send) {
				die_json_message('error', esc_html__('The email could not be sent. Your site may not be correctly configured to send emails.', 'weadapt'));
			} else {
				theme_auth_user($user_name, $user_pass, $redirect_url, esc_html__('Register successful, redirecting...', 'weadapt'));
			}
		}
	}

	die();
}
add_action('wp_ajax_theme_ajax_create', 'theme_ajax_create');
add_action('wp_ajax_nopriv_theme_ajax_create', 'theme_ajax_create');

function migrate_user_to_member_relationship_single($user_id) {
    // Fetch user details
    $user_info = get_userdata($user_id);
    $username = $user_info->user_login;

    // Fetch user meta fields
    $address_country = get_user_meta($user_id, 'address_country', true);
    $address_city = get_user_meta($user_id, 'address_city', true);
    $address_county = get_user_meta($user_id, 'address_county', true);

    // Ensure the address fields are strings, not arrays
    if (is_array($address_country)) {
        $address_country_str = isset($address_country[1]) ? $address_country[1] : implode(', ', $address_country); // Use second value or fallback
    } else {
        $address_country_str = $address_country;
    }

    error_log('Country: ' . $address_country_str);

    // Store the user meta fields as new user meta
    update_user_meta($user_id, 'migrated_username', $username);
    update_user_meta($user_id, 'migrated_address_country', $address_country_str);
    update_user_meta($user_id, 'migrated_address_city', $address_city);
    update_user_meta($user_id, 'migrated_address_county', $address_county);

    error_log('Updated user meta fields for user ID: ' . $user_id);

    // Additional logic for location data (if necessary)
    if ($address_country_str && $address_city && $address_county) {
        // Build the query URL to convert address into location (latitude, longitude)
        $query_url = "https://nominatim.openstreetmap.org/search.php?county=" . urlencode($address_county) . "&country=" . urlencode($address_country_str) . "&format=jsonv2";
        error_log('Query URL: ' . $query_url);

        // Make the HTTP request for geocoding the location
        $response = wp_remote_get($query_url);

        if (!is_wp_error($response)) {
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                $lat = floatval($data[0]['lat']);
                $lng = floatval($data[0]['lon']);

                // Save location data as a new user meta field
                $location_value = array(
                    'zoom' => 14,
                    'lat' => $lat,
                    'lng' => $lng,
                );

                // Store the location data in the user meta
                update_user_meta($user_id, 'location_users', $location_value);
                update_user_meta($user_id, '_location_users', 'field_65fcef62959d4'); // Assuming this is the ACF field key for location

                error_log('Converted and saved location for user ID: ' . $user_id);
            }
        }
    }

    error_log('User migration completed for user ID: ' . $user_id);
}



/**
 * Ajax Reset
 */
function theme_ajax_reset()
{
	require_once (ABSPATH . 'wp-includes/class-phpass.php');

	if (!wp_verify_nonce($_POST['ajax_reset_nonce'], 'ajax-reset-nonce')) {
		die_json_message('error', esc_html__('Sorry, the verification data does not match', 'weadapt'));
	} else {
		$user_pass = sanitize_text_field(trim(wp_unslash($_POST['user_pass'])));
		$user_pass_confirm = sanitize_text_field(trim(wp_unslash($_POST['user_pass_confirm'])));

		if ($user_pass !== $user_pass_confirm) {
			die_json_message('error', __("Passwords don't match!", 'weadapt'));
		}

		$user_login = !empty($_POST['user_login']) ? sanitize_text_field(trim($_POST['user_login'])) : '';
		$user_key = !empty($_POST['user_key']) ? sanitize_text_field(trim($_POST['user_key'])) : '';

		if ($user_login && $user_key) {
			$current_user = check_password_reset_key($user_key, $user_login);

			if (is_wp_error($current_user)) {
				die_json_message('error', str_replace('<strong>Error</strong>: ', '', $current_user->get_error_message()));
			}
		} else {
			$current_user = wp_get_current_user();
		}

		if (!$current_user->exists()) {
			die_json_message('error', esc_html__('We can\'t find an your account.', 'weadapt'));
		} else {
			wp_set_password($user_pass, $current_user->ID);

			die_json_message('success', esc_html__('Your password has been reset.', 'weadapt'), false, 15000, 'sign-in');
		}
	}

	die();
}
add_action('wp_ajax_theme_ajax_reset', 'theme_ajax_reset');
add_action('wp_ajax_nopriv_theme_ajax_reset', 'theme_ajax_reset');