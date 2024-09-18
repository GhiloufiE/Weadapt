<?php

/**
 * Rest Api Init
 */
add_action( 'rest_api_init', function () {
	register_rest_route( 'weadapt/v1', '/search-members-markers', [
		'methods'             => 'POST',
		'callback'            => 'rest_search_members_markers',
		'permission_callback' => '__return_true'
	] );
} );

function rest_search_members_markers( $request ) {
	$search_query  = ! empty( $request->get_param( 'search' ) ) ? esc_attr( $request->get_param( 'search' ) ) : '';
	$theme_network = ! empty( $request->get_param( 'theme_network' ) ) ? intval( $request->get_param( 'theme_network' ) ) : 0;
	$country = ! empty( $request->get_param( 'select_country' ) ) ? esc_attr( $request->get_param( 'select_country' ) ) : '';

	$query_args = [
		'post_type'       => get_allowed_post_types( [ 'members' ] ),
		'posts_per_page'  => -1,
		'fields'          => 'ids',
		'no_found_rows'   => true,
		's'               => $search_query,
		'meta_query'      => [
			'relation' => 'AND',
			[
				'key'     => 'location_members',
				'value'   => ':"lat";',
				'compare' => 'LIKE'
			],
			[
				'key'     => 'location_members',
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
	if ( ! empty( $country ) ) {
		$query_args['meta_query'][] = [
			'relation' => 'OR',
			[
				'key'     => 'address_country',
				'value'   => $country,
				'compare' => 'LIKE'
			],
		];
	}

	$case_studies = new WP_Query( $query_args );

	echo json_encode( [
		'ids' => $case_studies->posts
	] );

	die();
}