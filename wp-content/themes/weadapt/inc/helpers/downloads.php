<?php

/**
 * The event handler File Download
 */
function action_file_download() {
	if ( isset( $_POST['file_id'] ) ) {
		$file_id        = (int) sanitize_text_field( $_POST['file_id'] );
		$user_ip        = get_user_ip();
		$download_ip    = get_cleaned_ip_downloaded( $file_id );
		$download_count = (int) get_post_meta( $file_id, '_download_count', true );

		if ( ! array_key_exists( $user_ip, $download_ip ) ) {
			$download_ip[$user_ip] = time();

			++$download_count;
		}

		if ( $download_count < 0 ) {
			$download_count = 1;
		}

		update_post_meta( $file_id, '_download_ip', $download_ip );
		update_post_meta( $file_id, '_download_count', $download_count );

		do_action( 'theme_download', $file_id );

		echo json_encode( array( 'status' => 'success', 'download_count' => get_download_count( $file_id ) ) );
	} else {
		echo json_encode( array( 'status' => 'error', 'msg' => esc_html__( 'ID is not valid', 'weadapt' ) ) );
	}
	exit;
}

add_action( 'wp_ajax_nopriv_file_download', 'action_file_download' );
add_action( 'wp_ajax_file_download', 'action_file_download' );


/**
 * Get an array of cleaned IP downloads post.
 *
 * @param  int $file_id
 * @return array
 */
function get_cleaned_ip_downloaded( $file_id ){
	$download_ip = get_post_meta( $file_id, '_download_ip', true );

	if ( ! empty( $download_ip ) ) {
		$cleaned_IP = array();
		$now        = time();

		foreach( $download_ip as $ip => $time){
			if( round( ( $time - $now ) ) < (180*24*3600) ){
				$cleaned_IP[$ip] = $time;
			}
		}

		return $cleaned_IP;
	}

	return array();
}


/**
 * Get the number of downloads.
 *
 * @param  int $file_id The file id.
 * @return int The number of downloads.
 */
function get_download_count( $file_id ) {
    $download_count = (int) get_post_meta( $file_id, '_download_count', true );
    if ( $download_count === 0 ) {
        return sprintf( _n( ' Download', 'Downloads', 'weadapt' ), 0 );
    }
    $download_count = normal_number( $download_count );
    return sprintf( _n( '%s download', '%s Downloads', $download_count, 'weadapt' ), $download_count );
}