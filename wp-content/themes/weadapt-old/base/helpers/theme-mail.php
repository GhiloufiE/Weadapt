<?php

/**
 * Theme Mail
 */
function theme_mail( $to, $subject = '', $message = '', $from = '' ) {
	if ( defined( 'IS_IMPORT' ) && IS_IMPORT ) {
		return false;
	}

	$send   = false;
	$domain = str_replace( array( 'https://', 'http://' ), '', home_url() );

	if ( substr( $domain, -4 ) === '/web' ) {
		$domain = str_replace( '/web', '.com', $domain );
	}

	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-Type: text/html; charset=ISO-8859-1' . "\r\n";

	$headers .= 'From: ';
	$headers .= ! empty( $from ) ? $from : get_bloginfo( 'name' ) . ' <wordpress@' . $domain . '>';
	$headers .= "\r\n";

	// Temp Admin Email for Debug
	if ( defined( 'TEMP_ADMIN_EMAIL' ) ) {
		$message =  'Logs mail to: ' . $to . '<br><br>' . $message;
		$to      = TEMP_ADMIN_EMAIL;
	}

	// Send
	if ( function_exists( 'wp_mail' ) ) {
		$send = wp_mail( $to, $subject, $message, $headers );
	}

	if ( ! $send ) {
		$send = mail( $to, $subject, $message, $headers );
	}

	// Debug Logs
	error_log( print_r( [$to, $subject, str_replace( '<br>', PHP_EOL, $message ), $headers], true ) );

	return $send;
}

/**
 * Save Theme Mails to Database
 */
function theme_mail_save_to_db( $to, $subject = '', $message = '' ) {
	global $wpdb;
	global $mail_table;

	$domain = str_replace( array( 'https://', 'http://' ), '', home_url() );

	if ( substr( $domain, -4 ) === '/web' ) {
		$domain = str_replace( '/web', '.com', $domain );
	}

	$wpdb->insert( $mail_table, [
		'to'        => maybe_serialize( $to ),
		'from'      => get_bloginfo( 'name' ) . ' <wordpress@' . $domain . '>',
		'pending'   => maybe_serialize( $to ),
		'subject'   => $subject,
		'message'   => $message,
		'timestamp' => time(),
		'status'    => 'created'
	] );
}