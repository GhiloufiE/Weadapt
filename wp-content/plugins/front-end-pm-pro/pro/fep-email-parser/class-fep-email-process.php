<?php

class FEP_Email_Process {
	public $ep;
	public $parent_id = 0;
	public $sender_id = 0;
	public $inserted_id = 0;



    public function __construct( $ep ){

		$this->ep 		= $ep;

		$this->message_key 	= $this->ep->message_key();
		$this->blog_id 		= $this->ep->blog_id();
		$this->sender_email 	= $this->ep->sender_email();
		$this->subject 		= $this->ep->subject();
		$this->parent_id 		= $this->parent_id();
		
		if ( ! $this->parent_id ) {
			return false;
		}
		
		if ( ! apply_filters( 'fep_email_process_continue', true, $this->parent_id, $this ) ) {
			return false;
		}

		$this->multisite_switch();
		
		$this->sender_id = $this->sender_id();

		if ( ! $this->sender_id ) {
			$this->multisite_restore();
			return false;
		}

		$current_user_id = get_current_user_id();
		
		wp_set_current_user( $this->sender_id );

		if ( $this->check() ) {
			add_action( 'fep_action_message_after_send', array( $this, 'upload_attachments' ), 10, 3 );
			$this->send_message();
			remove_action( 'fep_action_message_after_send', array( $this, 'upload_attachments' ), 10, 3 );
		}
		wp_set_current_user( $current_user_id );
		$this->multisite_restore();
    }

	function multisite_switch() {
		if ( is_multisite() && $this->blog_id ) {
			$b_id = absint( $this->blog_id );
			add_action( 'switch_blog', array( $this, 'switch_to_blog_cache_clear' ), 10, 2 );
			switch_to_blog( $b_id );
		}
	}
	function multisite_restore() {
		if ( is_multisite() && $this->blog_id ) {
			restore_current_blog();
		}
	}
	function switch_to_blog_cache_clear( $blog_id, $prev_blog_id ) {
		if ( $blog_id === $prev_blog_id )
			return false;

		wp_cache_delete( 'notoptions', 'options' );
		wp_cache_delete( 'alloptions', 'options' );
	}
	
	function parent_id() {
		global $wpdb;
		if ( ! $this->message_key || empty( $wpdb->fep_messagemeta ) ) {
			return 0;
		}
		return (int) $wpdb->get_var( $wpdb->prepare( "SELECT fep_message_id FROM $wpdb->fep_messagemeta WHERE meta_key = '_fep_message_key' AND meta_value = %s LIMIT 1", $this->message_key ) );
	}
	
	function sender_id() {
		if ( apply_filters( 'fep_email_verify_sender', true ) ) {
			return fep_get_userdata( $this->sender_email, 'ID', 'email' );
		} else {
			$sender_id = $this->ep->sender_id();
			if ( $sender_id && in_array( $sender_id, fep_get_participants( $this->parent_id, true ) ) ) {
				return $sender_id;
			} else {
				return fep_get_userdata( $this->sender_email, 'ID', 'email' );
			}
		}
	}
	
	function check(){

		if( ! function_exists( 'fep_get_option' ) ) {
			return false;
		}

		if ( ! fep_current_user_can( 'send_reply', $this->parent_id ) ){
			return false;
		}
		return true;
	}

	function send_message(){
		if( fep_get_option('ep_clean_reply', 1 ) ) {
			$body = $this->ep->clean_body();
		} else {
			$body = $this->ep->body();
		}

		$message = array(
			'fep_parent_id' => $this->parent_id,
			'message_content' => $body,
		);
		$override = [];
		if ( $this->ep->date() && strtotime( $this->ep->date() ) < time() && strtotime( $this->ep->date() ) > ( time() - DAY_IN_SECONDS ) ) {
			$override['mgs_created'] = $this->ep->date();
		}

		$this->inserted_id = fep_send_message( $message, $override );
	}

	function upload_attachments( $message_id, $message, $inserted_message ){
		if ( ! fep_get_option( 'allow_attachment', 1 ) || ! $message_id )
			return false;

		$attachments = $this->ep->attachments();

		if( ! $attachments )
			return false;

		$size_limit = (int) wp_convert_hr_to_bytes(fep_get_option('attachment_size','4MB'));
		$fields = (int) fep_get_option('attachment_no', 4);

		if( class_exists( 'Fep_Attachment' ) ){
			add_filter('upload_dir', array(Fep_Attachment::init(), 'upload_dir'), 99 );
		}

		$i = 0;
		$attachments_array = array();
		foreach( $attachments as $k => $contents ) {

			$name = isset( $contents['name'] ) ? $contents['name'] : '';
			$mime = isset( $contents['mime'] ) ? $contents['mime'] : '';
			$content = isset( $contents['content'] ) ? $contents['content'] : '';

			if( !$name || !$mime || !in_array( $mime, get_allowed_mime_types() ) )
				continue;

			$size = strlen( $content );
			if( $size > $size_limit )
				continue;

			$att = wp_upload_bits( $name, null, $content );

			if( ! isset( $att['file'] ) || ! isset( $att['url'] ) || ! isset( $att['type'] ) )
				continue;
			
			$attachments_array[] = array(
				'att_mime' => $att['type'],
				'att_file' => _wp_relative_upload_path( $att['file'] ),
			);

			if( ++$i >= $fields )
				break;
		}
		if( $attachments_array ){
			$inserted_message->insert_attachments( $attachments_array );
		}
		
		if( class_exists( 'Fep_Attachment' ) ){
			remove_filter( 'upload_dir', array( Fep_Attachment::init(), 'upload_dir' ), 99 );
		}
	}

}
