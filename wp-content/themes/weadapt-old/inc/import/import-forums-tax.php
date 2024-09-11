<?php

/*

Forums:
http://weadapt/web/?import=forums&key=013b0f890d204a522a7e462d1dfa93e5&node=37621

*/

if (
	is_admin() ||
	! isset( $_GET['import'] ) ||
	! in_array( $_GET['import'], [
		'forums'
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
		$vocabulary_ID_result = $drupal_DB->get_col("SELECT vid FROM taxonomy_vocabulary WHERE machine_name = '" . esc_attr( $_GET['import'] ) . "'");
		$vocabulary_ID        = ! empty( $vocabulary_ID_result[0] ) ? intval( $vocabulary_ID_result[0] ) : 0;

		if ( empty( $vocabulary_ID ) ) {
			echo 'EMPTY $vocabulary_ID !!!';
			die();
		}

		$all_tax_nodes    = [];
		$all_tax_db_nodes = $drupal_DB->get_results("SELECT * FROM taxonomy_term_data WHERE vid = $vocabulary_ID ORDER BY weight");

		if ( empty( $all_tax_db_nodes ) ) return;

		foreach ( $all_tax_db_nodes as $tax_node ) {
			$tax_node_ID = ! empty( $tax_node->tid ) ? (int) $tax_node->tid : 0;

			if ( ! empty( $tax_node_ID ) ) {
				$parent_ID_result = $drupal_DB->get_col("SELECT parent FROM taxonomy_term_hierarchy WHERE tid = '" . $tax_node_ID . "'");
				$parent_ID        = ! empty( $parent_ID_result[0] ) ? (int) $parent_ID_result[0] : 0;

				$all_tax_nodes[] = (object) [
					'ID'          => $tax_node_ID,
					'vid'         => ! empty( $tax_node->vid ) ? (int) $tax_node->vid : 0,
					'name'        => ! empty( $tax_node->name ) ? $tax_node->name : '',
					'description' => ! empty( $tax_node->description ) ? $tax_node->description : '',
					'weight'      => ! empty( $tax_node->weight ) ? (int) $tax_node->weight : 0,
					'parent'      => $parent_ID
				];
			}
		}

		$all_nodes = sort_nodes_data_by_parent( $all_tax_nodes );
		$all_nodes = reverse_nodes_data_by_parent( $all_nodes );

		set_transient( "import_all_nodes_$import_type", $all_nodes, DAY_IN_SECONDS );
	}
	if ( empty( $all_nodes ) ) return;



	// Import
	$loop_start = ! empty( $_GET['item'] ) ? intval( $_GET['item'] ) : 1;
	$loop_end   = isset( $_GET['node'] ) ? count($all_nodes) : ($loop_start + 10);

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
		$is_new      = false;
		$post_ID     = 0;
		$parent_ID   = 0;
		$node        = (object) $all_nodes[$i-1];


		// Temp
		if ( isset( $_GET['node'] ) ) {
			if ( $node->ID !== intval( $_GET['node'] ) ) continue;
		}


		// Logs
		$logs .= '[Item]: ' . $i . '/' . count( $all_nodes ) . PHP_EOL;
		$logs .= '[Title]: ' . $node->name . PHP_EOL;
		$logs .= '[Node ID]: ' . $node->ID . PHP_EOL;
		$logs .= '[Description]: ' . $node->description . PHP_EOL;
		$logs .= '[Weight]: ' . $node->weight . PHP_EOL;

		$error_logs_temp = $logs;

		$url_alias = get_db_node_url_alias( $node->ID, 'forum' );

		$logs .= '[URL alias]: ';
		$logs .= ! empty( $url_alias ) ? $url_alias : '----';
		$logs .= PHP_EOL;

		if ( ! empty( $url_alias ) ) {
			$url_alias_data = explode( '/', $url_alias );

			$post_name = end($url_alias_data);
		}
		else {
			$post_name = sanitize_title( wp_strip_all_tags( $node->name ) );
		}
		$logs .= '[Post Name]: ' . $post_name . PHP_EOL;


		// Isset Post
		$isset_posts = get_posts( array(
			'numberposts'	=> -1,
			'post_type'		=> 'forums',
			'meta_key'		=> 'old_id',
			'meta_value'	=> $node->ID,
			'post_status'   => 'any'
		) );

		if ( ! empty( $isset_posts ) ) {
			$isset_post = $isset_posts[0];
			$post_ID    = $isset_post->ID;

			wp_update_post( wp_slash( [
				'ID'            => $post_ID,
				'post_name'     => $post_name,
				'post_content'  => '',
				'post_status'   => 'publish',
				'post_author'   => 1,
				'post_type'     => 'forums',
				'post_excerpt'  => fix_wp_excerpt( $node->description ),
			] ) );
		}
		else {
			$post_data = array(
				'post_title'    => wp_strip_all_tags( $node->name ),
				'post_name'     => $post_name,
				'post_content'  => '',
				'post_status'   => 'publish',
				'post_author'   => 1,
				'post_type'     => 'forums',
				'post_excerpt'  => fix_wp_excerpt( $node->description ),
			);

			if ( ! $debug ) {
				$post_ID = wp_insert_post( wp_slash( $post_data ) );

				// old_id
				update_field( 'field_6537882a2f5f3', $node->ID, $post_ID );
			}

			$is_new  = true;
		}

		if ( ! $debug ) {
			// weight
			update_field( 'field_653a21446ff3f', $node->weight, $post_ID );
		}


		// Post ID
		$logs .= '[Post ID]: ' . $post_ID . PHP_EOL;


		// Is new
		$logs .= '[Is New]: ';
		$logs .= ( $is_new ) ? 1 : 0;
		$logs .= PHP_EOL;


		// Theme/Network
		$theme_network = 0;
		$og_membership = $drupal_DB->get_results("SELECT gid FROM og_membership WHERE etid LIKE '$node->ID' AND entity_type = 'taxonomy_term' AND type = 'og_membership_type_default'");

		if ( ! empty( $og_membership[0]->gid ) ) {
			$isset_themes_networks = get_posts( array(
				'numberposts'	=> -1,
				'post_type'		=> array( 'theme', 'network' ),
				'meta_key'		=> 'old_id',
				'meta_value'	=> $og_membership[0]->gid,
				'post_status'   => 'any'
			) );

			if ( ! empty( $isset_themes_networks ) ) {
				$theme_network = $isset_themes_networks[0]->ID;
			}
		}

		$logs .= '[Theme/Network]: ' . $theme_network . PHP_EOL;

		if ( ! $debug ) {
			// theme_network
			update_field( 'field_653a1acc46a79', $theme_network, $post_ID );
		}


		// Parent Post
		if ( ! empty( $node->parent ) ) {
			$parent_forums = get_posts( array(
				'numberposts'	=> -1,
				'post_type'		=> 'forums',
				'meta_key'		=> 'old_id',
				'meta_value'	=> $node->parent,
				'post_status'   => 'any'
			) );

			if ( ! empty( $parent_forums ) ) {
				$post_parent = $parent_forums[0];
				$parent_ID   = $post_parent->ID;
			}

			if ( ! $debug ) {
				// parent
				update_field( 'field_6551de730950c', $parent_ID, $post_ID );
			}
		}
		$logs .= '[Parent]: ' . $node->parent . ' (wp: ' . $parent_ID . ')' . PHP_EOL;


		if ( ! $debug ) {
			// publish_to
			update_field( 'field_6374a3364bb73', [1], $post_ID );
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
						window.location = '<?php echo get_home_url( null, '?import=forums&key=013b0f890d204a522a7e462d1dfa93e5&item=' . $i ); ?>';
					}, 0 );
				};
			</script>
		<?php
	}

	die();
} );