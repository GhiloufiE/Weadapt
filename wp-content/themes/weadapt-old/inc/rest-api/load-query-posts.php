<?php

/**
 * Rest Api Init
 */
add_action( 'rest_api_init', function () {
	register_rest_route( 'weadapt/v1', '/load-query-posts', [
		'methods'             => 'POST',
		'callback'            => 'rest_load_query_posts',
		'permission_callback' => '__return_true'
	] );
} );

function rest_load_query_posts( $request ) {
	$query_args   = ! empty( $request->get_param( 'args' ) ) ? json_decode( $request->get_param( 'args' ), true ) : [];
	$paged        = ! empty( $request->get_param( 'paged' ) ) ? intval( $request->get_param( 'paged' ) ) : 2;

	$query_args['paged'] = $paged;

	ob_start();
		$query = new WP_Query( $query_args );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$post_type     = get_post_type();
				$part_name     = file_exists( get_theme_file_path( "/parts/archive/templates/grid/$post_type.php" ) ) ? $post_type : 'blog';

				?>
					<div class="col-12 col-md-6 col-lg-4">
						<?php get_part( "archive/templates/grid/$part_name", [
							'post_ID' => get_the_ID()
						] ); ?>
					</div>
				<?php
			}
		}
	$output_html = ob_get_clean();

	echo json_encode( [
		'paged'       => $paged,
		'pages'       => $query->max_num_pages,
		'output_html' => $output_html
	] );

	die();
}