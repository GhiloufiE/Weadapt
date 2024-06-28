<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Fep_Email_POP3 {

	private static $instance;

	public static function init() {
		if ( ! self::$instance instanceof self ) {
			self::$instance = new self();
		}
			return self::$instance;
	}

	function actions_filters() {
		add_filter( 'fep_settings_fields', array( $this, 'settings_fields' ) );
		add_action( 'fep_pop3_event', array( $this, 'pop3_event' ) );
	}

	function settings_fields( $fields ) {

		$fields['ep_enable']['options']['pop3'] = __( 'POP3', 'front-end-pm' );
		
		$fields['pop3_host'] = array(
			'type'     => 'text',
			'class'    => 'fep-pop3-hidden-field hidden',
			'section'  => 'email_piping',
			'priority' => 12,
			'value'    => fep_get_option( 'pop3_host', '' ),
			'label'    => __( 'POP3 Host', 'front-end-pm' ),
		);
		$fields['pop3_port'] = array(
			'type'     => 'number',
			'class'    => 'fep-pop3-hidden-field hidden',
			'section'  => 'email_piping',
			'priority' => 13,
			'value'    => fep_get_option( 'pop3_port', '' ),
			'label'    => __( 'POP3 Port', 'front-end-pm' ),
		);

		$fields['pop3_password']        = array(
			'type'     => 'text',
			'class'    => 'fep-pop3-hidden-field hidden',
			'section'  => 'email_piping',
			'priority' => 21,
			'label'    => __( 'POP3 Password', 'front-end-pm' ),
		);
		$fields['pop3_encryption']      = array(
			'type'     => 'select',
			'class'    => 'fep-pop3-hidden-field hidden',
			'section'  => 'email_piping',
			'priority' => 25,
			'value'    => fep_get_option( 'pop3_encryption', 'ssl' ),
			'label'    => __( 'Encryption', 'front-end-pm' ),
			'options'  => array(
				''    => __( 'None', 'front-end-pm' ),
				'ssl' => __( 'SSL', 'front-end-pm' ),
				'tls' => __( 'TLS', 'front-end-pm' ),
			),
		);
		$fields['pop3_novalidate-cert'] = array(
			'type'     => 'checkbox',
			'class'    => 'fep-pop3-hidden-field hidden',
			'section'  => 'email_piping',
			'priority' => 26,
			'value'    => fep_get_option( 'pop3_novalidate-cert' ),
			'label'    => __( 'No validate certificates', 'front-end-pm' ),
			'cb_label' => __( 'If server has certificate problem or uses self-signed certificates check this', 'front-end-pm' ),
		);
		if ( defined( 'FEP_POP3_PASSWORD' ) ) {
			$fields['pop3_password']['value']       = '********';
			$fields['pop3_password']['description'] = __( 'Password is defined as FEP_POP3_PASSWORD constant', 'front-end-pm' );
		} else {
			$fields['pop3_password']['value']       = fep_get_option( 'pop3_password', '' );
			$fields['pop3_password']['description'] = __( 'You can define your password in wp-config.php', 'front-end-pm' ) . "<br /><code>define( 'FEP_POP3_PASSWORD', 'your-pop3-password-here' );</code>";
		}
		if ( ! function_exists( 'imap_open' ) ) {
			$fields['pop3_unavailable'] = array(
				'type'     => 'html',
				'class'    => 'fep-pop3-hidden-field hidden',
				'section'  => 'email_piping',
				'priority' => 11,
				'value'    => '<div class="notice notice-error inline">
					<p>' . __( 'POP3 is not available in this server. Contact your host and tell them to enable imap extension if you want to use this feature.', 'front-end-pm' ) . '</p></div>',
			);
		}

		return $fields;

	}

	function pop3_event() {
		if ( 'pop3' !== fep_get_option( 'ep_enable' ) ) {
			return;
		}
		if ( ! function_exists( 'imap_open' ) ) {
			// error_log( 'imap_open is not available' );
			return;
		}
		if ( ! fep_is_func_disabled( 'set_time_limit' ) ) {
			set_time_limit( 0 );
		}

		$host       = fep_get_option( 'pop3_host', '' );
		$port       = fep_get_option( 'pop3_port', '' );
		$encryption = fep_get_option( 'pop3_encryption', 'ssl' );
		$username   = fep_get_option( 'ep_email', get_bloginfo( 'admin_email' ) );
		if ( defined( 'FEP_POP3_PASSWORD' ) ) {
			$password = FEP_POP3_PASSWORD;
		} else {
			$password = stripslashes( fep_get_option( 'pop3_password', '' ) );
		}
		if ( $encryption && fep_get_option( 'pop3_novalidate-cert' ) ) {
			$encryption .= '/novalidate-cert';
		}

		if ( ! $host || ! $port || ! $username || ! $password ) {
			// error_log( 'imap setup missing' );
			return;
		}
		$mailbox = sprintf( '{%s:%d/pop3%s}INBOX', $host, $port, $encryption ? "/$encryption" : '' );
		$mailbox = apply_filters( 'fep_filter_pop3_mailbox', $mailbox );
		$mail    = imap_open( $mailbox, $username, $password );

		// error_log( 'pop3 event start' );
		
		if ( ! $mail ) {
			// error_log( 'imap mail stream not available' );
			return;
		}
		$num_mgs = imap_num_msg( $mail );
		$num_mgs = min( $num_mgs, apply_filters( 'fep_filter_pop3_max', 100 ) );
		for ( $n = 1; $n <= $num_mgs; $n++ ) {
			$raw = imap_fetchheader( $mail, $n ) . imap_body( $mail, $n );
			$ep  = new FEP_Email_Parser();
			$ep->setRaw( $raw );
			$ep->decode();

			$process = new FEP_Email_Process( $ep );
			/*
			if ( ! empty( $process->inserted_id ) ) {
				error_log( sprintf( 'Message inserted id %d for subject "%s" and serial %d', $process->inserted_id, $ep->subject(), $n ) );
			}
			 */
			imap_delete( $mail, $n );
		}

		imap_close( $mail, CL_EXPUNGE );
		// error_log( 'pop3 event finish' );
	}

} //END CLASS

add_action( 'init', array( Fep_Email_POP3::init(), 'actions_filters' ) );
