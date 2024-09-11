<?php

/*

Users:
http://weadapt/web/?import=update-comment-status&key=013b0f890d204a522a7e462d1dfa93e5


mike-dev
85dCXpavMK!zyZAQ

*/

if (
	is_admin() ||
	! isset( $_GET['import'] ) ||
	$_GET['import'] != 'update-comment-status' ||
	! isset( $_GET['key'] ) ||
	$_GET['key'] != '013b0f890d204a522a7e462d1dfa93e5'
) return;


add_action( 'init', function() {
	$query = new WP_Query( array(
		'posts_per_page' => -1,
		'post_type'      => 'any',
		'post_status'    => 'any',
		'fields'         => 'ids'
	) );
	var_dump($query->posts);

	if ( ! empty( $query->posts ) ) {
		global $wpdb;

		foreach ( $query->posts as $post_ID ) {
			$wpdb->update( $wpdb->prefix . 'posts', [ 'comment_status' => 'open' ], [ 'ID' => $post_ID ] );
		}
	}

	die();
} );