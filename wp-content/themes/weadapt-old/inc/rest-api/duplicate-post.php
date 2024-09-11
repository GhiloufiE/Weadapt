<?php

/**
 * The event handler Post Like
 */
function action_post_duplicate() {
	global $wpdb;

	$post_ID  = intval( $_POST['post_id'] );
	$title    = get_the_title( $post_ID );
	$old_post = get_post( $post_ID );

	$post = [
		'post_status'  => 'draft',
		'post_title'   => sanitize_text_field( $title ),
		'post_content' => $old_post->post_content,
		'post_type'    => $old_post->post_type,
		'post_parent'  => $old_post->post_parent,
		'post_excerpt' => $old_post->post_excerpt,
	];

	$new_post_ID = wp_insert_post( $post );

	if ( is_wp_error( $new_post_ID ) ) {
		wp_die( json_encode( array( 'status' => 'error', 'message' => $new_post_ID->get_error_message() ) ) );
	}


	// Meta
	$meta_data = $wpdb->get_results(
		$wpdb->prepare( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d", $post_ID, )
	);

	if ( ! empty( $meta_data ) ) {
		foreach ( $meta_data as $meta ) {
			add_post_meta( $new_post_ID, $meta->meta_key, maybe_unserialize( $meta->meta_value ) );
		}
	}


	// Taxonomies
	$taxonomies = get_post_taxonomies( $post_ID );

	if ( ! empty( $taxonomies ) ) {
		foreach ($taxonomies as $taxonomy) {
			wp_set_object_terms( $new_post_ID, wp_get_object_terms(
				$post_ID,
				$taxonomy,
				['fields' => 'ids']
			), $taxonomy );
		}
	}

	wp_die( json_encode( array(
		'status'   => 'success',
		'message'  => __( 'Duplicate successful, redirecting...', 'weadapt' ),
		'redirect' => get_edit_post_link( $new_post_ID )
	) ) );
}

add_action( 'wp_ajax_post_duplicate', 'action_post_duplicate' );