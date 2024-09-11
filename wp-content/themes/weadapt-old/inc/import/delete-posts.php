<?php

/*

Users:
http://weadapt/web/?import=delete-posts&key=013b0f890d204a522a7e462d1dfa93e5

*/

if (
	is_admin() ||
	! isset( $_GET['import'] ) ||
	$_GET['import'] != 'delete-posts' ||
	! isset( $_GET['key'] ) ||
	$_GET['key'] != '013b0f890d204a522a7e462d1dfa93e5'
) return;


add_action( 'init', function() {
	$the_query = new WP_Query( [
		'post_status'    => 'any',
		// 'post_type'      => ['theme', 'network'], // initiative
		// 'post_type'      => ['organisation'], // organisation
		// 'post_type'      => ['article', 'event', 'blog'], // article
		// 'post_type'      => ['solutions-portal'], // aaa_portal_solution
		'post_type'      => ['case-study'], // placemarks
		'posts_per_page' => -1,
		'meta_query'     => [[
			'key'     => 'is_new_import',
			'compare' => 'NOT EXISTS',
		],],
		// 'date_query' => [
		// 	[
		// 		'before'    => [
		// 			'year'  => 2023,
		// 			'month' => 7,
		// 			'day'   => 1,
		// 		],
		// 	],
		// ],
	] );

	s($the_query);
	die();

	if ( $the_query->have_posts() ) :
		while( $the_query->have_posts() ) : $the_query->the_post();
			$logs       = '';
			$logs_error = '';
			$post_ID    = get_the_ID();
			$post_title = get_the_title();
			$post_type  = get_post_type();

			if ( false === wp_delete_post( $post_ID, true ) ){
				$logs_error .= 'Post: ' . $post_ID . ' | ' . $post_title . ' - error.' . PHP_EOL . PHP_EOL;
			}
			else {
				$logs .= 'Post: ' . $post_ID . ' | ' . $post_title . ' - deleted.' . PHP_EOL . PHP_EOL;
			}

			echo '<pre>' . $logs . '</pre>';

			// Logs Output
			if ( ! empty( $logs ) ) {
				file_put_contents( '../logs/delete/' . $post_type . '.log', $logs, FILE_APPEND );
			}

			// Logs Error Output
			if ( ! empty( $logs_error ) ) {
				file_put_contents( '../logs/delete/' . $post_type . '-error.log', $logs_error, FILE_APPEND );
			}
		endwhile;

		?>
			<script type="text/javascript">
				window.onload = function() {
					setTimeout(function() {
						// location.reload();
					}, 0 );
				};
			</script>
		<?php
	endif;

	die();
} );