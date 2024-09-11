<?php
// For debuging use: error_log( print_r( $variable, true ) );


/**
 * Fix multisite post_author on save_post
 */
add_filter( 'wp_insert_post_data', function( $data, $postarr, $unsanitized_postarr, $update ) {
	if ( ! isset( $postarr['_wpnonce'] ) || ! isset( $postarr['ID'] ) || ! wp_verify_nonce( $postarr['_wpnonce'], 'update-post_' . $postarr['ID'] ) ) {
		return $data;
	}

	$people = get_field( 'people', $postarr['ID'] );

	if ( ! empty( $people['creator'][0] ) ) {
		$data['post_author'] = intval( $people['creator'][0] );
	}

	return $data;
}, 10, 4);


/**
 * Editors Flow Notifications
 */
add_action( 'save_post', function( $post_ID, $post, $update ) {
	if ( defined( 'IS_IMPORT' ) && IS_IMPORT ) {
		return;
	}

	if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-post_' . $post_ID ) ) {
		return;
	}

	$people       = get_field( 'people', $post_ID );
	$current_user = wp_get_current_user();

	// Publish
	if ( in_array( $post->post_status, ['publish'] ) ) {

		// Update Post Creator Field
		if ( empty( $people['creator'] ) ) {
			$people['creator'] = [$current_user->ID];
		}

		// Update Post Publisher Field
		if ( empty( $people['publisher'] ) ) {
			$people['publisher'] = [$current_user->ID];
		}

		// Update Post Contributor Field
		if ( empty( $people['contributors'] ) ) {
			$people['contributors'] = [$current_user->ID];
		}

		if (
			! empty( $people['creator'] ) ||
			! empty( $people['publisher'] ) ||
			! empty( $people['contributors'] )
		) {
			update_field( 'people', $people, $post_ID );
		}

	}

	// Draft / Pending
	if ( in_array( $post->post_status, ['draft', 'pending'] ) ) {

		$publish_to = get_field( 'publish_to', $post_ID );

		// Update Publish to Field
		if ( empty( $publish_to ) ) {
			update_field( 'publish_to', [get_current_blog_id()], $post_ID );
		}

		// Update Post Creator Field
		if ( empty( $people['creator'] ) ) {
			$people['creator'] = [$current_user->ID];

			update_field( 'people', $people, $post_ID );
		}
	}
}, 20, 3 );
