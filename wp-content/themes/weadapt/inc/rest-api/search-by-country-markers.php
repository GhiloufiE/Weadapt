<?php

/**
 * Rest Api Init
 */
add_action( 'rest_api_init', function () {
	register_rest_route( 'weadapt/v1', '/search-country-markers', [
		'methods'             => 'POST',
		'callback'            => 'rest_search_country_markers',
		'permission_callback' => '__return_true'
	] );
} );

function rest_search_country_markers( $request ) {
	$country = ! empty( $request->get_param( 'select_country' ) ) ? esc_attr( $request->get_param( 'select_country' ) ) : '';
	$query_args = [
		'post_type'       => get_allowed_post_types( [ 'case-study', 'solutions-portal','stakeholders' ,'members','organisation'] ),
		'posts_per_page'  => -1,
		'fields'          => 'ids',
		'no_found_rows'   => true,
		'meta_query'      => [
			'relation' => 'OR',
			[
				'key'     => 'location',
				'value'   => $country,
				'compare' => 'LIKE'
			],
			[
				'key'     => 'address_location_org',
				'value'   => $country,
				'compare' => 'LIKE'
			],
			[
				'key'     => 'location_solution',
				'value'   => $country,
				'compare' => 'LIKE'
			],
			[
				'key'     => 'location_stakeholders',
				'value'   => $country,
				'compare' => 'LIKE'
			],
			[
				'key'     => 'location_members',
				'value'   => $country,
				'compare' => 'LIKE'
			],
		],
		'theme_query'     => true, // multisite fix
	];

	

	$case_studies = new WP_Query( $query_args );

	echo json_encode( [
		'ids' => $case_studies->posts
	] );

	die();
}