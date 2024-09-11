<?php

/**
 * Rest Api Init
 */
add_action( 'rest_api_init', function () {
	register_rest_route( 'weadapt/v1', '/search-theme-markers', [
		'methods'             => 'POST',
		'callback'            => 'rest_search_theme_markers',
		'permission_callback' => '__return_true'
	] );
} );

function rest_search_theme_markers( $request ) {
	$theme = ! empty( $request->get_param( 'theme_network' ) ) ? esc_attr( $request->get_param( 'theme_network' ) ) : '';
	$query_args = [
		'post_type'       => get_allowed_post_types( [ 'case-study', 'solutions-portal','stakeholders' ,'members','organisation'] ),
		'posts_per_page'  => -1,
		'fields'          => 'ids',
		'no_found_rows'   => true,
		'meta_query'      => [
			
				'key'   => 'relevant_main_theme_network',
				'value' => $theme,
			
		],
		'theme_query'     => true, // multisite fix
	];

	

	$case_studies = new WP_Query( $query_args );

	echo json_encode( [
		'ids' => $case_studies->posts
	] );

	die();
}