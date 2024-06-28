<?php

/**
 * Rest Api Init
 */
add_action( 'rest_api_init', function () {
	register_rest_route( 'weadapt/v1', '/load-resources-alt', [
		'methods'             => 'POST',
		'callback'            => 'rest_load_resources_alt',
		'permission_callback' => '__return_true'
	] );
} );

function rest_load_resources_alt( $request ) {
	$query_args			= ! empty( $request->get_param( 'query_args' ) ) ? json_decode( $request->get_param( 'query_args' ), true ) : [];
    $offset_resource    = $request->get_param( 'offset_resource' ) ? intval( $request->get_param( 'offset_resource' ) ) : null;

    if ( isset( $offset_resource ) ) {
        $query_args['offset'] = $offset_resource;
    }

    $all_posts  = [];

	$query = new WP_Query( $query_args );

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();

			$all_posts[] = [
				'type'  => 'resource',
				'id'    => get_the_ID(),
				'title' => str_replace( '”', '"', get_the_title() ),
			];
		}
	}

	$filtered_posts = $all_posts;

	$type_count			= array_count_values( array_column( $filtered_posts, 'type' ) );
	$offset_resource 	= $type_count['resource'] ?? 0;

	ob_start();

	if ( ! empty( $filtered_posts ) ) :
		foreach ( $filtered_posts as $resource ) : ?>
			<?php
				$post_type = get_post_type($resource['id']);
				echo get_part( 'components/alt-resource-item/index', [
					'resource_ID' 			=> $resource['id'],
					'resource_type' 		=> 'resource',
					'resource_cta_label' 	=> $post_type == 'event' ?  'View Event' :  'View Resource',
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
		'offsetResource'     => $offset_resource,
	] );

	die();
}