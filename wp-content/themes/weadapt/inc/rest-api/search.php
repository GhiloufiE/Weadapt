<?php

/**
 * Rest Api Init
 */
add_action( 'rest_api_init', function () {
	register_rest_route( 'weadapt/v1', '/search', [
		'methods'             => 'POST',
		'callback'            => 'rest_search',
		'permission_callback' => '__return_true'
	] );
} );

function rest_search( $request ) {
	$data     = json_decode( $request->get_body() );
	$query    = ! empty( $data->query ) ? sanitize_text_field( $data->query ) : '';

	ob_start();
	$has_output = false;

	$network_post_types = get_theme_network_post_types();

	if ( ! empty( $network_post_types ) ) {
		asort($network_post_types);

		foreach ( $network_post_types as $post_type ) {
			$query_args = array(
				's'                   => $query,
				'sentence'            => true,
				'post_type'           => $post_type,
				'posts_per_page'      => 3,
				'ignore_sticky_posts' => true,
				'theme_query'         => true, // multisite fix
			);

			$the_query = new WP_Query( $query_args );

			if ( $the_query->have_posts() ) :
			?>
				<header class="search-form__header">
					<h2><?php echo get_cpt_title( $post_type ); ?> (<?php echo $the_query->found_posts; ?>)</h2>

					<?php if ( $the_query->found_posts > $the_query->post_count ): ?>
						<a href="<?php echo add_query_arg( [ 's' => $query, 'post_type' => $post_type ], get_home_url() ); ?>"><span class="text-lg"><?php _e( 'View', 'weadapt' ); ?></span> <span class="text-md"><?php _e( 'All results', 'weadapt' ); ?></span></a>
					<?php endif; ?>
				</header>
			<?php

				while( $the_query->have_posts() ) : $the_query->the_post();
				?>
					<div class="search-form__item <?php echo esc_attr( $post_type ); ?>">
						<a href="<?php the_permalink(); ?>"><span class="screen-reader-text"><?php esc_html_e( 'Read more', 'weadapt' ); ?></span></a>
						<?php
							if ( in_array( $post_type, ['organisation', 'network', 'theme'] ) ):
								if ( has_post_thumbnail() ) :
									the_post_thumbnail( [39, 39] );
								endif;
							endif;
						?>
						<h2><?php echo wrap_content_query_string( $query, [get_the_title()] ); ?></h2>
						<p><?php echo wrap_content_query_string( $query, [get_the_excerpt(), get_the_content()], true ); ?></p>
					</div>
				<?php
				endwhile;

				wp_reset_postdata();

				$has_output = true;
			endif;
		}
	}

	$user_query_args = [
		'search'		      => "*$query*",
		'search_columns'      => ['display_name'],
		'number'              => 3,
		'fields'              => 'ID',
		'ignore_sticky_posts' => true,
		'theme_query'         => true, // multisite fix
	];


	$user_query = new WP_User_Query( $user_query_args );

	if ( ! empty( $user_query->get_results() ) ) :
		$people_template_ID = get_page_id_by_template( 'people' );
	?>
		<header class="search-form__header">
			<h2><?php _e( 'Users', 'weadapt' ); ?> (<?php echo $user_query->total_users; ?>)</h2>

			<?php if ( $user_query->total_users > count( $user_query->get_results() ) && ! empty( $people_template_ID ) ) : ?>
				<a href="<?php the_permalink( $people_template_ID ); ?>"><span class="text-lg"><?php _e( 'View', 'weadapt' ); ?></span> <span class="text-md"><?php _e( 'All results', 'weadapt' ); ?></span></a>
			<?php endif; ?>
		</header>
	<?php

		foreach ( $user_query->get_results() as $user_ID ) :
		?>

			<div class="search-form__item user">
				<a href="<?php echo get_author_posts_url( $user_ID ); ?>"><span class="screen-reader-text"><?php esc_html_e( 'Read more', 'weadapt' ); ?></span></a>

				<?php echo get_avatar( $user_ID, 80 ); ?>
				<h2><?php echo wrap_content_query_string( $query, [get_user_name( $user_ID )] ); ?></h2>
				<p><?php echo wrap_content_query_string( $query, [get_user_excerpt( $user_ID, -1 )], true ); ?></p>
			</div>

		<?php
		endforeach;

		$has_output = true;
	endif;

	if ( ! $has_output ) {
		?><div class="search-form__empty"><?php _e( 'Sorry, nothing found.', 'weadapt' ); ?></div><?php
	}

	$html = ob_get_clean();

	echo json_encode( $html );

	die();
}