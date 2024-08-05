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
        $current_month  = date('Y-m');
        $download_count_month = (int) get_post_meta( $file_id, '_download_count_month', true );
        $last_reset_month = get_post_meta( $file_id, '_last_reset_month', true );

        // Reset monthly count if it's a new month
        if ( $last_reset_month !== $current_month ) {
            $download_count_month = 0;
            update_post_meta( $file_id, '_last_reset_month', $current_month );
        }

        if ( ! array_key_exists( $user_ip, $download_ip ) ) {
            $download_ip[$user_ip] = time();

            ++$download_count;
            ++$download_count_month;
        }

        if ( $download_count < 0 ) {
            $download_count = 1;
        }

        update_post_meta( $file_id, '_download_ip', $download_ip );
        update_post_meta( $file_id, '_download_count', $download_count );
        update_post_meta( $file_id, '_download_count_month', $download_count_month );

        do_action( 'theme_download', $file_id );

        echo json_encode( array( 'status' => 'success', 'download_count' => get_download_count( $file_id ), 'download_count_month' => get_download_count_month( $file_id ) ) );
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
        return sprintf( _n( ' view', 'Views', 'weadapt' ), 0 );
    }
    $download_count = normal_number( $download_count );
    return sprintf( _n( '%s view', '%s Views', $download_count, 'weadapt' ), $download_count );
}

/**
 * Get the number of monthly downloads.
 *
 * @param  int $file_id The file id.
 * @return int The number of monthly downloads.
 */
function get_download_count_month( $file_id ) {
    $download_count_month = (int) get_post_meta( $file_id, '_download_count_month', true );
    if ( $download_count_month === 0 ) {
        return sprintf( _n( ' monthly view', 'Monthly Views', 'weadapt' ), 0 );
    }
    $download_count_month = normal_number( $download_count_month );
    return sprintf( _n( '%s monthly view', '%s Monthly Views', $download_count_month, 'weadapt' ), $download_count_month );
}
