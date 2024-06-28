<?php

/*

Badges:
http://weadapt-env/?import=update-badges&key=013b0f890d204a522a7e462d1dfa93e5
http://weadapt-env/?import=update-badges&key=013b0f890d204a522a7e462d1dfa93e5&include=1,7764,7765,7766


*/

if (
	is_admin() ||
	! isset( $_GET['import'] ) ||
	$_GET['import'] != 'update-badges' ||
	! isset( $_GET['key'] ) ||
	$_GET['key'] != '013b0f890d204a522a7e462d1dfa93e5'
) return;


add_action( 'init', function() {
	echo '<table>';

	$number  = 10;
	$offset  = isset( $_GET['offset'] ) ? intval( $_GET['offset'] ) : 0;
	$include = isset( $_GET['include'] ) ? esc_attr( $_GET['include'] ) : 0;
	$args    = [
		'fields' => 'ID',
		'number' => -1,
	];

	if ( ! empty( $include ) ) {
		$args['include'] = wp_parse_id_list( $include );
	}
	else {
		$args['number'] = $number;
		$args['offset'] = $offset;
	}

	$user_query = new WP_User_Query( $args );

	if ( ! empty( $user_query->results ) ) {
		foreach ( $user_query->results as $user_ID ) {
			$logs = '';
			$user = new WP_User( $user_ID );

			if ( $user->exists() ) {
				$logs .= '[User ID]: ' . $user_ID . PHP_EOL;
				$logs .= '[User]: ' . $user->display_name . PHP_EOL;
				$logs .= '[Badges]:';


				// Badge 1 - Prolific Publisher!
				$badge_ID = get_badge_id( 'prolific-publisher' );

				if ( ! empty( $badge_ID ) ) {
					$logs .= PHP_EOL . ' - 1) prolific-publisher | ';

					$query = new WP_Query( [
						'post_type'      => [ 'article', 'blog', 'course', 'event', 'case-study' ],
						'post_status'    => [ 'publish' ],
						'posts_per_page' => 5,
						'fields'         => 'ids',
						'meta_query'     => [
							[
								'key'     => 'people_creator',
								'value'   => sprintf( ':"%d";', $user_ID ),
								'compare' => 'LIKE'
							],
						],
					] );

					if ( 5 === $query->found_posts ) {
						set_badge_id( $user_ID, $badge_ID, true );

						$logs .= 'add';
					}
					else {
						delete_badge_id( $user_ID, $badge_ID, true );

						$logs .= 'delete';
					}
				}


				// Badge 3 - Passionate Learner!
				$logs .= PHP_EOL . ' - 3) passionate-learner | with cookie | ---';


				// Badge 4 - Expert Editor!
				$badge_ID = get_badge_id( 'expert-editor' );

				if ( ! empty( $badge_ID ) ) {
					$logs .= PHP_EOL . ' - 4) expert-editor | ';

					$has_editor_role = false;
					$temp_blog_ids   = [];

					foreach ( get_sites() as $blog ) {
						switch_to_blog( $blog->blog_id );

						$user_roles = get_userdata( $user_ID )->roles;

						if (
							in_array( 'author', $user_roles ) || // Author (Editor)
							in_array( 'editor', $user_roles )    // Editor (Microsite Editor)
						) {
							$has_editor_role = true;
							$temp_blog_ids[] = $blog->blog_id;
						}
					}

					restore_current_blog();

					if ( ! empty( $temp_blog_ids ) ) {
						$logs .= 'editor in: ' . implode( ',', $temp_blog_ids ) . ' | ';
					}

					if ( $has_editor_role ) {
						set_badge_id( $user_ID, $badge_ID, true );

						$logs .= 'add';
					}
					else {
						delete_badge_id( $user_ID, $badge_ID, true );

						$logs .= 'delete';
					}
				}


				// Badge 5 - Avid Explorer!
				$badge_ID = get_badge_id( 'avid-explorer' );

				if ( ! empty( $badge_ID ) ) {
					$logs .= PHP_EOL . ' - 5) avid-explorer | ';

					$frequently_logged_in = get_user_meta( $user_ID, 'frequently_logged_in', true );

					if ( ! empty( $frequently_logged_in ) && count( $frequently_logged_in ) >= 20 ) {
						set_badge_id( $user_ID, $badge_ID, true );

						$logs .= 'add';
					}
					else {
						delete_badge_id( $user_ID, $badge_ID, true );

						$logs .= 'delete';
					}
				}


				// Badge 6 - Popular Content Producer!
				$badge_ID = get_badge_id( 'popular-content-producer' );

				if ( ! empty( $badge_ID ) ) {
					$logs .= PHP_EOL . ' - 6) popular-content-producer | ';

					$downloads = 0;
					$query     = new WP_Query( [
						'post_type'      => [ 'attachment' ],
						'post_status'    => [ 'inherit' ],
						'posts_per_page' => -1,
						'author'         => $user_ID,
						'fields'         => 'ids',
					] );

					if ( $query->posts ) {
						foreach ( $query->posts as $post_ID ) {
							$downloads += (int) get_post_meta( $post_ID, '_download_count', true );

							if ( $downloads >= 50 ) break;
						}
					}

					if ( $downloads >= 50 ) {
						set_badge_id( $user_ID, $badge_ID, true );

						$logs .= 'add';
					}
					else {
						delete_badge_id( $user_ID, $badge_ID, true );

						$logs .= 'delete';
					}
				}


				// Badge 7 - Conversationalist!
				$badge_ID = get_badge_id( 'conversationalist' );

				if ( ! empty( $badge_ID ) ) {
					$logs .= PHP_EOL . ' - 7) conversationalist | ';

					$comments_count = get_comments( [
						'author__in' => [$user_ID],
						'count'      => true,
						'status'     => 'approve'
					] );

					if ( $comments_count >= 5 ) {
						set_badge_id( $user_ID, $badge_ID, true );

						$logs .= 'add';
					}
					else {
						delete_badge_id( $user_ID, $badge_ID, true );

						$logs .= 'delete';
					}
				}


				// Badge 8 - Active Community Member!
				$badge_ID = get_badge_id( 'active-community-member' );

				if ( ! empty( $badge_ID ) ) {
					$logs .= PHP_EOL . ' - 8) active-community-member | ';

					$join_IDs = get_followed_posts( ['theme', 'network'], $user_ID );

					if ( count( $join_IDs ) >= 5 ) {
						set_badge_id( $user_ID, $badge_ID, true );

						$logs .= 'add';
					}
					else {
						delete_badge_id( $user_ID, $badge_ID, true );

						$logs .= 'delete';
					}
				}
			}



			// Logs Output
			?><tr><td><pre><?php echo $logs; ?></pre></td></tr><?php
		}
	}

	// Styles
	?>
	<style>
		figure {
			margin: 0;
		}
		img {
			max-width: 500px;
		}
		pre {
			white-space: pre-line;
		}
		table{
			width: 100%;
			border-collapse: collapse;
		}
		td{
			width: 100%;
			vertical-align: top;
			padding: 20px;
			border: 1px solid #CCC;
		}
	</style>
	<?php

	if ( empty( $include ) ) {
		if ( ! empty( $user_query->results ) ) {
			?>
				<script type="text/javascript">
					window.onload = function() {
						setTimeout(function() {
							window.location = '<?php echo get_home_url( null, '?import=update-badges&key=013b0f890d204a522a7e462d1dfa93e5&offset=' . ( $offset + $number ) ); ?>';
						}, 0 );
					};
				</script>
			<?php
		}
		else {
			?><tr><td><pre>END !!!</pre></td></tr><?php
		}
	}

	echo '<table>';
	die();
} );