<?php

/**
 * Rest Api Init
 */
add_action( 'rest_api_init', function () {
	register_rest_route( 'weadapt/v1', '/load-post-user', [
		'methods'             => 'POST',
		'callback'            => 'rest_load_post_user',
		'permission_callback' => '__return_true'
	] );
} );

function rest_load_post_user( $request ) {
	$query_args          = ! empty( $request->get_param( 'query_args' ) ) ? json_decode( $request->get_param( 'query_args' ), true ) : [];
	$user_query_args     = ! empty( $request->get_param( 'user_query_args' ) ) ? json_decode( $request->get_param( 'user_query_args' ), true ) : [];
	$search              = ! empty( $request->get_param( 'search' ) ) ? esc_html( $request->get_param( 'search' ) ) : null;
	$offset_organisation = $request->get_param( 'offset_organisation' ) ? intval( $request->get_param( 'offset_organisation' ) ) : null;
	$offset_user         = $request->get_param( 'offset_user' ) ? intval( $request->get_param( 'offset_user' ) ) : null;
	$logged_in           = $request->get_param( 'logged_in' ) ? wp_validate_boolean( $request->get_param( 'logged_in' ) ) : false;
	$is_load_more        = $request->get_param( 'is_load_more' ) ? wp_validate_boolean( $request->get_param( 'is_load_more' ) ) : false;
	$is_empty_search     = empty( $search ) && (!$is_load_more);
	$posts_per_page      = $is_empty_search ? 6 : 3;

	if ( ! empty( $search ) ) {
		unset( $query_args[ 'post__not_in' ] );
		unset( $user_query_args[ 'exclude' ] );

		$query_args['s'] = $search;
		$user_query_args['search'] = "*$search*";
		$user_query_args['search_columns'] = ['display_name'];
	}

	if ( isset( $offset_organisation ) ) {
		$query_args['offset'] = $offset_organisation;
	}

	if ( isset( $offset_user ) ) {
		$user_query_args['offset'] = $offset_user;
	}

	$all_posts  = [];

	$query = new WP_Query( $query_args );

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();

			if ( ! $is_empty_search ) {
				$all_posts[] = [
					'type'  => 'post',
					'id'    => get_the_ID(),
					'title' => str_replace( '”', '"', get_the_title() ),
				];
			}
		}
	}
	wp_reset_postdata();

	if ( $is_empty_search ) {
		$user_query_args['orderby'] = 'registered';
		$user_query_args['order']   = 'DESC';

		unset( $user_query_args[ 'exclude' ] );
	}

	$user_query = new WP_User_Query( $user_query_args );

	foreach ( $user_query->get_results() as $user_ID ) {
		$all_posts[] = [
			'type'  => 'user',
			'id'    => $user_ID,
			'title' => get_user_name( $user_ID ),
		];
	}

	$filtered_posts = $all_posts;

	usort( $filtered_posts, function( $a, $b ) {
		return strcmp( $a['title'], $b['title'] );
	});

	$filtered_posts = array_slice( $filtered_posts, 0, $posts_per_page );

	$type_count          = array_count_values( array_column( $filtered_posts, 'type' ) );
	$offset_organisation = $type_count['post'] ?? 0;
	$offset_user         = $type_count['user'] ?? 0;

	ob_start();
		if ( ! empty( $filtered_posts ) ) :
			foreach ( $filtered_posts as $post ) : ?>
				<div class="contributors-organisations__col col-12 col-md-4">
					<?php
						if ( $post['type'] === 'post' ) {
							echo get_part( 'components/info-widget-cpt/index', [
								'cpt_ID'            => $post['id'],
								'cpt_buttons'       => ['find-out-more'],
								'hide_empty_fields' => true,
							]);
						} else {
							echo get_part( 'components/info-widget-user/index', [
								'user_ID'            => $post['id'],
								'show_follow_button' => false,
								'hide_empty_fields'  => true,
								'logged_in'          => $logged_in,
							] );
						}
					?>
				</div>
			<?php endforeach; ?>
		<?php else: ?>
			<div class="col">
				<p><?php _e( 'There is no content', 'weadapt' ); ?></p>
			</div>
		<?php endif;
	$output_html = ob_get_clean();

	echo json_encode( [
		'allPost'            => $query->found_posts + $user_query->total_users,
		'outputHtml'         => $output_html,
		'offsetOrganisation' => $offset_organisation,
		'offsetUser'         => $offset_user,
	] );

	die();
}