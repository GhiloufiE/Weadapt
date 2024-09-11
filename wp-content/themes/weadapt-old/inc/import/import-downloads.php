<?php

/*

Downloads:
http://weadapt/web/?import=downloads&key=013b0f890d204a522a7e462d1dfa93e5
http://weadapt/web/?import=downloads&key=013b0f890d204a522a7e462d1dfa93e5&node=62796


*/

if (
	is_admin() ||
	! isset( $_GET['import'] ) ||
	! in_array( $_GET['import'], [
		'downloads',
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
		$all_nodes = $drupal_DB->get_results("SELECT fid, COUNT(*) as row_count FROM download_count GROUP BY fid");

		set_transient( "import_all_nodes_$import_type", $all_nodes, DAY_IN_SECONDS );
	}
	if ( empty( $all_nodes ) ) return;


	// Import
	$loop_start = ! empty( $_GET['item'] ) ? intval( $_GET['item'] ) : 1;
	$loop_end   = isset( $_GET['node'] ) ? count($all_nodes) : ($loop_start + 100);

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
			if ( intval( $node->fid ) !== intval( $_GET['node'] ) ) continue;
		}


		// Logs
		$logs .= '[Item]: ' . $i . '/' . count( $all_nodes ) . PHP_EOL;
		$logs .= '[Old ID]: ' . intval( $node->fid ) . PHP_EOL;
		$logs .= '[Count]: ' . intval( $node->row_count ) . PHP_EOL;

		$error_logs_temp = $logs;



		$posts = get_posts( array(
			'post_type'  => 'attachment',
			'fields'     => 'IDs',
			'meta_query' => array(
				array(
					'key'     => 'old_id',
					'value'   => intval( $node->fid ),
					'compare' => '=',
				),
			),
		) );

		if ( ! empty( $post = $posts[0] ) ) {
			$attachment_ID  = $post->ID;
			$download_count = (int) get_post_meta( $attachment_ID, '_download_count', true );
			$new_count      = $download_count + intval( $node->row_count );

			$logs .= '[Media ID]: ' . $attachment_ID . PHP_EOL;
			$logs .= '[WP Count]: ' . $download_count . PHP_EOL;
			$logs .= '[New Count]: ' . $new_count . PHP_EOL;

			update_post_meta( $attachment_ID, '_download_count', $new_count );
		}


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
						window.location = '<?php echo get_home_url( null, '?import=downloads&key=013b0f890d204a522a7e462d1dfa93e5&item=' . $i ); ?>';
					}, 0 );
				};
			</script>
		<?php
	}

	die();
} );