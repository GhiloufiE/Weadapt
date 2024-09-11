<?php

/**
 * The event handler Post Like
 */
function action_post_like() {
	$nonce = sanitize_text_field( $_POST['nonce'] );

	if ( ! wp_verify_nonce( $nonce, 'like' ) ){
		die ( __( 'Busted!', 'weadapt' ) );
	}

	if ( isset( $_POST['post_id'] ) ) {
		$post_id = (int) sanitize_text_field( $_POST['post_id'] );

		$user_ip = get_user_ip();

		$like_IP = get_cleaned_ip_liked( $post_id );
		$like_count = (int) get_post_meta( $post_id, '_like_count', true );

		$like_IP[$user_ip] = time();

		if( isset( $_POST['event'] ) ){
			if( $_POST['event'] == '+' ){
				++$like_count;

				if ( $like_count < 0 ) {
					$like_count = 1;
				}

				$message = esc_html__( 'Thank you!', 'weadapt' );
			} elseif( $_POST['event'] == '-' ) {
				--$like_count;

				if ( $like_count < 0 ) {
					$like_count = 0;
				}

				$message = esc_html__( 'Thank you!', 'weadapt' );
			}
		}

		update_post_meta( $post_id, '_like_IP', $like_IP );
		update_post_meta( $post_id, '_like_count', $like_count );

		do_action( 'pgcache_flush_post', $post_id );

		echo json_encode( array( 'status' => 'success', 'like_count' => sprintf( _n( '%s Like', '%s Likes', $like_count, 'weadapt' ), $like_count ), 'msg' => $message ) );
	} else {
		echo json_encode( array( 'status' => 'error', 'msg' => esc_html__( 'ID is not valid', 'weadapt' ) ) );
	}
	exit;
}

add_action( 'wp_ajax_nopriv_post_like', 'action_post_like' );
add_action( 'wp_ajax_post_like', 'action_post_like' );


/**
 * The event handler Comment Like
 */
function action_comment_like() {
	if ( isset( $_POST['comment_id'] ) ) {
		$comment_id = (int) sanitize_text_field( $_POST['comment_id'] );

		$user_ip = get_user_ip();

		$like_IP = get_cleaned_ip_liked( $comment_id );
		$like_count = (int) get_comment_meta( $comment_id, '_like_count', true );

		$like_IP[$user_ip] = time();

		if( isset( $_POST['event'] ) ){
			if( $_POST['event'] == '+' ){
				++$like_count;

				if ( $like_count < 0 ) {
					$like_count = 1;
				}

				$message = esc_html__( 'Thank you!', 'weadapt' );
			} elseif( $_POST['event'] == '-' ) {
				--$like_count;

				if ( $like_count < 0 ) {
					$like_count = 0;
				}

				$message = esc_html__( 'Thank you!', 'weadapt' );
			}
		}

		update_comment_meta( $comment_id, '_like_IP', $like_IP );
		update_comment_meta( $comment_id, '_like_count', $like_count );

		echo json_encode( array( 'status' => 'success', 'like_count' => sprintf( _n( '%s Like', '%s Likes', $like_count, 'weadapt' ), $like_count ), 'msg' => $message ) );
	}
	else {
		echo json_encode( array( 'status' => 'error', 'msg' => esc_html__( 'ID is not valid', 'weadapt' ) ) );
	}

	exit;
}
add_action( 'wp_ajax_comment_like', 'action_comment_like' );


/**
 * Get the IP of the user
 */
function is_valid_user_ip( $ip = null ) {
	if( preg_match( "#^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$#", $ip ) ){
		return true;
	}

	return false;
}

function get_user_ip() {
	$ip = false;
	if( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ){
		$ipa[] = trim( strtok( $_SERVER['HTTP_X_FORWARDED_FOR'], ',' ) );
	}

	if( isset($_SERVER['HTTP_CLIENT_IP'] ) ){
		$ipa[] = $_SERVER['HTTP_CLIENT_IP'];
	}

	if( isset( $_SERVER['REMOTE_ADDR'] ) ){
		$ipa[] = $_SERVER['REMOTE_ADDR'];
	}

	if( isset( $_SERVER['HTTP_X_REAL_IP'] ) ){
		$ipa[] = $_SERVER['HTTP_X_REAL_IP'];
	}

	// check the ip addresses for validity since priority
	foreach( $ipa as $ips ){
		if( is_valid_user_ip( $ips ) ){
			$ip = $ips;
			break;
		}
	}
	return $ip;
}


/**
 * Set number to Short Form
 *
 * @param   int         $n number
 * @return (int|string)
 */
function abridged_number( $n ) {

	// First strip any formatting
	$n = ( 0 + str_replace( ',', '', $n ) );

	// Is this a number?
	if( !is_numeric( $n ) ) { return false; }

	// Number Index
	$index = 1000;

	// Filter Number
	if( $n > pow( $index, 4 ) ) {
		return floor( $n / pow( $index, 4 ) * 10 ) / 10 . ' ' . esc_html__( 'T', 'weadapt' );
	}
	elseif( $n > pow( $index, 3 ) ) {
		return floor( $n / pow( $index, 3 ) * 10 ) / 10 . ' ' . esc_html__( 'G', 'weadapt' );
	}
	elseif( $n > pow( $index, 2 ) ) {
		return floor( $n / pow( $index, 2 ) * 10 ) / 10 . ' ' . esc_html__( 'M', 'weadapt' );
	}
	elseif( $n > pow( $index, 1 ) ) {
		return floor( $n / pow( $index, 1 ) * 10 ) / 10 . ' ' . esc_html__( 'k', 'weadapt' );
	}

	return number_format( $n );
}

/**
 * Set number to Normal Form
 *
 * @param   int         $n number
 * @return (int|string)
 */
function normal_number( $n ) {

	// First strip any formatting
	$n = ( 0 + str_replace( ',', '', $n ) );

	// Is this a number?
	if( !is_numeric( $n ) ) { return false; }

	return number_format( $n, 0, '', '');
}


/**
 * Fires once a post has been saved.

 * @param int     $post_ID Post ID.
 * @param WP_Post $post    Post object.
 * @param bool    $update  Whether this is an existing post being updated or not.
 */
function save_post_default_like( $post_ID, $post, $update ) {
	if( ! wp_is_post_revision( $post_ID ) && '' === get_post_meta( $post_ID, '_like_count', true ) ){
		update_post_meta( $post_ID, '_like_count', 0 );
	}
}
add_action( 'save_post', 'save_post_default_like', 10, 3 );


/**
 * Get an array of cleaned IP likes post.
 *
 * @param  int $post_id
 * @return array
 */
function get_cleaned_ip_liked( $post_id ){
	$like_IP = get_post_meta( $post_id, '_like_IP', true );

	if( $like_IP ){

		$cleaned_IP = array();

		$now = time();

		foreach( $like_IP as $ip => $time){
			if( round( ( $time - $now ) ) < (180*24*3600) ){
				$cleaned_IP[$ip] = $time;
			}
		}

		return $cleaned_IP;
	}
}


/**
 * Get the number of likes.
 *
 * @param  int $post_id The post id.
 * @return int The number of likes.
 */
function get_like_count( $post_id ){
    $like_count = (int) get_post_meta( $post_id, '_like_count', true );
    if ($like_count === 0) {
        return 'Likes';
    }

    $like_count = abridged_number( $like_count );

    return sprintf( _n( '%s Like', '%s Likes', $like_count, 'weadapt' ), $like_count );
}

