<?php

/**
 * Rest Api Init
 */
add_action( 'rest_api_init', function () {
	register_rest_route( 'weadapt/v1', '/load-search-posts', [
		'methods'             => 'POST',
		'callback'            => 'rest_load_search_posts',
		'permission_callback' => '__return_true'
	] );
} );

function rest_load_search_posts( $request ) {
	$query_args   = ! empty( $request->get_param( 'args' ) ) ? json_decode( $request->get_param( 'args' ), true ) : [];
	$paged        = ! empty( $request->get_param( 'paged' ) ) ? intval( $request->get_param( 'paged' ) ) : 1;
	$search       = ! empty( $request->get_param( 'search' ) ) ? esc_html( $request->get_param( 'search' ) ) : '';
	$query_type   = ! empty(  $request->get_param( 'query_type' ) ) ? esc_html( $request->get_param( 'query_type' ) ) : 'wp_query';

	$max_num_pages = 0;
	$query_args['paged'] = $paged;

	if ( ! empty( $search ) ) {
		if ( $query_type === 'user_query' ) {
			$query_args['search'] = "*$search*";
			$query_args['search_columns'] = ['display_name'];
		}
		else {
			$query_args['s'] = $search;
		}
	}

	ob_start();

		if ( $query_type === 'user_query') {
			$user_query = new WP_User_Query( $query_args );
			$max_num_pages = ceil( $user_query->get_total() / get_option( 'posts_per_page' ) );

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
			$query = new WP_Query( $query_args );
			$max_num_pages = $query->max_num_pages;

			if ( $query->have_posts() ) :
				while ( $query->have_posts() ) : $query->the_post(); ?>
					<div class="col-12 col-lg-6">
						<?php echo get_part('components/info-widget-cpt/index', [
							'cpt_ID'  => get_the_ID(),
							'cpt_buttons' => [ 'find-out-more' ]
						]); ?>
					</div>
				<?php endwhile; wp_reset_postdata(); ?>
			<?php else: ?>
				<div class="col">
					<p class="cpt-content-heading__text">
						<?php _e( 'Nothing found.', 'weadapt' ); ?>
					</p>
				</div>
			<?php endif;

		}

	$output_html = ob_get_clean();


	echo json_encode( [
		'paged'       => $paged,
		'pages'       => $max_num_pages,
		'output_html' => $output_html,
	] );

	die();
}