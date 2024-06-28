<?php

/*

Articles:
http://weadapt/web/?import=comments&key=013b0f890d204a522a7e462d1dfa93e5


*/

if (
	is_admin() ||
	! isset( $_GET['import'] ) ||
	! in_array( $_GET['import'], [
		'comments',
	] ) ||
	! isset( $_GET['key'] ) ||
	$_GET['key'] != '013b0f890d204a522a7e462d1dfa93e5'
) return;


add_action( 'init', function() {

	// Database
	global $debug;
	global $media_debug;
	global $media_logs;
	global $error_logs;
	global $drupal_DB;

	$drupal_DB = new wpdb( 'lenvan_weadapt_3', 'ItRmj6IEM', 'lenvan_weadapt_3', 'lenvan.cal24.pl' ); // $dbuser, $dbpassword, $dbname, $dbhost


	// Get Data
	$import_type = ! empty( $_GET['import'] ) ? esc_attr( $_GET['import'] ) : false;
	$all_nodes   = get_transient( "import_all_nodes_$import_type" );

	if ( false === $all_nodes ) {
		$all_nodes = $drupal_DB->get_results("SELECT * FROM comment ORDER BY created");

		set_transient( "import_all_nodes_$import_type", $all_nodes, DAY_IN_SECONDS );
	}
	if ( empty( $all_nodes ) ) return;


	// Import
	$loop_start = ! empty( $_GET['item'] ) ? intval( $_GET['item'] ) : 1;
	$loop_end   = isset( $_GET['node'] ) ? count($all_nodes) : ($loop_start + 5);

	for ( $i = $loop_start; $i <= $loop_end; $i++ ) {
		if ( empty( $all_nodes[$i-1] ) ) {
			echo 'END !!!';
			die();
		}

		// Variables
		$logs        = '';
		$error_logs  = '';
		$debug       = false;
		$media_debug = false;
		$node        = $all_nodes[$i-1];


		// Temp
		if ( isset( $_GET['node'] ) ) {
			if ( $node->ID !== intval( $_GET['node'] ) ) continue;
		}


		// Logs
		$logs .= '[Item]: ' . $i . '/' . count( $all_nodes ) . PHP_EOL;
		$logs .= '[Node ID]: ' . $node->cid . PHP_EOL;
		$logs .= '[Status]: ' . intval( $node->status ) . PHP_EOL;
		$logs .= '[Created]: ' . date( 'Y-m-d H:i:s', $node->created + 60*60 ) . ' (' . $node->created . ')' . PHP_EOL;
		$logs .= '[Changed]: ' . date( 'Y-m-d H:i:s', $node->changed + 60*60 ) . ' (' . $node->changed . ')' . PHP_EOL;

		$error_logs_temp = $logs;


		$comment = [
			'subject' => $node->subject,
			'ID'      => $node->cid,
			'status'  => intval( $node->status ),
			'author'  => $node->uid,
			'created' => $node->created,
			'changed' => $node->changed,
			'ip'      => $node->hostname,
			'post'    => $node->nid,
			'parent'  => $node->pid,
			'content' => fix_wp_excerpt( get_db_value( 'comment_body', $node->cid ) ),
		];


		// Author
		$wp_user       = 0;
		$wp_user_name  = '';
		$wp_user_email = '';
		$user_node     = $drupal_DB->get_row("SELECT * FROM users WHERE uid LIKE '$node->uid'");

		if ( ! empty( $user_node->mail ) ) {
			$isset_user = get_user_by( 'email', esc_attr( $user_node->mail ) );

			if ( ! empty( $isset_user->data ) ) {
				$wp_user       = $isset_user->data->ID;
				$wp_user_name  = $isset_user->data->display_name;
				$wp_user_email = $isset_user->data->user_email;
			}
		}

		$comment['wp_user']       = $wp_user;
		$comment['wp_user_name']  = $wp_user_name;
		$comment['wp_user_email'] = $wp_user_email;


		// Post
		$post_ID     = 0;
		$isset_posts = get_posts( array(
			'numberposts'	=> -1,
			'post_type'		=> ['article', 'course', 'event', 'blog', 'forum', 'case-study', 'solutions-portal'],
			'meta_key'		=> 'old_id',
			'meta_value'	=> $node->nid,
			'post_status'   => 'any'
		) );

		if ( ! empty( $isset_posts ) ) {
			$isset_post = $isset_posts[0];
			$post_ID    = $isset_post->ID;
		}
		$comment['wp_post_id'] = $post_ID;


		$is_new_comment = false;

		$logs .= '[Subject]: ' . $comment['subject'] . PHP_EOL;
		$logs .= '[Author]: ' . $comment['author'] . ' (' . $comment['wp_user_name'] . ' | ' . $comment['wp_user_email'] . ' | ' . $comment['wp_user'] . ')' . PHP_EOL;
		$logs .= '[IP]: ' . $comment['ip'] . PHP_EOL;
		$logs .= '[Post]: ' . $comment['post'] . ' (wp: ' . $comment['wp_post_id'] . ')' . PHP_EOL;

		$comment_ID        = 0;
		$comment_parent_ID = 0;


		$isset_parent_comments = get_comments([
			'meta_key'   => 'old_id',
			'meta_value' => $comment['parent']
		]);

		if ( ! empty( $isset_parent_comments ) ) {
			$isset_parent_comment = $isset_parent_comments[0];
			$comment_parent_ID    = $isset_parent_comment->comment_ID;
		}
		$logs .= '[Parent]: ' . $comment['parent'] . ' (wp: ' . $comment_parent_ID . ')' . PHP_EOL;

		if ( ! empty( $post_ID ) ) {
			$isset_comments    = get_comments([
				'meta_key'   => 'old_id',
				'meta_value' => $comment['ID']
			]);

			if ( ! empty( $isset_comments ) ) {
				$isset_comment = $isset_comments[0];
				$comment_ID    = $isset_comment->comment_ID;

				wp_update_comment([
					'comment_ID'           => $comment_ID,
					'comment_post_ID'      => $comment['wp_post_id'],
					'comment_author'       => $comment['wp_user_name'],
					'comment_author_email' => $comment['wp_user_email'],
					'comment_content'      => $comment['content'],
					'user_ID'              => $comment['wp_user'],
					'comment_approved'     => $comment['status'],
					'comment_author_IP'    => $comment['ip'],
					'comment_date'         => date( 'Y-m-d H:i:s', $comment['created'] + 60*60 ),
					'comment_date_gmt'     => date( 'Y-m-d H:i:s', $comment['created'] + 60*60 ),
					'comment_parent'       => $comment_parent_ID,
					'comment_meta'         => [
						'old_id' => $comment['ID']
					]
				]);

			}
			else {
				$comment_ID = wp_insert_comment( [
					'comment_post_ID'      => $comment['wp_post_id'],
					'comment_author'       => $comment['wp_user_name'],
					'comment_author_email' => $comment['wp_user_email'],
					'comment_content'      => $comment['content'],
					'user_ID'              => $comment['wp_user'],
					'comment_approved'     => $comment['status'],
					'comment_author_IP'    => $comment['ip'],
					'comment_date'         => date( 'Y-m-d H:i:s', $comment['created'] + 60*60 ),
					'comment_date_gmt'     => date( 'Y-m-d H:i:s', $comment['created'] + 60*60 ),
					'comment_parent'       => $comment_parent_ID,
					'comment_meta'         => [
						'old_id' => $comment['ID']
					]
				] );

				$is_new_comment = true;
			}
		}

		$logs .= '[Comment ID]: ' . $comment_ID . PHP_EOL;


		// Is new
		$logs .= '[Is New]: ';
		$logs .= ( $is_new_comment ) ? 1 : 0;
		$logs .= PHP_EOL;

		$logs .= '[Content]:' . PHP_EOL . $comment['content'] . PHP_EOL . PHP_EOL;

		$logs .= PHP_EOL;




		// Logs Report
		if ( ! empty( $logs ) ) {
			$logs .= PHP_EOL . "-------------------------" . PHP_EOL . PHP_EOL;

			file_put_contents( 'logs/' . $import_type . '/' . get_logs_file_name( $i, 'logs' ), $logs, FILE_APPEND );
		}

		// Logs Error Report
		if ( ! empty( $error_logs ) ) {
			$error_logs = $error_logs_temp . $error_logs;

			$error_logs .= PHP_EOL . "-------------------------" . PHP_EOL . PHP_EOL;

			file_put_contents( 'logs/errors/' . $import_type . '.log', $error_logs, FILE_APPEND );
		}

		// Logs Media
		if ( ! empty( $media_logs ) ) {
			foreach ( $media_logs as $media_log ) {
				$media_log .= PHP_EOL . "-------------------------" . PHP_EOL;

				file_put_contents( 'logs/media/' . $import_type . '.log', $media_log, FILE_APPEND );
			}
		}


		// Logs Output
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
				width: 50%;
				vertical-align: top;
				padding: 20px;
				border: 1px solid #CCC;
			}
		</style>
		<table>
			<tr>
				<td><pre><?php echo htmlentities( $logs ); ?></pre></td>
				<td><pre><?php echo $logs; ?></pre></td>
			</tr>
		</table>
		<?php
	}

	// s($error_logs);
	// s($media_logs);
	if ( ! isset( $_GET['node'] ) ) {
		?>
			<script type="text/javascript">
				window.onload = function() {
					setTimeout(function() {
						window.location = '<?php echo get_home_url( null, '?import=comments&key=013b0f890d204a522a7e462d1dfa93e5&item=' . $i ); ?>';
					}, 0 );
				};
			</script>
		<?php
	}

	die();
} );