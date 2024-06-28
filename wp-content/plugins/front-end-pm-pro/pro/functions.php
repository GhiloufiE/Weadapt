<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function fep_can_send_to_max_recipients(){
	$max_repipients = absint( fep_get_option('mr-max-recipients', 5 ) );
	
	return apply_filters( 'fep_can_send_to_max_recipients', $max_repipients );
}

function fep_eb_reschedule_event() {
	if ( wp_next_scheduled( 'fep_eb_ann_email_interval_event' ) ) {
		wp_clear_scheduled_hook( 'fep_eb_ann_email_interval_event' );
	}
	wp_schedule_event( time(), 'fep_ann_email_interval', 'fep_eb_ann_email_interval_event' );
}

function fep_is_group_message( $mgs_id ) {
	return (bool) fep_get_meta( $mgs_id, '_fep_group_id', true );
}

function fep_get_group_name( $mgs_id ) {
	$return = '';
	if ( fep_is_group_message( $mgs_id ) ) {
		$group = fep_get_meta( $mgs_id, '_fep_group_name', true );
		if ( $group ) {
			$return = $group;
		} else {
			$return = fep_get_meta( $mgs_id, '_fep_group_id', true );
		}
	}
	return $return;
}

function fep_fs_license_key_migration() {
	if ( ! fep_fs()->has_api_connectivity() || fep_fs()->is_registered() ) {
		// No connectivity OR the user already opted-in to Freemius.
		return;
	}
	
	if ( 'pending' != get_option( 'fep_fs_migrated2fs', 'pending' ) ) {
		return;
	}

	// Get the license key from the previous eCommerce platform's storage.
	$license_key = trim( fep_get_option( 'front_end_pm_pro_license_key', '' ) );

	if ( empty( $license_key ) ) {
		// No key to migrate.
		return;
	}

	// Get the first 32 characters.
	$license_key = substr( $license_key, 0, 32 );

	try {
		$next_page = fep_fs()->activate_migrated_license( $license_key );
	} catch (Exception $e) {
		update_option( 'fep_fs_migrated2fs', 'unexpected_error' );
		return;
	}

	if ( fep_fs()->can_use_premium_code() ) {
		update_option( 'fep_fs_migrated2fs', 'done' );

		if ( is_string( $next_page ) ) {
			fs_redirect( $next_page );
		}
	} else {
		update_option( 'fep_fs_migrated2fs', 'failed' );
	}
}

add_action( 'admin_init', 'fep_fs_license_key_migration' );
