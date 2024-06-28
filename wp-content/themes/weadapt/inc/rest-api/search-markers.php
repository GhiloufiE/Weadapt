<?php

/**
 * Rest Api Init
 */
add_action( 'rest_api_init', function () {
	register_rest_route( 'weadapt/v1', '/search-markers', [
		'methods'             => 'POST',
		'callback'            => 'rest_search_markers',
		'permission_callback' => '__return_true'
	] );
} );

function rest_search_markers( $request ) {
	$search_query  = ! empty( $request->get_param( 'search' ) ) ? esc_attr( $request->get_param( 'search' ) ) : '';
	$query_args = [
		'post_type'       => get_allowed_post_types( [ 'case-study', 'solutions-portal','stakeholders' ,'members','organisation'] ),
		'posts_per_page'  => -1,
		'fields'          => 'ids',
		'no_found_rows'   => true,
		's'               => $search_query,
		'theme_query'     => true, // multisite fix
	];

	

	$case_studies = new WP_Query( $query_args );

	echo json_encode( [
		'ids' => $case_studies->posts
	] );

	die();
}