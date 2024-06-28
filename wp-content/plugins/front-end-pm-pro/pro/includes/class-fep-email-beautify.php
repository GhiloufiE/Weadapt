<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Fep_Email_Beautify
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
			add_action( 'fep_pro_plugin_update', array($this, 'email_beautify_activate' ));
			add_action( 'fep_plugin_manual_update', array($this, 'manual_update' ) );
			add_filter( 'fep_admin_settings_tabs', array($this, 'admin_settings_tabs' ) );
			add_filter( 'fep_settings_fields', array($this, 'settings_fields' ) );
			add_action( 'wp_loaded', array( $this, 'email_hook' ), 12 );
			add_action( 'fep_email_interval_event', array($this, 'email_interval_event_callback' ) );
			
			add_filter( 'fep_admin_table_columns', array( $this, 'announcement_columns_head' ), 10, 2 );
			add_filter( 'fep_admin_table_column_content_email_pending', array( $this, 'announcement_columns_content' ), 10, 2 );
    	}
	
	function email_beautify_activate(){
	
		if(  false !== fep_get_option( 'plugin_pro_version', false )  )
			return;
			
		$options = array();
	
		$options['eb_newmessage_subject'] = '{{site_title}} - New message';
		$options['eb_newmessage_content'] = '<p>Hi {{receiver}},<br />You have received a new message in {{site_title}}.<br />Subject: {{subject}}<br />Message: {{message}}<br />Message URL: <a href="{{message_url}}">{{message_url}}</a><br /><a href="{{site_url}}">{{site_title}}</a></p>';
		$options['eb_reply_subject'] = '{{site_title}} - New reply';
		$options['eb_reply_content'] = '<p>Hi {{receiver}},<br />You have received a new reply of your message in {{site_title}}.<br />Subject: {{subject}}<br />Message: {{message}}<br />Message URL: <a href="{{message_url}}">{{message_url}}</a><br /><a href="{{site_url}}">{{site_title}}</a></p>';
		$options['eb_announcement_subject'] = '{{site_title}} - Announcement';
		$options['eb_announcement_content'] = '<p>Hi {{receiver}},<br />A new announcement is published in {{site_title}}.<br />Title: {{subject}}<br />Announcement: {{message}}<br />Announcement URL: <a href="{{announcement_url}}">{{announcement_url}}</a><br /><a href="{{site_url}}">{{site_title}}</a></p>';
		$options['email_content_type'] = 'html';
			
		fep_update_option( $options );
		wp_schedule_event( time(), 'fep_15_min', 'fep_email_interval_event' );
		
	}
	
	function manual_update( $prev_ver ) {
		if ( version_compare( $prev_ver, '10.1.1', '<' ) && ! fep_get_option( 'v1011-part-2', 0, 'fep_updated_versions' ) ) {
			$queue     = get_option( 'fep_announcement_email_queue' );
			$new_queue = [];
			
			if ( $queue && is_array( $queue ) ) {
				foreach ( $queue as $k => $v ) {
					if ( ! $v || ! is_array( $v ) ) {
						continue;
					}
					$id = str_replace( 'id_', '', $k );
					
					if ( ! $id || ! is_numeric( $id ) ) {
						continue;
					}
					$mgs_id = get_post_meta( $id, '_fep_new_id', true );
					if ( ! $mgs_id || ! is_numeric( $mgs_id ) ) {
						continue;
					}
					foreach ( $v as $x => $y ) {
						if ( $y && ( $user_id = fep_get_userdata( $y, 'ID', 'email' ) ) ) {
							$new_queue[ 'id_' . $mgs_id ][] = $user_id;
						}
					}
				}
			}
			update_option( 'fep_announcement_email_queue', $new_queue, 'no' );
			fep_update_option( 'v1011-part-2', 1, 'fep_updated_versions' );
			$response = array(
				'update'     => 'continue',
				'message'    => __( 'Announcement queue updated.', 'front-end-pm' ),
				'custom_int' => 0,
				'custom_str' => '',
			);
			wp_send_json( $response );
		}
	}
	
	function email_legends( $where = 'newmessage', $mgs = '', $value = 'description', $user_email = '' ){
		
		$autop = false;
		if( 'html' == fep_get_option( 'email_content_type', 'plain_text' ) && apply_filters( 'fep_email_wpautop', true ) ) {
			$autop = true;
		}
		$content = ! empty( $mgs->mgs_content ) ? $mgs->mgs_content : '';
		$thread_id = 0;
		if ( is_object( $mgs ) ) {
			if ( 'threaded' === fep_get_message_view() && $mgs->mgs_parent ) {
				$thread_id = $mgs->mgs_parent;
			} else {
				$thread_id = $mgs->mgs_id;
			}
		}
		
		$legends = array(
			'subject' => array(
				'description' => __('Subject', 'front-end-pm'),
				'where' => array( 'newmessage', 'reply', 'announcement' ),
				'replace_with' => ! empty( $mgs->mgs_title ) ? $mgs->mgs_title : '',
				),
			'message' => array(
				'description' => __('Full Message', 'front-end-pm'),
				'where' => array( 'newmessage', 'reply', 'announcement' ),
				'replace_with' => $autop ? wpautop( $content ) : $content,
				),
			'message_url' => array(
				'description' => __('URL of message', 'front-end-pm'),
				'where' => array( 'newmessage', 'reply' ),
				'replace_with' => fep_query_url_raw( 'viewmessage', array( 'fep_id' => $thread_id ) ),
				),
			'announcement_url' => array(
				'description' => __('URL of announcement', 'front-end-pm'),
				'where' => 'announcement',
				'replace_with' => ! empty( $mgs->mgs_id ) ? fep_query_url_raw( 'view_announcement', array( 'fep_id' => $mgs->mgs_id ) ) : '',
				),
			'sender' => array(
				'description' => __('Sender Name', 'front-end-pm'),
				'where' => array( 'newmessage', 'reply', 'announcement' ),
				'replace_with' => ! empty( $mgs->mgs_author ) ? fep_user_name( $mgs->mgs_author ) : '',
				),
			'receiver' => array(
				'description' => __('Receiver Name', 'front-end-pm'),
				'where' => array( 'newmessage', 'reply', 'announcement' ),
				'replace_with' => fep_user_name( fep_get_userdata( $user_email, 'ID', 'email' ) )
				),
			'site_title' => array(
				'description' => __('Website title', 'front-end-pm'),
				'where' => array( 'newmessage', 'reply', 'announcement' ),
				'replace_with' => wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES )
				),
			'site_url' => array(
				'description' => __('Website URL', 'front-end-pm'),
				'where' => array( 'newmessage', 'reply', 'announcement' ),
				'replace_with' => get_bloginfo('url')
				),
			);
		$legends = apply_filters( 'fep_eb_email_legends', $legends, $mgs, $user_email );
		
		$ret = array();
		foreach( $legends as $k => $legend ) {
		
				if ( empty($legend['where']) )
					$legend['where'] = array( 'newmessage', 'reply', 'announcement' );
				
				if( is_array($legend['where'])){
					if ( ! in_array(  $where, $legend['where'] )){
						continue;
					}
				} else {
					if ( $where != $legend['where'] ){
						continue;
					}
				}
				if( 'description' == $value ) {
					$ret[$k] = '<code>{{' . $k . '}}</code> = ' . $legend['description'];
				} else {
					$ret['{{' . $k . '}}'] = $legend['replace_with'];
				}
		}
		return $ret;
	}
	
	function admin_settings_tabs( $tabs ) {
				
		$tabs['eb_newmessage'] =  array(
				'section_title'			=> __('New Message email', 'front-end-pm'),
				'section_page'		=> 'fep_settings_emails',
				'priority'			=> 55,
				'tab_output'		=> false
				);
		$tabs['eb_reply'] =  array(
				'section_title'			=> __('Reply Message email', 'front-end-pm'),
				'section_page'		=> 'fep_settings_emails',
				'priority'			=> 65,
				'tab_output'		=> false
				);
		$tabs['eb_announcement'] =  array(
				'section_title'			=> __('Announcement email', 'front-end-pm'),
				'section_page'		=> 'fep_settings_emails',
				'priority'			=> 75,
				'tab_output'		=> false
				);
				
		return $tabs;
	}
	
	function settings_fields( $fields )
		{
			$templates = array(
				'default'	=> __( 'Default', 'front-end-pm' ),
				);
			
			$fields['email_interval'] =   array(
				'type' => 'select',
				'section'	=> 'emails',
				'priority'	=> 16,
				'value' => fep_get_option('email_interval', 'fep_15_min' ),
				'label' => __( 'Email Sending Interval.', 'front-end-pm' ),
				'description' => __( 'Email sending Interval.', 'front-end-pm' ),
				'options'	=> array_map( function($e){return $e['display'];}, wp_get_schedules() ),
				);
			$fields['email_per_interval'] =   array(
				'type' => 'number',
				'section'	=> 'emails',
				'priority'	=> 16,
				'value' => fep_get_option('email_per_interval', 50 ),
				'label' => __( 'Emails send per interval.', 'front-end-pm' ),
				'description' => __( 'Emails send per interval.', 'front-end-pm' )
				);
			$fields['eb_newmessage_template'] =   array(
				'section'	=> 'eb_newmessage',
				'value' => fep_get_option('eb_newmessage_template', 'default'),
				'label' => __( 'New message email template', 'front-end-pm' ),
				'type'	=>	'select',
				//'description' => __( 'Admin alwayes have Wp Editor.', 'front-end-pm' ),
				'options'	=> apply_filters( 'fep_eb_templates', $templates, 'newmessage' ),
				);
			$fields['eb_newmessage_subject'] =   array(
				'section'	=> 'eb_newmessage',
				'value' => fep_get_option('eb_newmessage_subject', ''),
				'label' => __( 'New message subject.', 'front-end-pm' )
				);
			$fields['eb_newmessage_content'] =   array(
				'type' => 'teeny',
				'section'	=> 'eb_newmessage',
				'value' => fep_get_option('eb_newmessage_content', ''),
				'description' => implode( '<br />', $this->email_legends() ),
				'label' => __( 'New message content.', 'front-end-pm' )
				);
			$fields['eb_newmessage_attachment'] =   array(
				'type'	=>	'checkbox',
				'class'	=> '',
				'section'	=> 'eb_newmessage',
				'value' => fep_get_option('eb_newmessage_attachment', 0 ),
				'label' => __( 'Send Attachments', 'front-end-pm' ),
				'cb_label' => __( 'Send attachments with new message email?', 'front-end-pm' )
				);
			$fields['eb_reply_template'] =   array(
				'section'	=> 'eb_reply',
				'value' => fep_get_option('eb_reply_template', 'default'),
				'label' => __( 'Reply message email template', 'front-end-pm' ),
				'type'	=>	'select',
				//'description' => __( 'Admin alwayes have Wp Editor.', 'front-end-pm' ),
				'options'	=> apply_filters( 'fep_eb_templates', $templates, 'reply' ),
				);
			$fields['eb_reply_subject'] =   array(
				'section'	=> 'eb_reply',
				'value' => fep_get_option('eb_reply_subject', ''),
				'label' => __( 'Reply subject.', 'front-end-pm' )
				);
			$fields['eb_reply_content'] =   array(
				'type' => 'teeny',
				'section'	=> 'eb_reply',
				'value' => fep_get_option('eb_reply_content', ''),
				'description' => implode( '<br />', $this->email_legends( 'reply' ) ),
				'label' => __( 'Reply content.', 'front-end-pm' )
				);
			$fields['eb_reply_attachment'] =   array(
				'type'	=>	'checkbox',
				'class'	=> '',
				'section'	=> 'eb_reply',
				'value' => fep_get_option('eb_reply_attachment', 0 ),
				'label' => __( 'Send Attachments', 'front-end-pm' ),
				'cb_label' => __( 'Send attachments with reply message email?', 'front-end-pm' )
				);
			$fields['eb_announcement_template'] =   array(
				'section'	=> 'eb_announcement',
				'value' => fep_get_option('eb_announcement_template', 'default'),
				'label' => __( 'Announcement email template', 'front-end-pm' ),
				'type'	=>	'select',
				//'description' => __( 'Admin alwayes have Wp Editor.', 'front-end-pm' ),
				'options'	=> apply_filters( 'fep_eb_templates', $templates, 'announcement' ),
				);
			$fields['eb_announcement_subject'] =   array(
				'section'	=> 'eb_announcement',
				'value' => fep_get_option('eb_announcement_subject', ''),
				'label' => __( 'Announcement subject.', 'front-end-pm' )
				);
			$fields['eb_announcement_content'] =   array(
				'type' => 'teeny',
				'section'	=> 'eb_announcement',
				'value' => fep_get_option('eb_announcement_content', ''),
				'description' => implode( '<br />', $this->email_legends( 'announcement' ) ),
				'label' => __( 'Announcement content.', 'front-end-pm' )
				);
			$fields['eb_announcement_attachment'] =   array(
				'type'	=>	'checkbox',
				'class'	=> '',
				'section'	=> 'eb_announcement',
				'value' => fep_get_option('eb_announcement_attachment', 0 ),
				'label' => __( 'Send Attachments', 'front-end-pm' ),
				'cb_label' => __( 'Send attachments with announcement email?', 'front-end-pm' )
				);
				
			unset($fields['ann_to']);
								
			return $fields;
			
		}

	function filter_before_email_send( $content, $mgs, $user_email = '' ){
		
		$autop = false;
		$html = ( 'html' == fep_get_option( 'email_content_type', 'plain_text' ) ) ? true : false;
		
		if( $html && apply_filters( 'fep_email_wpautop', true ) ) {
			$autop = true;
		}
		
		if( 'announcement' === $mgs->mgs_type ) {
			$legends = $this->email_legends( 'announcement', $mgs, 'replace_with', $user_email );
			$subject = stripslashes( fep_get_option('eb_announcement_subject', '') );
			$message = stripslashes( fep_get_option('eb_announcement_content', '') );
			
			if( $autop ){
				$message = wpautop( $message );
			}
			$content['subject'] = str_replace( array_keys($legends), $legends, $subject );
			
			$template_slug = fep_get_option('eb_announcement_template', 'default');
			$template_name = "emails/{$template_slug}.php";
			
			if( $template_slug && has_filter( "fep_filter_announcement_email_template_{$template_slug}") ){
				$message = apply_filters( "fep_filter_announcement_email_template_{$template_slug}", $message, $mgs, $user_email );
				
			} elseif( $template_slug && $html && $template = fep_locate_template( $template_name ) ){
				ob_start();
				require $template;
				$body = ob_get_clean();
				
				$message = str_replace( '{FEP-EMAIL-CONTENT}', $message, $body );
			}
			$content['message'] = str_replace( array_keys($legends), $legends, $message );
			
			if( fep_get_option('eb_announcement_attachment', 0 ) && $attachments = $mgs->get_attachments() ){
				foreach( $attachments as $attachment ){
					if( $file = Fep_Attachment::init()->absulate_path( $attachment->att_file ) ){
						$content['attachments'][] = $file;
					}
				}
			}
		} elseif( $mgs->mgs_parent ){
			$legends = $this->email_legends( 'reply', $mgs, 'replace_with', $user_email );
			$subject = stripslashes( fep_get_option('eb_reply_subject', '') );
			$message = stripslashes( fep_get_option('eb_reply_content', '') );
			
			if( $autop ){
				$message = wpautop( $message );
			}
			$content['subject'] = str_replace( array_keys($legends), $legends, $subject );
			
			$template_slug = fep_get_option('eb_reply_template', 'default');
			$template_name = "emails/{$template_slug}.php";
			
			if( $template_slug && has_filter( "fep_filter_reply_email_template_{$template_slug}") ){
				$message = apply_filters( "fep_filter_reply_email_template_{$template_slug}", $message, $mgs, $user_email );
				
			} elseif( $template_slug && $html && $template = fep_locate_template( $template_name ) ){
				ob_start();
				require( $template );
				$body = ob_get_clean();
				
				$message = str_replace( '{FEP-EMAIL-CONTENT}', $message, $body );
			}
			$content['message'] = str_replace( array_keys($legends), $legends, $message );
			
			if( fep_get_option('eb_reply_attachment', 0 ) && $attachments = $mgs->get_attachments() ){
				foreach( $attachments as $attachment ){
					if( $file = Fep_Attachment::init()->absulate_path( $attachment->att_file ) ){
						$content['attachments'][] = $file;
					}
				}
			}
			
		} else {
			$legends = $this->email_legends( 'newmessage', $mgs, 'replace_with', $user_email );
			
			$subject = stripslashes( fep_get_option('eb_newmessage_subject', '') );
			$message = stripslashes( fep_get_option('eb_newmessage_content', '') );
			
			if( $autop ){
				$message = wpautop( $message );
			}
			$content['subject'] = str_replace( array_keys($legends), $legends, $subject );
			
			$template_slug = fep_get_option('eb_newmessage_template', 'default');
			$template_name = "emails/{$template_slug}.php";
			
			if( $template_slug && has_filter( "fep_filter_newmessage_email_template_{$template_slug}") ){
				$message = apply_filters( "fep_filter_newmessage_email_template_{$template_slug}", $message, $mgs, $user_email );
				
			} elseif( $template_slug && $html && $template = fep_locate_template( $template_name ) ){
				ob_start();
				require( $template );
				$body = ob_get_clean();
				
				$message = str_replace( '{FEP-EMAIL-CONTENT}', $message, $body );
			}
			$content['message'] = str_replace( array_keys($legends), $legends, $message );
			
			if( fep_get_option('eb_newmessage_attachment', 0 ) && $attachments = $mgs->get_attachments() ){
				foreach( $attachments as $attachment ){
					if( $file = Fep_Attachment::init()->absulate_path( $attachment->att_file ) ){
						$content['attachments'][] = $file;
					}
				}
			}
		}

		return $content;
	}
	
	function email_hook(){
		if ( true != apply_filters( 'fep_enable_email_send', true ) ) {
			return;
		}
		remove_action( 'fep_status_to_publish', array( Fep_Emails::init(), 'send_email' ), 99, 2 );
		add_action( 'fep_message_status_to_publish', array( $this, 'send_email' ), 99, 2 );
		
		if ( '1' == fep_get_option( 'notify_ann', '1' ) ) {
			remove_action( 'fep_status_to_publish', array( Fep_Emails::init(), 'notify_users' ), 99, 2 );
			add_action( 'fep_status_to_publish', array( $this, 'notify_users' ), 99, 2 );
		}
	}
	
	function send_email( $mgs, $prev_status ) {
		if ( fep_get_meta( $mgs->mgs_id, '_fep_email_sent', true ) ) {
			return;
		}

		$participants = fep_get_participants( $mgs->mgs_id );
		$participants = apply_filters( 'fep_filter_send_email_participants', $participants, $mgs->mgs_id );
		$participants = array_unique( array_filter( $participants ) );
		if ( ! $participants ) {
			return;
		}
		// post_author also included in participants, so use -1
		if ( ( count( $participants ) - 1 ) > apply_filters( 'fep_filter_email_count_require_queue', 5 ) ) {
			$queue = get_option( 'fep_message_email_queue' );
			
			if( ! is_array( $queue ) ) {
				$queue = array();
			}
		
			$queue['id_'. $mgs->mgs_id] = $participants;
			
			update_option( 'fep_message_email_queue', $queue, 'no' );
		} else {
			fep_add_email_filters();
			if( 'html' == fep_get_option( 'email_content_type', 'plain_text' ) ) {
				$content_type = 'text/html';
			} else {
				$content_type = 'text/plain';
			}
			$empty_content = array(
				'subject' => '',
				'message' => '',
				'attachments' => array()
				);
			$headers = array();
			$headers['from'] = 'From: ' . stripslashes( fep_get_option('from_name', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) ) ) . ' <' . fep_get_option( 'from_email', get_bloginfo( 'admin_email' ) ) . '>';
			$headers['content_type'] = "Content-Type: $content_type";
			
			cache_users( $participants );

			foreach ( $participants as $participant ) {
				if ( $participant == $mgs->mgs_author ) {
					continue;
				}

				if ( ! fep_get_user_option( 'allow_emails', 1, $participant ) ) {
					continue;
				}
				$user_email = fep_get_userdata( $participant, 'user_email', 'id' );
				if ( ! $user_email ) {
					continue;
				}
				$content = $this->filter_before_email_send( $empty_content, $mgs, $user_email );
				$content['headers'] = $headers;
				
				$content = apply_filters( 'fep_filter_before_email_send', $content, $mgs, $user_email );

				if ( empty( $content['subject'] ) || empty( $content['message'] ) ) {
					continue;
				}
				wp_mail( $user_email, $content['subject'], $content['message'], $content['headers'], $content['attachments'] );
			} //End foreach
			fep_remove_email_filters();
		}
		fep_update_meta( $mgs->mgs_id, '_fep_email_sent', time() );
	}
	
	function notify_users( $mgs, $prev_status ) {
		if ( 'announcement' != $mgs->mgs_type ) {
			return;
		}
		if ( fep_get_meta( $mgs->mgs_id, '_fep_email_sent', true ) ) {
			return;
		}

		$user_ids = fep_get_participants( $mgs->mgs_id );
		if ( ! $user_ids ) {
			return;
		}
		$queue = get_option( 'fep_announcement_email_queue' );
		
		if( ! is_array( $queue ) ) {
			$queue = array();
		}
	
		$queue['id_'. $mgs->mgs_id] = $user_ids;
		
		update_option( 'fep_announcement_email_queue', $queue, 'no' );
		fep_update_meta( $mgs->mgs_id, '_fep_email_sent', time() );
	}

	function email_interval_event_callback(){
		$per_interval = (int) fep_get_option( 'email_per_interval', 50 );
		$count        = 0;
		
		foreach ( [ 'message', 'announcement' ] as $type ) {
			if( $per_interval <= $count )
				break;
	
			$queue = get_option( "fep_{$type}_email_queue" );
			
			if( ! $queue || ! is_array( $queue ) )
				continue;
			
			if ( ! fep_is_func_disabled( 'set_time_limit' ) )
			set_time_limit( 0 );
				
			
			fep_add_email_filters( $type );
			if( 'html' == fep_get_option( 'email_content_type', 'plain_text' ) ) {
				$content_type = 'text/html';
			} else {
				$content_type = 'text/plain';
			}
			$empty_content = array(
				'subject' => '',
				'message' => '',
				'attachments' => array()
				);
			$headers = array();
			$headers['from'] = 'From: '.stripslashes( fep_get_option('from_name', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) ) ).' <'. fep_get_option('from_email', get_bloginfo('admin_email')) .'>';
			$headers['content_type'] = "Content-Type: $content_type";
				
			foreach( $queue as $k => $v ) {
				if( ! $v || ! is_array( $v ) ) {
					unset( $queue[$k] );
					continue;
				}
				$id = str_replace( 'id_', '', $k );
				
				if( ! $id || ! is_numeric( $id ) ) {
					unset( $queue[$k] );
					continue;
				}
				
				$mgs = fep_get_message( $id );
				
				if( ! $mgs || $type != $mgs->mgs_type || 'publish' != $mgs->mgs_status ) {
					unset( $queue[$k] );
					continue;
				}
				cache_users( array_slice( $v, 0, $per_interval - $count ) );
				
				foreach( $v as $x => $y ) {
					if( $per_interval <= $count )
						break 2;
					
					if( $mgs->mgs_author == $y && !apply_filters( 'fep_filter_email_to_sender', false, $mgs ) ){
						unset( $queue[$k][$x] );
						continue;
					}
					$user_email = fep_get_userdata( $y, 'user_email', 'id' );
					
					if( ! $user_email ){
						unset( $queue[$k][$x] );
						continue;
					}
					
					if( 'announcement' == $type && ! fep_get_user_option( 'allow_ann', 1, $y ) ){
						unset( $queue[$k][$x] );
						continue;
					} elseif ( 'message' == $type && ! fep_get_user_option( 'allow_emails', 1, $y ) ) {
						unset( $queue[$k][$x] );
						continue;
					}
					
					$content = $this->filter_before_email_send( $empty_content, $mgs, $user_email );
					$content['headers'] = $headers;
					
					if( 'announcement' == $type ){
						$content = apply_filters( 'fep_filter_before_announcement_email_send', $content, $mgs, array( $user_email ) );
					} elseif ( 'message' == $type ) {
						$content = apply_filters( 'fep_filter_before_email_send', $content, $mgs, $user_email );
					}
			
					if( empty( $content['subject'] ) || empty( $content['message'] ) ){
						unset( $queue[$k][$x] );
						continue;
					}
					
					if( wp_mail( $user_email, $content['subject'], $content['message'], $content['headers'], $content['attachments'] ) ) {
						unset( $queue[$k][$x] );
						$count++;
					}
					if( $count && ( $count % 10 == 0 ) ){
						//Save in every 10th emails for timeout issue
						update_option( "fep_{$type}_email_queue", $queue, 'no' );
					}
				}
			}
			
			fep_remove_email_filters( $type );
			
			update_option( "fep_{$type}_email_queue", $queue, 'no' );
		}	
	}
	
	function announcement_columns_head( $columns, $mgs_type ) {
		if( 'announcement' === $mgs_type ){
			$columns['email_pending'] = __('Email Pending', 'front-end-pm');
		}
		return $columns;
	}

	function announcement_columns_content( $return, $mgs ) {
		
		$queue = get_option( 'fep_announcement_email_queue' );
		
		if( is_array( $queue ) && ! empty( $queue['id_'. $mgs->mgs_id] ) ) {
			$email_pending = count( $queue['id_'. $mgs->mgs_id] );
		} else {
			$email_pending = 0;
		}
		return $email_pending;
	}
  } //END CLASS

add_action('init', array(Fep_Email_Beautify::init(), 'actions_filters'));

