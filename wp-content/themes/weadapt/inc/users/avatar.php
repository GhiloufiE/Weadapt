<?php

/**
 *  Override get_avatar with uploaded avatar else default.
 */
add_filter( 'pre_get_avatar_data', function( $args, $id_or_email ) {
	$args['default'] = 'blank';

	return $args;
}, 10, 2 );

add_filter( 'get_avatar', function( $avatar, $id_or_email, $size, $default, $alt, $args ) {
	$id_or_email    = isset( $id_or_email->user_id ) ? $id_or_email->user_id : $id_or_email;
	$transient_name = sprintf( 'avatar_%s_%s', $id_or_email, $size );

	$avatar_url  = get_transient( $transient_name );

	if ( false === $avatar_url ) {
		$avatar_ID   = get_field( 'avatar', 'user_' . $id_or_email );
		$default_url = get_theme_file_uri( '/assets/images/svg/avatar.svg' );

		if ( ! empty( $avatar_ID ) ) {
			$thumbnail = 'mini-thumbnail';

			if ( $size > 40 ) {
				$thumbnail = 'thumbnail';

				if ( $size > 150 ) {
					$thumbnail = 'medium';
				}
				elseif ( $size > 320 ) {
					$thumbnail = 'large';
				}
				elseif ( $size > 500 ) {
					$thumbnail = 'full';
				}
			}

			$avatar_data = wp_get_attachment_image_src( $avatar_ID, $thumbnail );
			$avatar_url  = ! empty( $avatar_data[0] ) ? $avatar_data[0] : $default_url;
		}
		else {
			$response = wp_remote_get( $args['url'] );

			if ( is_wp_error( $response ) ) {
				$avatar_url = $default_url;
			}
			else {
				$avatar_data = getimagesizefromstring( wp_remote_retrieve_body( $response ) );

				if ( false !== $avatar_data && 'image/png' === $avatar_data['mime'] ) {
					$avatar_url = $default_url;
				}
			}
		}

		set_transient( $transient_name, $avatar_url, HOUR_IN_SECONDS );
	}

	if ( ! empty( $avatar_url ) ) {
		$avatar = "<img alt='{$alt}' src='{$avatar_url}' class='avatar avatar-{$size} photo avatar-default' height='{$size}' width='{$size}' />";
	}

	return $avatar;
}, 10, 6 );


/**
 * Function for `profile_update` action-hook.
 *
 * @param int     $user_id       User ID.
 * @param WP_User $old_user_data Object containing user's data prior to update.
 * @param array   $userdata      The raw array of data passed to wp_insert_user().
 */
add_action( 'profile_update', function ( $user_id, $old_user_data, $userdata ) {
	foreach ( [ 26, 64, 80, 98, 608 ] as $avatar_size ) {
		delete_transient( sprintf( 'avatar_%s_%s', $user_id, $avatar_size ) );
	}
}, 10, 3 );