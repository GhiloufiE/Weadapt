<?php

/**
 * Rest Api Init
 */
add_action( 'rest_api_init', function () {
	register_rest_route( 'weadapt/v1', '/load-contributors-alt', [
		'methods'             => 'POST',
		'callback'            => 'rest_load_contributors_alt',
		'permission_callback' => '__return_true'
	] );
} );

function rest_load_contributors_alt( $request ) {
	$user_query_args     = ! empty( $request->get_param( 'user_query_args' ) ) ? json_decode( $request->get_param( 'user_query_args' ), true ) : [];
    $offset_user         = $request->get_param( 'offset_user' ) ? intval( $request->get_param( 'offset_user' ) ) : null;

    if ( isset( $offset_user ) ) {
        $user_query_args['offset'] = $offset_user;
    }

    $all_posts  = [];

    $user_query = new WP_User_Query( $user_query_args );

    foreach ( $user_query->get_results() as $user_ID ) {
        $all_posts[] = [
            'type'  => 'user',
            'id'    => $user_ID,
            'title' => get_user_name( $user_ID ),
        ];
    }

    $filtered_posts = $all_posts;

    $type_count          = array_count_values( array_column( $filtered_posts, 'type' ) );
    $offset_user         = $type_count['user'] ?? 0;


    ob_start();

    if ( ! empty( $filtered_posts ) ) :
        foreach ( $filtered_posts as $contributor ) : ?>
			<?php
				echo get_part( 'components/alt-contributor-item/index', [
					'user_ID'    => $contributor['id'],
				] );
			?>
        <?php endforeach; ?>
    <?php else: ?>
		<p><?php _e( 'There is no content', 'weadapt' ); ?></p>
    <?php endif;

    $output_html = ob_get_clean();

    echo json_encode( [
        'allPost'            => $user_query->total_users,
        'outputHtml'         => $output_html,
        'offsetUser'         => $offset_user,
    ] );

    die();
}