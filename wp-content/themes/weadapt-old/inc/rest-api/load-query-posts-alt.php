<?php

/**
 * Rest Api Init
 */
add_action( 'rest_api_init', function () {
	register_rest_route( 'weadapt/v1', '/load-query-posts-alt', [
		'methods'             => 'POST',
		'callback'            => 'rest_load_query_posts_alt',
		'permission_callback' => '__return_true'
	] );
} );

function rest_load_query_posts_alt($request) {
	$args           = ! empty( $request->get_param( 'args' ) ) ? json_decode( $request->get_param( 'args' ), true ) : [];
	$base_url       = ! empty( $request->get_param( 'base_url' ) ) ? esc_attr( $request->get_param( 'base_url' ) ) : '';
	$search         = ! empty( $request->get_param( 'search' ) ) ? esc_html( $request->get_param( 'search' ) ) : '';
	$query_type     = ! empty( $request->get_param( 'query_type' ) ) ? esc_html( $request->get_param( 'query_type' ) ) : 'wp_query';
	$request_body   = [];

	$status_array	= [];
	$status_pilot 	= ! empty( $request->get_param( 'status_pilot' ) ) ? esc_html( $request->get_param( 'status_pilot' ) ) : '';
	$status_full 	= ! empty( $request->get_param( 'status_full' ) ) ? esc_html( $request->get_param( 'status_full' ) ) : '';

	if( !empty($status_pilot) ) {
		$status_array[] = $status_pilot;
	}
	if( !empty($status_full) ) {
		$status_array[] = $status_full;
	}

	foreach([
		'solution-scale'     			=> $request->get_param( 'solution-scale' ),
		'solution-ecosystem-type'     	=> $request->get_param( 'solution-ecosystem-type' ),
		'solution-type'     			=> $request->get_param( 'solution-type' ),
		'solution-sector'     			=> $request->get_param( 'solution-sector' ),
		'solution-climate-impact'    	=> $request->get_param( 'solution-climate-impact' ),
		'sort_by'        				=> $request->get_param( 'sort_by' ),
		'paged'          				=> $request->get_param( 'paged' ),
		'search'		              	=> $search,
		'status'		              	=> $status_array,
		'post_type'      				=> $request->get_param( 'post_type' ),
	] as $key => $request_item ) {
		if ( ! empty( $request_item ) ) {
			$request_body[$key] = $request_item;
		}
	}

	$args_for_query = apply_filters( 'args_for_solutions_query', $args, $request_body, $query_type );

	/* Taxonomies */
	$scales 	= $args_for_query['scales'];
	$ecosystems = $args_for_query['ecosystems'];
	$types 		= $args_for_query['types'];
	$sectors 	= $args_for_query['sectors'];
	$impacts 	= $args_for_query['impacts'];
	$statuses 	= $args_for_query['status'];

	$query_args     = $args_for_query['args'];
	$max_num_pages  = 0;

	$tax_query = [
		'relation' => 'AND'
	];

	$meta_query = [
		'relation' => 'OR',
	];

	if( !empty($scales) ) {
		$tax_query[] = [
			'taxonomy' => 'solution-scale',
			'field'    => 'term_id',
			'terms'    => wp_parse_id_list($args_for_query["scales"]),
			'operator' => 'IN',
		];
	}
	if( !empty($ecosystems) ) {
		$tax_query[] = [
			'taxonomy' => 'solution-ecosystem-type',
			'field'    => 'term_id',
			'terms'    =>  wp_parse_id_list($args_for_query["ecosystems"]),
			'operator' => 'IN',
		];
	}
	if( !empty($types) ) {
		$tax_query[] = [
			'taxonomy' => 'solution-type',
			'field'    => 'term_id',
			'terms'    =>  wp_parse_id_list($args_for_query["types"]),
			'operator' => 'IN',
		];
	}
	if( !empty($sectors) ) {
		$tax_query[] = [
			'taxonomy' => 'solution-sector',
			'field'    => 'term_id',
			'terms'    =>  wp_parse_id_list($args_for_query["sectors"]),
			'operator' => 'IN',
		];
	}
	if( !empty($impacts) ) {
		$tax_query[] = [
			'taxonomy' => 'solution-climate-impact',
			'field'    => 'term_id',
			'terms'    =>  wp_parse_id_list($args_for_query["impacts"]),
			'operator' => 'IN',
		];
	}

	if ( !empty($statuses) ) {
		foreach ($statuses as $status) {
			$meta_query[] = [
				'key'     => 'status',
				'value'   => $status,
				'compare' => '=',
			];
		}
	}

	if ( ! empty( $tax_query ) ) {
		$query_args['tax_query'] = $tax_query;
	}
	if ( ! empty( $meta_query ) ) {
		$query_args['meta_query'] = $meta_query;
	}

	ob_start();
	$query = new WP_Query( $query_args );
	$max_num_pages = $query->max_num_pages;
	$results       = $query->found_posts;

	if ( $results > 0 ) {
		echo sprintf( '<div class="custom-results-count results-count">%s</div>', sprintf(
			_n( 'Found %s post.', 'Found %s posts.', $results, 'weadapt' ),
			number_format_i18n( $results )
		) );
	}

	if ( $query->have_posts() ) {

		while ( $query->have_posts() ) {

			$query->the_post();
			$post_type = get_post_type();

			echo get_part( 'components/alt-resource-item/index', [
				'resource_ID' => get_the_ID(),
				'resource_type' 		=> 'resource',
				'resource_cta_label' 	=> $post_type === 'event' ? 'View Event' : 'View Resource',
			] );
		}
	} else {
		echo sprintf( '<span class="col-12 empty-result">%s</span>', __( 'Nothing found.', 'weadapt' ) );
	}

	$output_html = ob_get_clean();
	$output_filters_html = '';

	if (! empty( $scales ) || ! empty( $ecosystems ) || ! empty( $types ) || ! empty( $sectors ) || ! empty( $impacts ) ) :
		ob_start();
			do_action( 'selected_taxonomies_filter', $base_url, $args_for_query );
		$output_filters_html = ob_get_clean();
	endif;

	echo json_encode( [
		'page'                => $args_for_query['page'],
		'pages'               => $max_num_pages,
		'output_html'         => $output_html,
		'output_filters_html' => $output_filters_html,
	] );

	die();
}