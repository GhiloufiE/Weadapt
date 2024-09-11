<?php

/**
 * Rest Api Init
 */
add_action( 'rest_api_init', function () {
	register_rest_route( 'weadapt/v1', '/search-all-markers', [
		'methods'             => 'POST',
		'callback'            => 'rest_search_all_markers',
		'permission_callback' => '__return_true'
	] );
} );

function rest_search_all_markers( $request ) {
	$search_query  = ! empty( $request->get_param( 'search' ) ) ? esc_attr( $request->get_param( 'search' ) ) : '';
	$theme_network = ! empty( $request->get_param( 'theme_network' ) ) ? intval( $request->get_param( 'theme_network' ) ) : 0;
	$query_args = [
		'post_type'       => get_allowed_post_types( [ 'case_study' ]),
		'posts_per_page'  => -1,
		'fields'          => 'ids',
		'no_found_rows'   => true,
		's'               => $search_query,
		'meta_query'      => [
			'relation' => 'AND',
			[
				'key'     => 'location',
				'value'   => ':"lat";',
				'compare' => 'LIKE'
			],
			[
				'key'     => 'location',
				'value'   => ':"lng";',
				'compare' => 'LIKE'
			]
		],
		'theme_query'     => true, // multisite fix
	];
	
	if ( ! empty( $theme_network ) ) {
		$query_args['meta_query'][] = [
			'key'   => 'relevant_main_theme_network',
			'value' => $theme_network,
		];
	}

	$case_studies = new WP_Query( $query_args );

	echo json_encode( [
		'ids' => $case_studies->posts
	] );

	die();
}