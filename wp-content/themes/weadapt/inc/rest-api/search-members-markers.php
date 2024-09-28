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
		'search'         => "*{$search_query}*", // Search users by the query value.
		'search_columns' => [ 'user_login', 'user_nicename', 'user_email' ], // Specify which columns to search.
		'meta_query'     => [
			'relation' => 'AND',
			[
				'key'     => 'location_users',
				'value'   => ':"lat";',
				'compare' => 'LIKE'
			],
			[
				'key'     => 'location_users',
				'value'   => ':"lng";',
				'compare' => 'LIKE'
			]
		],
		'number'         => -1, // Equivalent to posts_per_page => -1 for users
	];

	// Filter by theme network if provided
	if ( ! empty( $theme_network ) ) {
		$query_args['meta_query'][] = [
			'key'   => 'relevant_main_theme_network',
			'value' => $theme_network,
		];
	}

	// Filter by country if provided
	if ( ! empty( $country ) ) {
		$query_args['meta_query'][] = [
			'key'     => 'address_country',
			'value'   => $country,
			'compare' => 'LIKE'
		];
	}

	// Run the user query
	$user_query = new WP_User_Query( $query_args );

	// Extract user IDs from the results
	$user_ids = wp_list_pluck( $user_query->get_results(), 'ID' );

	echo json_encode( [
		'ids' => $user_ids
	] );

	die();
}
