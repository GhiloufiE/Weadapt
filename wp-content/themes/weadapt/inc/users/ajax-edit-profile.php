<?php

/**
 * Edit Profile
 */
function theme_ajax_edit_profile() {
	$is_reload = false;

	if (
		empty( $_POST['ajax_user_edit_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( $_POST['ajax_user_edit_nonce'] ), 'ajax-user-edit-nonce' )
	) {
		die_json_message( 'error', __( 'Sorry, the verification data does not match', 'weadapt' ) );
	}

	$user_ID   = ! empty( $_POST['user_id'] ) ? $_POST['user_id'] : 0;
	$user_data = get_userdata( $user_ID );

	if ( empty( $user_data ) ) {
		die_json_message( 'error', __( 'The user does not exist!', 'weadapt' ) );
	}

	$post_id   = "user_$user_ID";

	$first_name        = ! empty( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
	$last_name         = ! empty( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
	$organisation      = ! empty( $_POST['organisation'] ) ? wp_parse_id_list( $_POST['organisation'] ) : [];
	$country           = ! empty( $_POST['country'] ) && $_POST['country'] !== 'default' ? explode( '||', sanitize_text_field( $_POST['country'] ) ) : '';
	$town_city         = ! empty( $_POST['town_city'] ) ? sanitize_text_field( $_POST['town_city'] ) : '';
	$county            = ! empty( $_POST['county'] ) ? sanitize_text_field( $_POST['county'] ) : '';
	$timezone          = ! empty( $_POST['timezone'] ) && $_POST['timezone'] !== 'default' ? explode( '||', sanitize_text_field( $_POST['timezone'] ) ) : '';
	$contact_form      = ! empty( $_POST['contact_form'] );
	$mimemail_textonly = ! empty( $_POST['mimemail_textonly'] );
	$newsletter        = ! empty( $_POST['newsletter'] );
	$agreement         = ! empty( $_POST['agreement'] );
	$about_user        = ! empty( $_POST['about_user'] ) ? sanitize_text_field( $_POST['about_user'] ) : '';
	$job_title         = ! empty( $_POST['job_title'] ) ? sanitize_text_field( $_POST['job_title'] ) : '';
	$interest          = ! empty( $_POST['subjects-of-interest'] ) ? wp_parse_id_list( $_POST['subjects-of-interest'] ) : [];
	$referrer          = ! empty( $_POST['referrer'] ) ? sanitize_text_field( $_POST['referrer'] ) : '';
	$content_sought    = ! empty( $_POST['content_sought'] ) ? sanitize_text_field( $_POST['content_sought'] ) : '';
	$company           = ! empty( $_POST['company'] ) ? sanitize_text_field( $_POST['company'] ) : '';
	$twitter_url       = ! empty( $_POST['twitter_url'] ) ? sanitize_url( $_POST['twitter_url'] ) : '';
	$instagram_url     = ! empty( $_POST['instagram_url'] ) ? sanitize_url( $_POST['instagram_url'] ) : '';
	$website_url       = ! empty( $_POST['website_url'] ) ? sanitize_url( $_POST['website_url'] ) : '';
	$linkedin_url      = ! empty( $_POST['linkedin_url'] ) ? sanitize_url( $_POST['linkedin_url'] ) : '';
	$avatar            = ! empty( $_FILES['avatar'] ) ? $_FILES['avatar'] : [];

	// Account settings
	update_user_meta( $user_ID, 'first_name', $first_name );
	update_user_meta( $user_ID, 'last_name', $last_name );
	update_field( 'organisations', $organisation, $post_id );
	update_field( 'address_country', $country, $post_id );
	update_field( 'address_city', $town_city, $post_id );
	update_field( 'address_county', $county, $post_id );
	update_field( 'contact_form', $contact_form, $post_id );
	update_field( 'mimemail_textonly', $mimemail_textonly, $post_id );
	update_field( 'newsletter', $newsletter, $post_id );
	update_field( 'agreement', $agreement, $post_id );
	update_field( 'timezone', $timezone, $post_id );

	// Tell us more about you
	update_user_meta( $user_ID, 'description', $about_user );
	update_field( 'job_title', $job_title, $post_id );
	update_field( 'interest', $interest, $post_id );
	update_field( 'referrer', $referrer, $post_id );
	update_field( 'content_sought', $content_sought, $post_id );
	update_field( 'company', $company, $post_id );

	// Personal Details
	update_field( 'twitter_url', $twitter_url, $post_id );
	update_field( 'instagram_url', $instagram_url, $post_id );
	update_field( 'website_url', $website_url, $post_id );
	update_field( 'linkedin_url', $linkedin_url, $post_id );


	// Avatar
	if (
		empty( $avatar['error'] ) &&
		! empty( $avatar['name'] ) &&
		! empty( $avatar['type'] ) &&
		! empty( $avatar['tmp_name'] ) &&
		! empty( $avatar['size'] )
	) {

		// Check File Type
		if ( ! in_array( $avatar['type'], [
			'image/jpeg',
			'image/jpg',
			'image/png',
			'image/gif'
		] ) ) {
			die_json_message( 'error', __( 'File type not allowed. Allowed file types: png, gif, jpg, jpeg.', 'weadapt' ) );
		};

		// Check File Size
		if ( intval( $avatar['size'] ) > 25000000 ) {
			die_json_message( 'error', __( 'File size exceeds the allowed limit. Maximum file size is 256 MB.', 'weadapt' ) );
		}

		// Upload Avatar
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );

		$attachment_id = media_handle_upload( 'avatar', 0 );

		if ( is_wp_error( $attachment_id ) ) {
			die_json_message( 'error', $attachment_id->get_error_messages() );
		}
		else {
			$is_reload = true;

			update_field( 'avatar', $attachment_id, $post_id );

			foreach ( [ 26, 64, 80, 98, 608 ] as $avatar_size ) {
				delete_transient( sprintf( 'avatar_%s_%s', $user_ID, $avatar_size ) );
			}
		}
	}

	// Email/Password
	$current_password     = ! empty( $_POST['current_password'] ) ? sanitize_text_field( trim( wp_unslash( $_POST['current_password'] ) ) ) : '';
	$email_address        = ! empty( $_POST['email_address'] ) ? sanitize_email( $_POST['email_address'] ) : '';
	$new_password         = ! empty( $_POST['new_password'] ) ? sanitize_text_field( trim( wp_unslash( $_POST['new_password'] ) ) ) : '';
	$new_confirm_password = ! empty( $_POST['new_confirm_password'] ) ? sanitize_text_field( trim( wp_unslash( $_POST['new_confirm_password'] ) ) ) : '';

	// Email
	if ( $email_address !== $user_data->user_email ) {
		if ( ! filter_var( $email_address, FILTER_VALIDATE_EMAIL ) ) {
			die_json_message( 'error',  __( 'Email address is not well-formed!', 'weadapt' ) );
		}
		if ( empty( $current_password ) ) {
			die_json_message( 'error', __( 'Please enter a Current password to change your email.', 'weadapt' ) );
		}
		if ( ! wp_check_password( $current_password, $user_data->user_pass ) ) {
			die_json_message( 'error', __( 'The password is wrong!', 'weadapt' ) );
		}

		// Success
		wp_update_user( [
			'ID'         => $user_ID,
			'user_email' => $email_address
		] );
	}

	if ( ! empty( $new_password ) || ! empty( $new_confirm_password ) ) {
		if ( empty( $current_password ) ) {
			die_json_message( 'error', __( 'Please enter a Current password to change your password.', 'weadapt' ) );
		}
		if ( ! wp_check_password( $current_password, $user_data->user_pass ) ) {
			die_json_message( 'error', __( 'The password is wrong!', 'weadapt' ) );
		}
		if ( $new_password !== $new_confirm_password ) {
			die_json_message( 'error', __( 'The password doesn\'t match', 'weadapt' ) );
		}

		// Success
		wp_set_password( $new_password, $user_ID );

		$user_signon = wp_signon( [
			'user_login'    => $user_data->user_login,
			'user_password' => $new_password,
			'remember'      => true
		], is_ssl() );

		if ( is_wp_error( $user_signon ) ) {
			die_json_message( 'error', esc_html__( 'Oops! Something went wrong while updaing your account, redirecting...', 'weadapt' ), true );
		}
	}


	do_action( 'theme_profile_update', $user_ID, [], [] );


	// Success
	die_json_message( 'success', __( 'The user\'s data was successfully updated!', 'weadapt' ), $is_reload );
}
add_action( 'wp_ajax_theme_ajax_edit_profile', 'theme_ajax_edit_profile' );