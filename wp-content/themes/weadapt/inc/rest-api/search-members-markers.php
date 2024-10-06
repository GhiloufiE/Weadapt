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
	$country_map = [
		'Germany' => 'DE',
		'France'  => 'FR',
		'Sweden'  => 'SE',
		'Spain'   => 'ES'
	];

	if ( array_key_exists( $country, $country_map ) ) {
		$country = $country_map[ $country ];
	}
	$query_args = [
		'search'         => "*{$search_query}*", 
		'search_columns' => [ 'user_login', 'user_nicename', 'user_email' ], 
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
		'number'         => -1, 
	];
	if ( ! empty( $theme_network ) ) {
		$query_args['meta_query'][] = [
			'key'   => 'relevant_main_theme_network',
			'value' => $theme_network,
		];
	}

	if ( ! empty( $country ) ) {
		$query_args['meta_query'][] = [
			'key'     => 'migrated_address_country',
			'value'   => $country,
			'compare' => 'LIKE'
		];
	}

	$user_query = new WP_User_Query( $query_args );
	$user_ids = wp_list_pluck( $user_query->get_results(), 'ID' );

	echo json_encode( [
		'ids' => $user_ids
	] );

	die();
}
