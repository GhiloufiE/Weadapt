<?php

/*

Downloads:
http://weadapt/web/?import=citation&key=013b0f890d204a522a7e462d1dfa93e5
http://weadapt/web/?import=citation&key=013b0f890d204a522a7e462d1dfa93e5&node=62796


*/

if (
	is_admin() ||
	! isset( $_GET['import'] ) ||
	! in_array( $_GET['import'], [
		'citation',
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
		$all_nodes      = [];
		$temp_all_nodes = [];
		$all_list_nodes = $drupal_DB->get_results("SELECT * FROM field_data_field_related_content_list");

		if ( ! empty( $all_list_nodes ) ) {
			foreach ($all_list_nodes as $list_node) {
				$body_id   = $list_node->field_related_content_list_target_id;
				$body_data = $drupal_DB->get_results("SELECT * FROM field_data_body WHERE bundle LIKE 'related_content' AND entity_id = $body_id");

				if ( ! empty( $body_data ) ) {
					foreach ( $body_data as $data ) {
						$temp_all_nodes[$list_node->entity_id][] = [
							'node_id'  => $list_node->entity_id,
							'body_id' => $body_id,
							'content' => $data->body_value,
							'delta'   => $data->delta,
						];
					}
				}
			}

			foreach ( $temp_all_nodes as $node_id => $data ) {
				$temp_data = [
					'node_id' => $node_id,
					'content' => [],
				];

				foreach ( $data as $data_item ) {
					$temp_data['content'][] = [
						'body_id' => $data_item['body_id'],
						'content' => $data_item['content'],
						'delta'   => $data_item['delta'],
					];
				}

				$all_nodes[] = $temp_data;
			}
		}

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
			if ( intval( $node['node_id'] ) !== intval( $_GET['node'] ) ) continue;
		}


		// Logs
		$logs .= '[Item]: ' . $i . '/' . count( $all_nodes ) . PHP_EOL;
		$logs .= '[node_id]: ' . intval( $node['node_id'] ) . PHP_EOL;
		$logs .= '[content]: ';

		if ( ! empty( $node['content'] ) ) {
			$logs .= PHP_EOL;

			$content = '';

			foreach ( $node['content'] as $key => $content_item ) {
				$logs .= ' - ' . ($key + 1) . ') [body_id: ' . $content_item['body_id'] . ' | delta: ' . $content_item['delta'] . ']:' . PHP_EOL;

				$temp_content = fix_wp_textarea( $content_item['content'] );

				// Fix last Further Reading
				$temp_content = str_replace( '<h3>Further Reading:</h3>', '', $temp_content );
				$temp_content = str_replace( '<h3>Further reading:</h3>', '', $temp_content );
				$temp_content = str_replace( '<h3>Further Readings:</h3>', '', $temp_content );
				$temp_content = str_replace( '<h3>Further readings:</h3>', '', $temp_content );

				// Fix double PHP_EOL
				$temp_content = str_replace( PHP_EOL . PHP_EOL . PHP_EOL, PHP_EOL . PHP_EOL, $temp_content );

				$logs .= $temp_content . PHP_EOL;

				if ( 0 === $key ) {
					$content .= $temp_content;
				}
				else {
					$content .= PHP_EOL . $temp_content;
				}
			}
		}
		else {
			$logs .= '---';
		}

		$logs .= PHP_EOL;

		$error_logs_temp = $logs;



		$posts = get_posts( array(
			'post_type'  => ['article', 'course', 'event', 'blog', 'case-study'],
			'fields'     => 'IDs',
			'meta_query' => array(
				array(
					'key'     => 'old_id',
					'value'   => intval( $node['node_id'] ),
					'compare' => '=',
				),
			),
		) );

		if ( ! empty( $post = $posts[0] ) ) {
			$logs .= '[Post ID]: ' . $post->ID . PHP_EOL;
			$logs .= '[Post Title]: ' . $post->post_title . PHP_EOL;

			// Additional resources
			update_field( 'field_658577c1dbf82', $content, $post->ID );
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
						window.location = '<?php echo get_home_url( null, '?import=citation&key=013b0f890d204a522a7e462d1dfa93e5&item=' . $i ); ?>';
					}, 0 );
				};
			</script>
		<?php
	}

	die();
} );