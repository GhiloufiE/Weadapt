<?php

/*

Users:
http://weadapt/web/?import=delete-users&key=013b0f890d204a522a7e462d1dfa93e5

*/

if (
	is_admin() ||
	! isset( $_GET['import'] ) ||
	$_GET['import'] != 'delete-users' ||
	! isset( $_GET['key'] ) ||
	$_GET['key'] != '013b0f890d204a522a7e462d1dfa93e5'
) return;


add_action( 'init', function() {
	$user_query = new WP_User_Query( [
		'post_status'    => 'any',
		'fields'         => 'ID',
		'role__not_in'   => ['administrator'],
		'number'         => 50,
		'meta_query'     => [[
			'key'     => 'is_new_import',
			'compare' => 'NOT EXISTS',
		],]
	] );
	s($user_query->results);

	if ( ! empty( $user_query->results ) ) :
		foreach ( $user_query->results as $user_ID ) :
			$logs       = '';
			$logs_error = '';
			$user       = new WP_User( $user_ID );

			s($user);
			// die();

			// if ( false === wp_delete_user( $user_ID, 1 ) ){
			// 	$logs_error .= 'User: ' . $user->ID . ' | ' . $user->user_email . ' - error.' . PHP_EOL . PHP_EOL;
			// }
			// else {
			// 	$logs .= 'User: ' . $user->ID . ' | ' . $user->user_email . ' - deleted.' . PHP_EOL . PHP_EOL;
			// }
			// echo '<pre>' . $logs . '</pre>';

			// // Logs Output
			// if ( ! empty( $logs ) ) {
			// 	file_put_contents( '../logs/delete/user.log', $logs, FILE_APPEND );
			// }

			// // Logs Error Output
			// if ( ! empty( $logs_error ) ) {
			// 	file_put_contents( '../logs/delete/user-error.log', $logs_error, FILE_APPEND );
			// }
		endforeach;

		?>
			<script type="text/javascript">
				window.onload = function() {
					setTimeout(function() {
						location.reload();
					}, 0 );
				};
			</script>
		<?php
	endif;

	die();
} );