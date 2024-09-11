<?php

/**
 * Rest Api Init
 */
add_action( 'rest_api_init', function () {
	register_rest_route( 'weadapt/v1', '/load-organisations-alt', [
		'methods'             => 'POST',
		'callback'            => 'rest_load_organisations_alt',
		'permission_callback' => '__return_true'
	] );
} );

function rest_load_organisations_alt( $request ) {
	$query_args          = ! empty( $request->get_param( 'query_args' ) ) ? json_decode( $request->get_param( 'query_args' ), true ) : [];
	$offset_organisation = $request->get_param( 'offset_organisation' ) ? intval( $request->get_param( 'offset_organisation' ) ) : null;
	$show_description = $request->get_param( 'show_description' ) ? $request->get_param( 'show_description' ) : 'true';

	if ( isset( $offset_organisation ) ) {
		$query_args['offset'] = $offset_organisation;
	}

	$all_posts  = [];

	$query = new WP_Query( $query_args );

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();

			$all_posts[] = [
				'type'  => 'post',
				'id'    => get_the_ID(),
				'title' => str_replace( '”', '"', get_the_title() ),
			];
		}
	}

	$filtered_posts = $all_posts;

	$type_count          = array_count_values( array_column( $filtered_posts, 'type' ) );
	$offset_organisation = $type_count['post'] ?? 0;

	ob_start();

	if ( ! empty( $filtered_posts ) ) :
		foreach ( $filtered_posts as $organisation ) : ?>
			<?php
				echo get_part( 'components/alt-organisation-item/index', [
					'org_ID'     		 => $organisation['id'],
					'show_description' 	 => $show_description,
				]);
			?>
		<?php endforeach; ?>
	<?php else: ?>
		<p><?php _e( 'There is no content', 'weadapt' ); ?></p>
	<?php endif;

	$output_html = ob_get_clean();

	echo json_encode( [
		'allPost'            => $query->found_posts,
		'outputHtml'         => $output_html,
		'offsetOrganisation' => $offset_organisation,
	] );

	die();
}