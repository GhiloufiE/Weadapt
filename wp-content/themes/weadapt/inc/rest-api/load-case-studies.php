<?php

/**
 * Rest Api Init
 */
add_action( 'rest_api_init', function () {
	register_rest_route( 'weadapt/v1', '/load-case-studies', [
		'methods'             => 'POST',
		'callback'            => 'rest_case_studies',
		'permission_callback' => '__return_true'
	] );
} );

function rest_case_studies( $request ) {
	$args           = ! empty( $request->get_param( 'args' ) ) ? json_decode( $request->get_param( 'args' ), true ) : [];
	$base_url       = ! empty( $request->get_param( 'base_url' ) ) ? esc_attr( $request->get_param( 'base_url' ) ) : '';
	$search         = ! empty( $request->get_param( 'search' ) ) ? esc_html( $request->get_param( 'search' ) ) : '';
	$query_type     = ! empty(  $request->get_param( 'query_type' ) ) ? esc_html( $request->get_param( 'query_type' ) ) : 'wp_query';
	$has_categories = ! empty( $request->get_param( 'has_categories' ) ) ? wp_validate_boolean( $request->get_param( 'has_categories' ) ) : false;
	$request_body   = [];

	foreach([
		'categories'     => $request->get_param( 'categories' ),
		'sort_by'        => $request->get_param( 'sort_by' ),
		'paged'          => $request->get_param( 'paged' ),
		's'              => $request->get_param( 's' ),
		'post_type'      => $request->get_param( 'post_type' ),
		'post_types'     => $request->get_param( 'post_types' ) ? implode( ',', $request->get_param( 'post_types' ) ) : '',
	] as $key => $request_item ) {
		if ( ! empty( $request_item ) ) {
			$request_body[$key] = $request_item;
		}
	}

	$args_for_query = apply_filters( 'args_for_query', $args, $request_body, $query_type );
	$query_args     = $args_for_query['args'];
	$max_num_pages  = 0;
	$results        = 0;

	ob_start();
		if ( $query_type === 'user_query') {
			if ( ! empty( $search ) ) {
				$query_args['search'] = "*$search*";
				$query_args['search_columns'] = ['display_name'];
			}

			$user_query = new WP_User_Query( $query_args );

			$posts_per_page = $query_args['number'] ? intval( $query_args['number'] ) : get_option( 'posts_per_page' );
			$results        = $user_query->get_total();
			$max_num_pages  = ceil( $results / $posts_per_page );

			if ( $results > 0 ) {
				echo sprintf( '<div class="results-count">%s</div>', sprintf(
					_n( 'Found %s user.', 'Found %s users.', $results, 'weadapt' ),
					number_format_i18n( $results )
				) );
			}

			if ( ! empty( $user_query->results ) ) :
				foreach ( $user_query->results as $user_ID ) :
					echo get_part( 'components/member-item/index', [
						'member_ID' => $user_ID
					] );
				endforeach; ?>
			<?php else: ?>
				<p class="cpt-content-heading__text">
					<?php _e( 'Nothing found.', 'weadapt' ); ?>
				</p>
			<?php endif;
		}
		else {
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

					foreach( [
						'theme_show_buttons',
						'theme_is_author_page',
						'theme_short_excerpt'
					] as $query_var ) {
						if ( ! empty( $query_args[$query_var] ) ) {
							set_query_var( $query_var, wp_validate_boolean( $query_args[$query_var] ) );
						}
					}

                    echo '<div class="col-12 col-md-6 col-lg-4">';
                    get_part( "archive/templates/grid/blog", [
                        'post_ID' => get_the_ID()
                    ] );
                    echo '</div>';

				}
			}
			else {
				echo sprintf( '<span class="col-12 empty-result">%s</span>', __( 'Nothing found.', 'weadapt' ) );
			}
		}

	$output_html = ob_get_clean();

	$output_filters_html = '';
	$categories = ! empty( $args_for_query['categories'] ) ? $args_for_query['categories'] : [];
	$post_types = ! empty( $args_for_query['post_types'] ) ? $args_for_query['post_types'] : '';

	if ( ! empty( $categories ) || ! empty( $post_types ) ) :
		ob_start();
			if ( ! $has_categories && ! empty( $categories ) ) {
				do_action( 'selected_categories_filter', $base_url, $args_for_query );
			}

			if ( ! empty( $post_types ) ) {
				do_action( 'selected_post_types_filter', $base_url, $args_for_query );
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
