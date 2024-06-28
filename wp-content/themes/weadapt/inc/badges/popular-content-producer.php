<?php

/**
 * Badge 6 - Popular Content Producer!
 *
 * Granted to a user whose content has been downloaded more than 50 times on the website (can be taken from the number of downloads of a report).
 *
 * For debuging use: error_log( print_r( $variable, true ) );
 */
if ( ! function_exists( 'badge_popular_content_producer_download' ) ) :

	function badge_popular_content_producer_download( $file_ID ) {

		// Check Badge ID
		if ( empty( $badge_ID = get_badge_id( 'popular-content-producer' ) ) ) return;

		// Check Author ID
		$post    = get_post( $file_ID );
		$user_ID = ! empty( $post->post_author ) ? $post->post_author : 0;

		if ( empty( $user_ID ) ) return;

		$user = new WP_User( $user_ID );

		// Check User
		if ( $user->exists() ) {
			$downloads = 0;
			$query     = new WP_Query( [
				'post_type'      => [ 'attachment' ],
				'post_status'    => [ 'inherit' ],
				'posts_per_page' => -1,
				'author'         => $user_ID,
				'fields'         => 'ids',
			] );

			if ( $query->posts ) {
				foreach ( $query->posts as $post_ID ) {
					$downloads += (int) get_post_meta( $post_ID, '_download_count', true );

					if ( $downloads >= 50 ) break;
				}
			}

			if ( $downloads >= 50 ) {
				set_badge_id( $user_ID, $badge_ID );
			}
		}
	}

endif;

add_action( 'theme_download', 'badge_popular_content_producer_download', 10, 1 );