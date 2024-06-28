<?php

/*

Articles:
http://weadapt/web/?import=forum&key=013b0f890d204a522a7e462d1dfa93e5
http://weadapt/web/?import=forum&key=013b0f890d204a522a7e462d1dfa93e5&node=13206
http://weadapt/web/?import=forum&key=013b0f890d204a522a7e462d1dfa93e5&node=108926

http://weadapt/web/?import=forum&key=013b0f890d204a522a7e462d1dfa93e5&node=64526


*/

if (
	is_admin() ||
	! isset( $_GET['import'] ) ||
	! in_array( $_GET['import'], [
		'forum',
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
		$all_nodes    = [];
		$all_db_nodes = $drupal_DB->get_results("SELECT * FROM node");

		foreach ( $all_db_nodes as $node ) {
			$node_type = ! empty( $node->type ) ? $node->type : false;

			if ( $import_type === $node_type ) {
				$published_result = $drupal_DB->get_col("SELECT published_at FROM publication_date WHERE nid LIKE '$node->nid'");

				$all_nodes[] = (object) [
					'ID'        => ! empty( $node->nid ) ? (int) $node->nid : '',
					'vid'       => ! empty( $node->vid ) ? (int) $node->vid : '',
					'title'     => ! empty( $node->title ) ? $node->title : '',
					'status'    => ! empty( $node->status ) ? (int) $node->status : 0,
					'uid'       => ! empty( $node->uid ) ? (int) $node->uid : '',
					'type'      => ! empty( $node->type ) ? $node->type : '',
					'created'   => ! empty( $node->created ) ? $node->created : '',
					'changed'   => ! empty( $node->changed ) ? $node->changed : '',
					'published' => ! empty( $published_result[0] ) ? $published_result[0] : '',
					'comment'   => ! empty( $node->comment ) ? (int) $node->comment : 0,
					// 'post_type' => get_db_field( 'initiative_type', 'value', $node->nid )
				];
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
		$is_new      = false;
		$post_ID     = 0;
		$node        = $all_nodes[$i-1];
		$post_type   = 'forum';


		// Temp
		if ( isset( $_GET['node'] ) ) {
			if ( $node->ID !== intval( $_GET['node'] ) ) continue;
		}


		// Logs
		$logs .= '[Item]: ' . $i . '/' . count( $all_nodes ) . PHP_EOL;
		$logs .= '[Title]: ' . $node->title . PHP_EOL;
		$logs .= '[Node ID]: ' . $node->ID . PHP_EOL;
		$logs .= '[Status]: ' . ( ( $node->status !== 1 ) ? 'draft' : 'publish' ) . PHP_EOL;
		$logs .= '[Coments]: ' . $node->comment . PHP_EOL;
		$logs .= '[Type]: ' . $node->type . PHP_EOL;
		$logs .= '[Created]: ' . date( 'Y-m-d H:i:s', $node->created + 60*60 ) . ' (' . $node->created . ')' . PHP_EOL;
		$logs .= '[Changed]: ' . date( 'Y-m-d H:i:s', $node->changed + 60*60 ) . ' (' . $node->changed . ')' . PHP_EOL;

		if ( $node->published ) {
			$logs .= '[Published]: ' . date( 'Y-m-d H:i:s', $node->published + 60*60 ) . ' (' . $node->published . ')' . PHP_EOL;
		}

		$error_logs_temp = $logs;


		// URL Alias
		$url_alias = get_db_node_url_alias( $node->ID );

		$logs .= '[URL alias]: ';
		$logs .= ! empty( $url_alias ) ? $url_alias : '----';
		$logs .= PHP_EOL;

		if ( ! empty( $url_alias ) ) {
			$url_alias_data = explode( '/', $url_alias );

			$post_name = end($url_alias_data);
		}
		else {
			$short_title = get_db_field( 'name', 'value', $node->ID );
			$temp_title  = ! empty( $short_title ) ? $short_title : $node->title;

			$post_name = sanitize_title( wp_strip_all_tags( $temp_title ) );
		}


		// Isset Post
		$isset_posts = get_posts( array(
			'numberposts'	=> -1,
			'post_type'		=> $post_type,
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
				'post_status'   => ( ( $node->status !== 1 ) ? 'draft' : 'publish' ),
				'post_author'   => 1,
				'post_type'     => $post_type,
				'post_excerpt'  => '',
				'post_date'     => date( 'Y-m-d H:i:s', $node->created + 60*60 ),
				'post_date_gmt' => date( 'Y-m-d H:i:s', $node->created + 60*60 )
			] ) );
		}
		else {
			$post_data = array(
				'post_title'    => wp_strip_all_tags( $node->title ),
				'post_name'     => $post_name,
				'post_content'  => '',
				'post_status'   => ( ( $node->status !== 1 ) ? 'draft' : 'publish' ),
				'post_author'   => 1,
				'post_type'     => $post_type,
				'post_excerpt'  => '',
				'post_date'     => date( 'Y-m-d H:i:s', $node->created + 60*60 ),
				'post_date_gmt' => date( 'Y-m-d H:i:s', $node->created + 60*60 )
			);

			if ( ! $debug ) {
				$post_ID = wp_insert_post( wp_slash( $post_data ) );

				// old_id
				update_field( 'field_653b5c7e65eb4', $node->ID, $post_ID );
			}

			$is_new  = true;
		}


		// Forum Taxonomy
		$forms_term_id = 0;
		$forms_dp_id   = 0;
		$forum_results = $drupal_DB->get_row("SELECT taxonomy_forums_tid FROM field_data_taxonomy_forums WHERE entity_id LIKE '$node->ID' AND bundle = 'forum'");

		if ( ! empty( $forum_results->taxonomy_forums_tid ) ) {
			$forms_dp_id = $forum_results->taxonomy_forums_tid;

			$parent_forums = get_posts( array(
				'numberposts'	=> -1,
				'post_type'		=> 'forums',
				'meta_key'		=> 'old_id',
				'meta_value'	=> $forms_dp_id,
				'post_status'   => 'any'
			) );

			if ( ! empty( $parent_forums ) ) {
				$parent_forum  = $parent_forums[0];
				$forms_term_id = $parent_forum->ID;
			}
		}

		$logs .= '[Forum ID]: ' . $forms_dp_id . ' (wp: ' . $forms_term_id . ')' . PHP_EOL;

		if ( ! $debug ) {
			// forum
			update_field( 'field_653b5c7e6d5f5', $forms_term_id, $post_ID );
		}


		// Check Post Name VS URL Alias
		if ( get_post_status( $post_ID ) === 'publish' ) {
			$post_url = get_permalink( $post_ID );
		}
		else {
			global $wpdb;

			$post_url = $wpdb->get_var( "SELECT post_name FROM $wpdb->posts WHERE ID = $post_ID" );

			$main_theme_network_ID = get_field( 'relevant_main_theme_network', $post_ID );

			if ( ! empty( $main_theme_network_ID ) ) {
				$main_theme_network_url = $wpdb->get_var( "SELECT post_name FROM $wpdb->posts WHERE ID = $main_theme_network_ID" );

				$post_url = $main_theme_network_url . '/' . $post_url;
			}
			$post_url = get_home_url() . '/knowledge-base/' . $post_url . '/';
		}

		$post_url_alias = rtrim( str_replace( get_home_url() . '/', '', $post_url ), '/' );

		if ( $url_alias !== $post_url_alias ) {
			$url_logs  = $url_alias . ' (Node ID: ' . $node->ID . ');' . PHP_EOL;
			$url_logs .= $post_url_alias . ' (Post ID: ' . $post_ID . ');' . PHP_EOL;
			$url_logs .= PHP_EOL;

			file_put_contents( '../logs/url-alias/' . $post_type . '.log', $url_logs, FILE_APPEND );
		}


		// Post ID
		$logs .= '[Post ID]: ' . $post_ID . PHP_EOL;


		// Is new
		$logs .= '[Is New]: ';
		$logs .= ( $is_new ) ? 1 : 0;
		$logs .= PHP_EOL;


		// Publish To
		$publish_to = get_db_domain_access( $node->ID );

		if ( ! $debug ) {
			// publish_to
			update_field( 'field_6374a3364bb73', $publish_to, $post_ID );
		}

		$logs .= '[Publish to]: ';
		$logs .= ! empty( $publish_to ) ? implode( ', ', $publish_to ) : '----';
		$logs .= PHP_EOL;


		// People
		$people = [
			'creator'      => [],
			'contributors' => [],
			'publisher'    => [],
		];

		$publisher_node = $drupal_DB->get_row("SELECT uid FROM node_revision WHERE nid LIKE '$node->ID'");
		$temp_people    = [
			'creator'      => $node->uid,
			'contributors' => $node->uid,
			'publisher'    => ! empty( $publisher_node->uid ) ? (int) $publisher_node->uid : 0,
		];

		foreach ( $temp_people as $wp_key => $user_ID ) {
			$user_node  = $drupal_DB->get_row("SELECT * FROM users WHERE uid LIKE '$user_ID'");

			$logs .= '[' . ucfirst( $wp_key ) . ']: ' . $user_ID;

			if ( ! empty( $user_node->mail ) ) {
				$logs .= ' ' . $user_node->mail;

				$isset_user = get_user_by( 'email', esc_attr( $user_node->mail ) );

				if ( ! empty( $isset_user->data ) ) {
					$people[$wp_key]  = $isset_user->data->ID;

					$logs .= ' (' . $isset_user->data->ID . ')';
				}
			}
			else {
				$logs .= '----';
			}

			$logs .= PHP_EOL;
		}

		if ( ! $debug ) {
			// people
			update_field( 'field_637f1ee7327b4', $people, $post_ID );
		}


		// Content
		$post_content = get_db_value( 'body', $node->ID );

		$logs .= '[Content]:' . PHP_EOL;

		if ( ! empty( $post_content ) ) {
			$post_content = fix_wp_content( $post_content, $post_ID, $node->ID );

			if ( ! $debug ) {
				wp_update_post( wp_slash( [
					'ID'           => $post_ID,
					'post_content' => $post_content
				] ) );
			}
		}

		$logs .= ! empty( $post_content ) ? $post_content : '----';
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
						window.location = '<?php echo get_home_url( null, '?import=forum&key=013b0f890d204a522a7e462d1dfa93e5&item=' . $i ); ?>';
					}, 0 );
				};
			</script>
		<?php
	}

	die();
} );