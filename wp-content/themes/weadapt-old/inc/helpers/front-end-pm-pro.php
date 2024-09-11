<?php


// Logs Helper
if ( ! function_exists( 's' ) ) {
	function s( $data ) {
		echo '<pre>';
		var_dump( $data );
		echo '</pre>';
	}
}

// TEMP
// add_filter('fep_filter_ajax_notification_interval', function( $time ) {
// 	return 5 * 1000; // 5 sec
// }, 10, 3);


/**
 * Fix Default Multisite Options
 */
// Global Options
add_filter( 'weadapt_fep_default_options', function( $fields ) {
	$fields = array(
		// General
		'editor_type'      => 'textarea',
		'time_delay'       => 0,
		'allow_attachment' => false,
		'show_directory'   => false,
		'show_branding'    => false,

		// Appearance
		'load_css' => 'never',

		// Privacy
		'export_messages'      => false,
		'export_announcements' => false,

		// Security
		'add_ann_frontend'  => false,
		'reply_deleted_mgs' => true,

		// Misc
		'show_notification'                   => false,
		'show_unread_count_in_desktop'        => false,
		'message_box_administrator'           => 0,
		'message_box_author'                  => 0,
		'message_box_contributor'             => 0,
		'message_box_subscriber'              => 0,
		'message_box_pro'                     => 0,
		'message_box_survey-administrator'    => 0,
		'message_box_no-notifs-administrator' => 0,
		'message_box_editor'                  => 0,
	);

	return $fields;
} );

// Set Default
add_filter( 'fep_settings_fields', function( $fields ) {
	foreach ( apply_filters( 'weadapt_fep_default_options', [] ) as $option_key => $default_value ) {
		if ( isset( $fields[$option_key]['value'] ) ) {
			$fields[$option_key]['value'] = fep_get_option( $option_key, $default_value );
		}
	}

	return $fields;
});

// Get Default
add_filter( 'fep_get_option', function( $value, $option, $default, $is_default ) {
	$fields = apply_filters( 'weadapt_fep_default_options', [] );

	if ( array_key_exists( $option, $fields ) && $is_default ) {
		$value = $fields[$option];
	}

	return $value;
}, 10, 4);



/**
 * Remove announcement caps
 */
add_filter('fep_current_user_can', function( $can, $cap, $id ) {
	if ( in_array( $cap, array(
		'add_announcement',
		'delete_announcement',
		'view_announcement',
	) ) ) {
		return false;
	}

	return $can;
}, 10, 3);


/**
 * Remove admin pages
 */
add_action( 'admin_menu', function() {
	foreach ( [
		'messages',
		'announcements',
		'attachments'
	] as $base_name ) {
		remove_submenu_page( 'fep-all-messages', "fep-all-$base_name" );
	}

	// All Groups
	remove_submenu_page( 'fep-all-messages', 'edit.php?post_type=fep_group' );
}, 100 );


/**
 * Redirect admin pages
 */
add_action( 'current_screen', function() {
	$current_screen = get_current_screen();

	if ( ! empty( $current_screen->base ) ) {
		foreach ( [
			'front-end-pm-pro_page_fep-all-announcements',
			'front-end-pm-pro_page_fep-all-attachments'
		] as $base_name ) {
			if ( $base_name === $current_screen->base ) {
				wp_safe_redirect( admin_url() );
			}
		}
	}

	// Only if has_active_valid_license()
	if ( 'toplevel_page_fep-all-messages' === $current_screen->base ) {
		global $fep_fs;

		if ( isset( $fep_fs ) && $fep_fs->has_active_valid_license() ) {
			wp_safe_redirect( admin_url() );
		}
	}
} );


/**
 * Message date format
 */
add_filter( 'fep_formate_date', function( $h_time, $date ) {
	if ( '0000-00-00 00:00:00' === $date ) {
		$h_time = __( 'Unpublished', 'front-end-pm' );
	} else {
		$time = strtotime( $date );

		if ( ( abs( time() - $time ) ) < DAY_IN_SECONDS ) {
			$h_time = mysql2date( 'g:i', get_date_from_gmt( $date ) );
		} else {
			$h_time = mysql2date( 'M d', get_date_from_gmt( $date ) );
		}
	}

	return $h_time;
}, 10, 2 );


/**
 * Content Templates
 */
// Disable double avatars
add_filter( 'fep_remove_own_avatar_from_messagebox', '__return_true' );

// Column Author template
add_action( 'fep_message_table_column_content_title', function() {
	$fep_ID = fep_get_the_id();

	?><span class="fep-message" fep-id="<?php echo esc_attr( $fep_ID ); ?>"><?php echo fep_get_the_title(); ?></span><?php
} );

// Disable Message receipt
add_filter( 'fep_filter_read_receipt', '__return_false' );

// Reply button text
add_filter( 'fep_form_submit_button', function( $output, $where ) {
	if ( 'reply' == $where ) {
		$output = sprintf( '<button type="submit" class="fep-button">%s</button>', __( 'Send message', 'weadapt' ) );
	}

	return $output;
}, 10, 2 );

add_action( 'fep_before_form_fields', function( $where, $errors ) {
	if ( 'reply' == $where ) {
		$current_user = wp_get_current_user();

		?>
			<div class="fep-form__author">
				<div class="fep-form__author__avatar"><?php echo get_avatar( $current_user->ID, 80 ); ?></div>
				<div class="fep-form__author__name"><?php echo $current_user->display_name; ?></div>
			</div>
		<?php
	}
}, 10, 2 );

// Message attributes
add_filter( 'fep_form_fields_after_process', function( $fields, $where ) {
	if ( 'reply' == $where && isset( $fields['message_content'] ) ) {
		$fields['message_content']['label'] = '';
		$fields['message_content']['placeholder'] = __( 'Write your message here', 'weadapt' );
	}

	return $fields;
}, 10, 2 );


/**
 * Add js response variable message_unread_ids
 */
add_filter( 'fep_filter_notification_response', function( $response ) {
	if ( ! empty( $userid = get_current_user_id() ) ) {
		$response['message_unread_ids'] = fep_get_messages( array(
			'mgs_type'          => 'message',
			'mgs_status'        => 'publish',
			'mgs_parent'        => 0,
			'per_page'          => 0,
			'fields'            => 'ids',
			'participant_query' => array( array(
				'mgs_participant' => $userid,
				'mgs_parent_read' => false,
				'mgs_deleted'     => false,
			) ),
		) );
	}

	return $response;
} );


/**
 * Loadmore Pagination
 */
function weadapt_fep_pagination_prev_next( $has_more_row ) {
	$feppage = ! empty( $_GET['feppage'] ) ? absint( $_GET['feppage'] ) : 1;
	?>
		<div class="wp-block-button wp-block-button--template messages__more<?php echo !$has_more_row ? ' hidden' : ''; ?>">
			<button type="button" class="wp-block-button__link messages__more__button" data-page="<?php echo intval( $feppage ); ?>"><?php _e( 'Load more', 'weadapt' ); ?></button>
		</div>
	<?php
}


/**
 * Ajax Template Message View
 */
function action_messages_view() {
	if (
		empty( $_POST['nonce'] ) ||
		empty( $_POST['fep_id'] ) ||
		! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'view-messages' )
	) {
		wp_die( json_encode( array(
			'status' => 'error',
			'message' => __( 'You cannot view this message.', 'weadapt' )
		) ) );
	}

	$parent_id = intval( $_POST['fep_id'] );
	$is_update = isset( $_POST['is_update'] ) ? wp_validate_boolean( $_POST['is_update'] ) : false;
	$messages  = Fep_Messages::init()->get_message_with_replies( $parent_id );

	add_filter( 'fep_form_attribute', function( $form_attr, $where ) use ( $parent_id ) {
		if (
			'reply' === $where &&
			isset( $form_attr['action'] ) &&
			'/wp-admin/admin-ajax.php' === $form_attr['action']
		) {
			$profile_ID = get_page_id_by_template( 'profile' );

			if ( ! empty( $profile_ID ) ) {
				$form_attr['action'] = add_query_arg( array(
					'fepaction' => 'viewmessage',
					'fep_id'    => $parent_id,
				), get_permalink( $profile_ID ) );
			}
		}

		return $form_attr;
	}, 10, 2 );

	if ( ! fep_current_user_can( 'view_message', $parent_id ) ) {
		wp_die( json_encode( array(
			'status' => 'error',
			'message' => __( 'You do not have permission to view this message!', 'weadapt' )
		) ) );
	}

	ob_start();
	require_once( fep_locate_template( 'view-message-content.php' ) );

	if ( ! $is_update ) {
		require_once( fep_locate_template( 'form-reply.php' ) );
	}
	$content = ob_get_clean();


	// Fix unread messages count.
	$user_id         = get_current_user_id();
	$unread_messages = fep_get_messages( array(
		'mgs_type'          => 'message',
		'mgs_status'        => 'publish',
		'mgs_parent'        => $parent_id,
		'per_page'          => 0,
		'fields'            => 'ids',
		'participant_query' => array( array(
			'mgs_participant' => get_current_user_id(),
			'mgs_parent_read' => false,
			'mgs_deleted'     => false,
		) ),
	) );

	if ( ! empty( $unread_messages ) ) {
		foreach ( $unread_messages as $message_id ) {
			FEP_Participants::init()->mark( $message_id, $user_id, ['read' => true, 'parent_read' => true ] );
		}

		fep_make_read( true, $parent_id, $user_id );
	}

	wp_die( json_encode( array(
		'status'  => 'success',
		'content' => $content,
		'unread'  => fep_get_new_message_number()
	) ) );
}
add_action( 'wp_ajax_messages_view', 'action_messages_view' );


/**
 * Ajax Template Message View
 */
function action_messages_filter() {
	ob_start();
	$box_content = Fep_Messages::init()->user_messages();

	require_once( fep_locate_template( 'box-message.php' ) );
	$content = ob_get_clean();

	wp_die( json_encode( array(
		'status'  => 'success',
		'content' => $content,
		'unread'  => fep_get_new_message_number()
	) ) );
}
add_action( 'wp_ajax_messages_filter', 'action_messages_filter' );


/**
 * Ajax Template Message View
 */
function action_messages_action() {
	$box_content = Fep_Messages::init()->user_messages();

	require_once( fep_locate_template( 'box-message.php' ) );

	die();
}
add_action( 'wp_ajax_messages_action', 'action_messages_action' );