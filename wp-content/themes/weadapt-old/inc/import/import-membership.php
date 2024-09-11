<?php

/*

http://weadapt/web/?import=membership&key=013b0f890d204a522a7e462d1dfa93e5

*/

if (
	is_admin() ||
	! isset( $_GET['import'] ) ||
	$_GET['import'] != 'membership' ||
	! isset( $_GET['key'] ) ||
	$_GET['key'] != '013b0f890d204a522a7e462d1dfa93e5'
) return;


add_action( 'init', function() {

	// Database
	global $debug;
	global $drupal_DB;

	$drupal_DB = new wpdb( 'lenvan_weadapt_2', 'ItRmj6IEM', 'lenvan_weadapt_2', 'lenvan.cal24.pl' ); // $dbuser, $dbpassword, $dbname, $dbhost


	// Get Data
	$import_type  = ! empty( $_GET['import'] ) ? esc_attr( $_GET['import'] ) : false;
	$all_data     = get_transient( "import_all_data_$import_type" );

	if ( false === $all_data ) {
		$all_data    = [];
		$all_db_data = $drupal_DB->get_results("SELECT * FROM og_membership");

		foreach ( $all_db_data as $data ) {
			$field_name = ! empty( $data->field_name ) ? $data->field_name : false;

			if ( 'og_user_node' === $field_name ) {
				$all_data[] = (object) [
					'dp_id'   => ! empty( $data->id ) ? (int) $data->id : '',
					'user_id' => ! empty( $data->etid ) ? (int) $data->etid : '',
					'node_id' => ! empty( $data->gid ) ? $data->gid : '',
					'state'   => ! empty( $data->state ) ? (int) $data->state : 0,
					'created' => ! empty( $data->created ) ? $data->created + 60*60 : 0,
				];
			}
		}

		set_transient( "import_all_data_$import_type", $all_data, DAY_IN_SECONDS );
	}

	if ( empty( $all_data ) ) return;



	// Import
	$loop_start = ! empty( $_GET['item'] ) ? intval( $_GET['item'] ) : 1;
	$loop_end   = isset( $_GET['node'] ) ? count($all_data) : ($loop_start + 19);

	for ( $i = $loop_start; $i <= $loop_end; $i++ ) {
		if ( empty( $all_data[$i-1] ) ) {
			echo 'END !!!';
			die();
		}

		// Variables
		$logs        = '';
		$error_logs  = '';
		$debug       = false;
		$node        = $all_data[$i-1];


		// Logs
		$logs .= '[Item]: ' . $i . '/' . count( $all_data ) . PHP_EOL;
		$logs .= '[DP ID]: ' . $node->dp_id . PHP_EOL;
		$logs .= '[User ID]: ' . $node->user_id . PHP_EOL;
		$logs .= '[Node ID]: ' . $node->node_id . PHP_EOL;
		$logs .= '[Created]: ' . date( 'Y-m-d H:i:s', $node->created ) . ' (' . $node->created . ')' . PHP_EOL;

		$error_logs_temp = $logs;


		$user_ID   = 0;
		$post_ID   = 0;
		$post_type = 'post';


		// User
		$dp_user_email = $drupal_DB->get_var("SELECT mail FROM users WHERE uid = {$node->user_id}");

		if ( empty( $dp_user_email ) ) {
			$error_logs .= '$dp_user empty: ' . $node->user_id . PHP_EOL;
		}
		else {
			$isset_user = get_user_by( 'email', $dp_user_email );

			if ( empty( $isset_user->data ) ) {
				$error_logs .= '$isset_user empty: ' . $dp_user_email . PHP_EOL;
			}
			else {
				$user_ID = intval( $isset_user->data->ID );
			}
		}


		// Post
		$isset_posts = get_posts( array(
			'numberposts'	=> -1,
			'post_type'		=> ['theme', 'network'],
			'meta_key'		=> 'old_id',
			'meta_value'	=> $node->node_id,
			'post_status'   => 'any'
		) );

		if ( empty( $isset_posts ) ) {
			$error_logs .= '$isset_posts empty: ' . $node->node_id . PHP_EOL;
		}
		else {
			$isset_post = $isset_posts[0];
			$post_ID    = intval( $isset_post->ID );
			$post_type  = $isset_post->post_type;
		}


		// DB
		if ( ! empty( $user_ID ) && ! empty( $post_ID ) ) {
			global $wpdb;
			global $join_table;

			$exists = $wpdb->get_var( "SELECT COUNT(*) FROM $join_table WHERE join_id = '$post_ID' AND user_id = '$user_ID' and type = '$post_type'" );

			if ( 0 == $exists ) {
				$wpdb->insert( $join_table, [
					'user_id' => trim( $user_ID ),
					'join_id' => trim( $post_ID ),
					'type'    => trim( $post_type ),
					'created' => trim( $node->created )
				] );
			}
			else {
				$error_logs .= '$exists: join_id: ' . $post_ID . '; user_id: ' . $user_ID . '; type: ' . $post_type . ';' . PHP_EOL;
			}
		}

		// Logs Error Report
		if ( ! empty( $error_logs ) ) {
			$error_logs = $error_logs_temp . $error_logs;

			$error_logs .= PHP_EOL . "-------------------------" . PHP_EOL . PHP_EOL;

			file_put_contents( '../logs/errors/' . $import_type . '.log', $error_logs, FILE_APPEND );
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
				<td><pre><?php echo $logs; ?></pre></td>
			</tr>
		</table>
		<?php

		if ( ! empty( $error_logs ) ) {
			s($error_logs);
		}
	}


	if ( ! isset( $_GET['node'] ) ) {
		?>
			<script type="text/javascript">
				window.onload = function() {
					setTimeout(function() {
						window.location = '<?php echo get_home_url( null, '?import=membership&key=013b0f890d204a522a7e462d1dfa93e5&item=' . $i ); ?>';
					}, 0 );
				};
			</script>
		<?php
	}

	die();
} );