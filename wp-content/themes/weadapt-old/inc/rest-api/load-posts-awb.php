<?php

/**
 * Rest Api Init
 */
add_action( 'rest_api_init', function () {
	register_rest_route( 'weadapt/v1', '/load-posts-awb', [
		'methods'             => 'POST',
		'callback'            => 'rest_load_posts_awb',
		'permission_callback' => '__return_true'
	] );
} );

function rest_load_posts_awb( $request ) {
	$args           = ! empty( $request->get_param( 'args' ) ) ? json_decode( $request->get_param( 'args' ), true ) : [];
	$base_url       = ! empty( $request->get_param( 'base_url' ) ) ? esc_attr( $request->get_param( 'base_url' ) ) : '';
	$search         = ! empty( $request->get_param( 'search' ) ) ? esc_html( $request->get_param( 'search' ) ) : '';
	$query_type     = ! empty(  $request->get_param( 'query_type' ) ) ? esc_html( $request->get_param( 'query_type' ) ) : 'wp_query';
	$has_categories = ! empty( $request->get_param( 'has_categories' ) ) ? wp_validate_boolean( $request->get_param( 'has_categories' ) ) : false;
	$tab            = ! empty( $request->get_param( 'tab' ) ) ? esc_html( $request->get_param( 'tab' ) ) : '';
	$selected_posts = ! empty( $request->get_param( 'selected_posts' ) ) ? esc_html( $request->get_param( 'selected_posts' ) ) : '';
	$request_body   = [];

	foreach([
		'categories'     => $request->get_param( 'categories' ),
		'sort_by'        => $request->get_param( 'sort_by' ),
		'paged'          => $request->get_param( 'paged' ),
		'search'              => $search,
		'post_type'      => $request->get_param( 'post_type' ),
		'post_types'     => $request->get_param( 'post_types' ) ? implode( ',', $request->get_param( 'post_types' ) ) : '',
	] as $key => $request_item ) {
		if ( ! empty( $request_item ) ) {
			$request_body[$key] = $request_item;
		}
	}

	$args_for_query = apply_filters( 'args_for_groups_filter', $args, $request_body, $query_type );
	$query_args     = $args_for_query['args'];
	$max_num_pages  = 0;
	$results        = 0;

	ob_start();

	$query         = new WP_Query( $query_args );

	$max_num_pages = $query->max_num_pages;
	$results       = $query->found_posts;

	if ( $results > 0 ) {
		echo sprintf( '<div class="col-12 results-count">%s</div>', sprintf(
			_n( 'Found %s post.', 'Found %s posts.', $results, 'weadapt' ),
			number_format_i18n( $results )
		) );
	}

	if ( $query->have_posts() ) {

		while ( $query->have_posts() ) {
			$query->the_post();

			$part = get_post_type() == 'event' ? 'event' : 'blog' ;

			foreach( [
				'theme_show_buttons',
				'theme_is_author_page',
				'theme_short_excerpt'
			] as $query_var ) {
				if ( ! empty( $query_args[$query_var] ) ) {
					set_query_var( $query_var, wp_validate_boolean( $query_args[$query_var] ) );
				}
			}

			?>
				<div class="col-12 col-md-6 col-lg-4">
					<?php get_part( "archive/templates/grid/$part", [
						'post_ID' => get_the_ID()
					] ); ?>
				</div>
			<?php
		}
	}
	else {
		echo sprintf( '<span class="col-12 empty-result">%s</span>', __( 'Nothing found.', 'weadapt' ) );
	}

	$output_html = ob_get_clean();

	$output_filters_html = '';
	$categories = ! empty( $args_for_query['categories'] ) ? $args_for_query['categories'] : [];
	$post_types = ! empty( $args_for_query['post_types'] ) ? $args_for_query['post_types'] : '';

	if ( ! empty( $categories ) || ! empty( $post_types ) ) :
		ob_start();
			if ( ! $has_categories && ! empty( $categories ) ) {
			    $args_for_query['tab'] = $tab;
				do_action( 'selected_categories_groups_filter', $base_url, $args_for_query );
			}

			if ( ! empty( $post_types ) ) {
			    $args_for_query['tab'] = $tab;
			    $args_for_query['selected_posts'] = $selected_posts;
				do_action( 'selected_post_types_groups_filter', $base_url, $args_for_query );
			}
		$output_filters_html = ob_get_clean();

	endif;

	echo json_encode( [
		'page'                => $args_for_query['page'],
		'pages'               => $max_num_pages,
		'results'             => $results,
		'output_html'         => $output_html,
		'output_filters_html' => $output_filters_html,
	] );

	die();
}
