<?php

/**
 * Rest Api Init
 */
add_action( 'rest_api_init', function () {
	register_rest_route( 'weadapt/v1', '/search-case-study-solution-markers', [
		'methods'             => 'POST',
		'callback'            => 'rest_search_case_study_solution_markers',
		'permission_callback' => '__return_true'
	] );
} );

function rest_search_case_study_solution_markers( $request ) {
	$search_query  = ! empty( $request->get_param( 'search' ) ) ? esc_attr( $request->get_param( 'search' ) ) : '';
	$theme_network = ! empty( $request->get_param( 'theme_network' ) ) ? intval( $request->get_param( 'theme_network' ) ) : 0;
	$post_type = ! empty( $request->get_param( 'post_type' ) ) ? $request->get_param( 'post_type' ) : '';

	$query_args_cases = [
		'post_type'       => get_allowed_post_types( [ 'case-study' ] ),
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

	$query_args_solutions = [
    		'post_type'       => get_allowed_post_types( [ 'solutions-portal' ] ),
    		'posts_per_page'  => -1,
    		'fields'          => 'ids',
    		'no_found_rows'   => true,
    		's'               => $search_query,
    		'meta_query'      => [
    			'relation' => 'AND',
    			[
    				'key'     => 'central_location',
    				'value'   => ':"lat";',
    				'compare' => 'LIKE'
    			],
    			[
    				'key'     => 'central_location',
    				'value'   => ':"lng";',
    				'compare' => 'LIKE'
    			]
    		],
    		'theme_query'     => true, // multisite fix
    	];

	if ( ! empty( $theme_network ) ) {
		$query_args_cases['meta_query'][] = [
			'key'   => 'relevant_main_theme_network',
			'value' => $theme_network,
		];

		$query_args_solutions['meta_query'][] = [
			'key'   => 'relevant_main_theme_network',
			'value' => $theme_network,
		];
	}

	if(!empty($post_type) && $post_type != 'all'){
	    $query_args_cases['post_type'] = $post_type;
	    $query_args_solutions['post_type'] = $post_type;
	}

	$case_studies = new WP_Query( $query_args_cases );
	$solutions = new WP_Query( $query_args_solutions );

	$case_studies_posts = !empty($case_studies->posts) ? $case_studies->posts : [];
    $solutions_posts = !empty($solutions->posts) ? $solutions->posts : [];

    $posts = array_merge($case_studies_posts, $solutions_posts) ?? [];

	echo json_encode( [
		'ids' => $posts
	] );

	die();
}