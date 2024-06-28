<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Fep_Email_Piping
  {
	private static $instance;

	public static function init()
        {
            if(!self::$instance instanceof self) {
                self::$instance = new self;
            }
            return self::$instance;
        }

    function actions_filters()
    	{
			add_filter( 'fep_admin_settings_tabs', array($this, 'admin_settings_tabs' ) );
			add_filter( 'fep_settings_fields', array($this, 'settings_fields' ) );

			if( fep_get_option( 'ep_enable' ) ) {
				add_filter( 'fep_filter_before_email_send', array($this, 'add_reply_to' ) );
				add_filter( 'fep_filter_before_email_send', array($this, 'filter_before_email_send' ), 99, 3 );
			}

    	}

	function admin_settings_tabs( $tabs ) {

		$tabs['email_piping'] =  array(
				'section_title'		=> __('Email Piping/POP3', 'front-end-pm'),
				'section_page'		=> 'fep_settings_emails',
				'priority'			=> 53,
				'tab_output'		=> false
				);

		return $tabs;
	}

	function settings_fields( $fields )
		{
			$fields['ep_enable'] =   array(
				'type'	=>	'select',
				'class'	=> '',
				'section'	=> 'email_piping',
				'priority'	 => 10,
				'value' => fep_get_option('ep_enable', '' ),
				'label' => __( 'Enable', 'front-end-pm' ),
				'options'	=> array(
					''	=> __( 'None', 'front-end-pm' ),
					'piping'		=> __( 'Piping', 'front-end-pm' ),
				),
			);
			$fields['ep_email'] =   array(
				'type'	=>	'email',
				'section'	=> 'email_piping',
				'priority'	 => 20,
				'value' => fep_get_option('ep_email', get_bloginfo('admin_email') ),
				'label' => __( 'Email', 'front-end-pm' )
				);
			$fields['ep_clean_reply'] =   array(
				'type'	=>	'checkbox',
				'class'	=> '',
				'section'	=> 'email_piping',
				'priority'	 => 30,
				'value' => fep_get_option('ep_clean_reply', 1 ),
				'label' => __( 'Clean reply quote', 'front-end-pm' ),
				'cb_label' => __( 'Clean reply quote from email?', 'front-end-pm' )
				);

			return $fields;

		}
		
		function add_reply_to( $content ){

			$content['headers']['reply-to'] = 'Reply-To: ' . fep_get_option('ep_email', get_bloginfo('admin_email'));
			return $content;
		}

		function filter_before_email_send( $message, $mgs, $to ){
			if( empty( $message['subject'] ) || empty( $message['message'] ) )
				return $message;

			$parent_id = fep_get_parent_id( $mgs->mgs_id );

			$key = fep_get_meta( $parent_id, '_fep_message_key', true );

			if( ! $key ) {
				global $wpdb;
				do{
					$key = $this->generate_key();
					$message_id = $wpdb->get_var( $wpdb->prepare( "SELECT fep_message_id FROM $wpdb->fep_messagemeta WHERE meta_key = '_fep_message_key' AND meta_value = %s LIMIT 1", $key ) );

				} while( $message_id );

				fep_update_meta( $parent_id, '_fep_message_key', $key );
			}

			if( is_multisite() ) {
				$key .= '-' . get_current_blog_id();
			}
			
			if ( ! apply_filters( 'fep_email_verify_sender', true ) ) {
				$key .= '-' . fep_get_userdata( $to, 'ID', 'email' );
			}
			
			$identifier = apply_filters( 'fep_email_piping_code_identifier', array( '[MESSAGE KEY-', ']' ) );

			if( 'body' == apply_filters( 'fep_email_piping_code_location', 'subject' ) ){				
				$message['message'] = $message['message'] . ( 'html' == fep_get_option( 'email_content_type', 'plain_text' ) ? "<br />" : "\n" ) . $identifier[0] . $key . $identifier[1];
			} else {
				$message['subject'] = $message['subject'] . ' ' . $identifier[0] . $key . $identifier[1];
			}

			return $message;
		}
		
		function generate_key( $length = 12 ){
			
			$alpha_numeric = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
			$max = strlen( $alpha_numeric );
			$key = '';
			
			for ( $i = 0; $i < $length; $i++ ) {
				$key .= $alpha_numeric[ wp_rand(0, $max-1) ];
			}
			return apply_filters( 'fep_filter_generate_key', $key, $length );
		}

  } //END CLASS

add_action('init', array(Fep_Email_Piping::init(), 'actions_filters'));
