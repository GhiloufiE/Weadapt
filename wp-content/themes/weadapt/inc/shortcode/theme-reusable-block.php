<?php

add_shortcode( 'theme_reusable_block', 'theme_reusable_block_handler' );

function theme_reusable_block_handler( $id ){
	if ( empty( $id ) ) {
		return;
	}

	$posts = get_posts( [
		'post_type' => 'wp_block',
		'include'   => $id
	] );

	if ( ! empty( $posts ) && ! empty( $content = $posts[0]->post_content ) ) {
		echo apply_filters( 'the_content', $content );
	}

	wp_reset_postdata();
}
