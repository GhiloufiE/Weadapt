<?php

/*

Articles:
http://weadapt/web/?import=aaa_portal_solution&key=013b0f890d204a522a7e462d1dfa93e5&node=75351

*/

if (
	is_admin() ||
	! isset( $_GET['import'] ) ||
	! in_array( $_GET['import'], [
		// 'page',
		// 'article',         // Article
		// 'initiative',      // Theme/Network/Project
		// 'placemarks',      // Case Study
		// 'organisation',    // Organizations
		// 'webform',
		// 'auth_pages',
		// 'article_section',
		// 'related_content',
		// 'forum',
		// 'theme_category',
		// 'landing_page',
		// 'step',
		// 'tool',
		// 'faq_page',
		'aaa_portal_solution' // Solutions
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

	$drupal_DB = new wpdb( 'lenvan_weadapt_2', 'ItRmj6IEM', 'lenvan_weadapt_2', 'lenvan.cal24.pl' ); // $dbuser, $dbpassword, $dbname, $dbhost


	// Get Data
	$import_type = ! empty( $_GET['import'] ) ? esc_attr( $_GET['import'] ) : false;
	$all_nodes   = get_transient( "import_all_nodes_$import_type" );

	if ( false === $all_nodes ) {
		$all_nodes    = [];
		$all_db_nodes = $drupal_DB->get_results("SELECT * FROM node");

		foreach ( $all_db_nodes as $node ) {
			$node_type = ! empty( $node->type ) ? $node->type : false;

			if ( $import_type === $node_type ) {
				$all_nodes[] = (object) [
					'ID'      => ! empty( $node->nid ) ? (int) $node->nid : '',
					'vid'     => ! empty( $node->vid ) ? (int) $node->vid : '',
					'title'   => ! empty( $node->title ) ? $node->title : '',
					'status'  => ! empty( $node->status ) ? (int) $node->status : 0,
					'uid'     => ! empty( $node->uid ) ? (int) $node->uid : '',
					'type'    => ! empty( $node->type ) ? $node->type : '',
					'created' => ! empty( $node->created ) ? $node->created : '',
					'changed' => ! empty( $node->changed ) ? $node->changed : '',
					'comment' => ! empty( $node->comment ) ? (int) $node->comment : 0
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

		$error_logs_temp = $logs;

		// // Post excerpt
		// $post_excerpt = fix_wp_excerpt( get_db_value( 'field_annotation', $node->ID ) );

		// $logs .= '[Post excerpt]:' . PHP_EOL;
		// $logs .= ! empty( $post_excerpt ) ? $post_excerpt : '----';
		// $logs .= PHP_EOL;


		// // URL Alias
		// $url_alias = get_db_node_url_alias( $node->ID );

		// $logs .= '[URL alias]: ';
		// $logs .= ! empty( $url_alias ) ? $url_alias : '----';
		// $logs .= PHP_EOL;

		// if ( ! empty( $url_alias ) ) {
		// 	$url_alias_data = explode( '/', $url_alias );

		// 	$post_name = end($url_alias_data);
		// }
		// else {
		// 	$short_title = get_db_field( 'name', 'value', $node->ID );
		// 	$temp_title  = ! empty( $short_title ) ? $short_title : $node->title;

		// 	$post_name = sanitize_title( wp_strip_all_tags( $temp_title ) );
		// }


		// Isset Post
		$isset_posts = get_posts( array(
			'numberposts'	=> -1,
			'post_type'		=> 'solutions-portal',
			'meta_key'		=> 'old_id',
			'meta_value'	=> $node->ID,
			'post_status'   => 'any'
		) );





		if ( ! empty( $isset_posts ) ) {
			$isset_post = $isset_posts[0];
			$post_ID    = $isset_post->ID;

			// Post ID
			$logs .= '[Post ID]: ' . $post_ID . PHP_EOL;

			// Publish To
			$publish_to = get_db_domain_access( $node->ID );

			if ( ! $debug ) {
				// publish_to
				update_field( 'field_6374a3364bb73', $publish_to, $post_ID );
			}

			$logs .= '[Publish to]: ';
			$logs .= ! empty( $publish_to ) ? implode( ', ', $publish_to ) : '----';
			$logs .= PHP_EOL;
		}













		// if ( ! empty( $isset_posts ) ) {
		// 	$isset_post = $isset_posts[0];
		// 	$post_ID    = $isset_post->ID;

		// 	wp_update_post( wp_slash( [
		// 		'ID'            => $post_ID,
		// 		'post_name'     => $post_name,
		// 		'post_content'  => '',
		// 		'post_status'   => ( ( $node->status !== 1 ) ? 'draft' : 'publish' ),
		// 		'post_author'   => 1,
		// 		'post_type'     => 'solutions-portal',
		// 		'post_excerpt'  => $post_excerpt,
		// 		'post_date'     => date( 'Y-m-d H:i:s', $node->created + 60*60 ),
		// 		'post_date_gmt' => date( 'Y-m-d H:i:s', $node->created + 60*60 )
		// 	] ) );
		// }
		// else {
		// 	$post_data = array(
		// 		'post_title'    => wp_strip_all_tags( $node->title ),
		// 		'post_name'     => $post_name,
		// 		'post_content'  => '',
		// 		'post_status'   => ( ( $node->status !== 1 ) ? 'draft' : 'publish' ),
		// 		'post_author'   => 1,
		// 		'post_type'     => 'solutions-portal',
		// 		'post_excerpt'  => $post_excerpt,
		// 		'post_date'     => date( 'Y-m-d H:i:s', $node->created + 60*60 ),
		// 		'post_date_gmt' => date( 'Y-m-d H:i:s', $node->created + 60*60 )
		// 	);

		// 	if ( ! $debug ) {
		// 		$post_ID = wp_insert_post( wp_slash( $post_data ) );

		// 		// old_id
		// 		update_field( 'field_6461e17777ed7', $node->ID, $post_ID );
		// 	}

		// 	$is_new  = true;
		// }

		// // if ( ! $debug ) {
		// // 	update_post_meta( $post_ID, 'is_new_import', 1);
		// // }


		// // Check Post Name VS URL Alias
		// if ( get_post_status( $post_ID ) === 'publish' ) {
		// 	$post_url = get_permalink( $post_ID );
		// }
		// else {
		// 	global $wpdb;

		// 	$post_url = $wpdb->get_var( "SELECT post_name FROM $wpdb->posts WHERE ID = $post_ID" );
		// 	$post_url = get_home_url() . '/solutions-portal/' . $post_url . '/';
		// }

		// $post_url_alias = rtrim( str_replace( get_home_url() . '/', '', $post_url ), '/' );

		// if ( $url_alias !== $post_url_alias ) {
		// 	$url_logs  = $url_alias . ' (Node ID: ' . $node->ID . ');' . PHP_EOL;
		// 	$url_logs .= $post_url_alias . ' (Post ID: ' . $post_ID . ');' . PHP_EOL;
		// 	$url_logs .= PHP_EOL;

		// 	file_put_contents( '../logs/url-alias/solutions-portal.log', $url_logs, FILE_APPEND );
		// }


		// // Post ID
		// $logs .= '[Post ID]: ' . $post_ID . PHP_EOL;


		// // Is new
		// $logs .= '[Is New]: ';
		// $logs .= ( $is_new ) ? 1 : 0;
		// $logs .= PHP_EOL;


		// // Content
		// $post_content = '';

		// // Content | Body
		// $field_body = get_db_value( 'body', $node->ID );

		// if ( ! empty( $field_body ) ) {
		// 	$post_content .= $field_body;
		// }

		// if ( ! empty( $post_content ) ) {
		// 	$post_content = fix_wp_content( $post_content, $post_ID, $node->ID );

		// 	if ( ! $debug ) {
		// 		wp_update_post( wp_slash( [
		// 			'ID'           => $post_ID,
		// 			'post_content' => $post_content
		// 		] ) );
		// 	}
		// }

		// $logs .= ! empty( $post_content ) ? $post_content : '----';
		// $logs .= PHP_EOL;


		// // Publish To
		// $publish_to = get_db_domain_access( $node->ID );

		// if ( ! $debug ) {
		// 	// publish_to
		// 	update_field( 'field_6374a3364bb73', $publish_to, $post_ID );
		// }

		// $logs .= '[Publish to]: ';
		// $logs .= ! empty( $publish_to ) ? implode( ', ', $publish_to ) : '----';
		// $logs .= PHP_EOL;


		// // Video List
		// $videos = get_db_solution_videos( $node->ID );

		// $logs .= '[Video list]:' . PHP_EOL;

		// if ( ! empty( $videos ) ) {

		// 	if ( ! $debug ) {
		// 		// video_list
		// 		update_field( 'field_6461e1778a788', $videos, $post_ID );
		// 	}

		// 	foreach ( $videos as $key => $video ) {
		// 		$logs .= $key . ') ' . $video['url'] . ' | ' . $video['description'] . PHP_EOL;
		// 	}
		// }
		// else {
		// 	$logs .= '----';
		// }

		// $logs .= PHP_EOL;


		// // Thumbnail
		// $thumbnail_ID  = get_db_field( 'solution_picture_video', 'fid', $node->ID, " AND bundle = '" . $node->type . "'" );
		// $thumbnail_url = get_db_file_managed_url( $thumbnail_ID );
		// $thumbnail_alt = get_db_field( 'solution_image_attribution', 'value', $node->ID, " AND bundle = '" . $node->type . "'" );

		// if ( ! empty( $thumbnail_url ) ) {
		// 	$thumbnail_ID = upload_attachment_from_url( $thumbnail_url, $post_ID, '', $thumbnail_alt, $thumbnail_ID );

		// 	if ( ! $debug ) {
		// 		set_post_thumbnail( $post_ID, $thumbnail_ID );
		// 	}

		// 	// Logs
		// 	$temp_media_logs = PHP_EOL;
		// 	$temp_media_logs .= 'post_ID: ' . $post_ID . PHP_EOL;
		// 	$temp_media_logs .= 'node_ID: ' . $node->ID . PHP_EOL;
		// 	$temp_media_logs .= 'old src: ' . $thumbnail_url . PHP_EOL;
		// 	$temp_media_logs .= 'new src: ' . ( ! empty( $thumbnail_ID ) ? wp_get_attachment_image_url( $thumbnail_ID, 'full' ) : 'Error !!!' ) . PHP_EOL;

		// 	$media_logs['thumbnail'] = $temp_media_logs;

		// 	if ( empty( $thumbnail_ID ) ) {
		// 		$error_logs .= PHP_EOL . 'Content Image:' . PHP_EOL;
		// 		$error_logs .= $temp_media_logs;
		// 	}
		// }

		// $logs .= '[Thumbnail]:' . PHP_EOL;
		// $logs .= ! empty( $thumbnail_ID ) ? '<img src="' . wp_get_attachment_image_url( $thumbnail_ID, 'full' ) . '">' :'----';
		// $logs .= PHP_EOL;


		// // Status
		// $status = get_db_field( 'solution_status', 'value', $node->ID, " AND bundle = '" . $node->type . "'" );

		// $logs .= '[Status]: ';
		// $logs .= ! empty( $status ) ? $status : '----';
		// $logs .= PHP_EOL;

		// if ( ! $debug ) {
		// 	// status
		// 	update_field( 'field_6463226facbf4', $status, $post_ID );
		// }


		// // Users
		// $people = [
		// 	'contributors' => [],
		// ];

		// foreach( [
		// 	'authors'   => 'contributors'
		// ] as $drupal_key => $wp_key ) {
		// 	$users     = get_user_ids_by_target_ids( $drupal_key, $node->ID );
		// 	$users_IDs = [];

		// 	$logs .= '[' . ucfirst( $wp_key ) . ']: ';

		// 	if ( ! empty( $users ) ) {
		// 		$temp_logs = [];

		// 		foreach ( $users as $key => $author ) {
		// 			$users_IDs[] = $author['wp_id'];
		// 			$temp_logs[] = $author['old_id'] . ' ' . $author['email'] . ' (' . $author['wp_id'] . ')';
		// 		}

		// 		$logs .= implode( ', ', $temp_logs );

		// 		$people[$wp_key] = $users_IDs;
		// 	}
		// 	else {
		// 		$logs .= '----';
		// 	}

		// 	$logs .= PHP_EOL;
		// }

		// if ( ! $debug ) {
		// 	// people
		// 	update_field( 'field_646323702c17d', $people, $post_ID );
		// }


		// // Keywords
		// $keywords_IDs  = [];
		// $keywords_logs = [];
		// $keywords      = get_db_terms( 'keywords', $node->ID );

		// if ( ! empty( $keywords ) ) {
		// 	foreach ( $keywords as $temp_term_name ) {
		// 		if ( ! empty( trim( $temp_term_name ) ) ) {
		// 			$term_ID = 0;
		// 			$term    = get_term_by( 'name', $temp_term_name, 'tags' );

		// 			if ( empty( $term ) ) {
		// 				if ( ! $debug ) {
		// 					$term    = wp_insert_term( $temp_term_name, 'tags' );

		// 					if( is_wp_error( $term ) ){
		// 						s( $term->get_error_message() );
		// 					}
		// 					$term_ID = $term['term_id'];
		// 				}
		// 			}
		// 			elseif ( $term_ID === 0 ) {
		// 				$term_ID = $term->term_id;
		// 			}

		// 			$keywords_IDs[]  = $term_ID;
		// 			$keywords_logs[] = $temp_term_name . ' (' . $term_ID . ')';
		// 		}
		// 	}

		// 	if ( ! $debug ) {
		// 		wp_set_post_terms( $post_ID, $keywords_IDs, 'tags' );
		// 	}
		// }

		// $logs .= '[Keywords]: ';
		// $logs .= ! empty( $keywords_logs ) ? implode( ', ', $keywords_logs ) : '----';
		// $logs .= PHP_EOL;


		// // Relevant
		// $relevant = [
		// 	'organizations'      => [],
		// ];


		// // Participating/Implementing organisations
		// $organisations = get_post_ids_by_target_ids( 'organisations', $node->ID, ['organisation'] );

		// if ( ! empty( $organisations ) ) {
		// 	$relevant['organizations'] = array_keys( $organisations );
		// }

		// $logs .= '[Organisations]: ';
		// $logs .= ! empty( $organisations ) ? implode(', ', array_map(function ($key, $value) {
		// 	return $value . ' (' . $key . ')';
		// }, array_keys($organisations), $organisations)) : '----';
		// $logs .= PHP_EOL;


		// if ( ! $debug ) {
		// 	// relevant
		// 	update_field( 'field_64632e89d3ce3', $relevant, $post_ID );
		// }


		// // Location | Location
		// $locations = [];

		// if ( ! empty( $location = get_db_field( 'solution_location', 'value', $node->ID, " AND bundle = '" . $node->type . "'" ) ) ) {
		// 	$locations[] = $location;
		// }

		// if ( ! empty( get_db_field( 'multiple_countries', 'value', $node->ID, " AND bundle = '" . $node->type . "'" ) ) ) {
		// 	$other_countries = $drupal_DB->get_results("SELECT * FROM field_data_field_other_countries WHERE entity_id LIKE '$node->ID'  AND bundle = '" . $node->type . "'");

		// 	if ( ! empty( $other_countries ) ) {
		// 		foreach ( $other_countries as $country_value ) {
		// 			if ( ! empty( $country_value->field_other_countries_value ) ) {
		// 				$locations[] = $country_value->field_other_countries_value;
		// 			}
		// 		}
		// 	}
		// }

		// $logs .= '[Location]: ';
		// $logs .= ! empty( $locations ) ? implode( ', ', $locations ) : '----';
		// $logs .= PHP_EOL;


		// if ( ! $debug ) {
		// 	// locations
		// 	update_field( 'field_64638da2492f5', $locations, $post_ID );
		// }


		// // Location | Central location
		// $logs .= '[Central location]:' . PHP_EOL;
		// $map = [
		// 	'zoom' => 14
		// ];

		// $lat = trim( get_db_field( 'location', 'lat', $node->ID, " AND bundle = '" . $node->type . "'" ) );
		// $lng = trim( get_db_field( 'location', 'lng', $node->ID, " AND bundle = '" . $node->type . "'" ) );

		// if ( ! empty( $lat ) && ! empty( $lng ) ) {
		// 	$map['lat'] = $lat;
		// 	$map['lng'] = $lng;

		// 	$response = wp_remote_get( "https://maps.googleapis.com/maps/api/geocode/json?latlng={$lat},{$lng}&key=AIzaSyB-9icjn5KC37L9xV1tof4BGN3xwHUdINg", array('timeout' => 5));

		// 	if($response['response']['code'] == 200) {
		// 		$api_response = json_decode(wp_remote_retrieve_body($response), true);

		// 		if ( ! empty( $api_response['results'][0]['formatted_address'] ) ) {
		// 			$map['address'] = $api_response['results'][0]['formatted_address'];
		// 		}
		// 	}
		// }

		// foreach ( $map as $key => $value ) {
		// 	$logs .= ' - [' . $key . ']: ' . $value . PHP_EOL;
		// }
		// $logs .= PHP_EOL;

		// if ( ! $debug ) {
		// 	// central_location
		// 	update_field( 'field_64638ef0492f9', $map, $post_ID );
		// }


		// // Fields
		// foreach ([
		// 	'field_64639c948c391' => 'multiple_locations', // multiple_locations
		// 	'field_64639cc58c392' => 'mountain_range', // mountain_range
		// 	'field_64638eaf492f7' => 'region_province', // region_province
		// 	'field_64638ed2492f8' => 'location_main_site', // site_locations

		// 	'field_64646317ed2e0' => 'area_covered', // area_covered
		// 	'field_64646361ed2e3' => 'ecosystem_other', // ecosystem_other
		// 	'field_64646396ed2e5' => 'solution_type_other', // solution_type_other

		// 	'field_6464a06deda1b' => 'sectors_other', // sectors_other
		// 	'field_6464a7ab72341' => 'climate_impact_other', // climate_impact_other
		// 	'field_6464ab1bb6772' => 'benefit_other', // benefit_other
		// 	'field_6464ab886bda9' => 'cobenefits_other', // co_benefit_other
		// 	'field_6464ab9a6bdaa' => 'beneficiaries_outcomes', // beneficiaries

		// 	'field_6464c652785a5' => 'planning_implementation', // planning_implementation
		// 	'field_6464c672785a6' => 'implementation_start_date', // implementation_start_date
		// 	'field_6464c6b9785a7' => 'implementation_end_date', // implementation_end_date
		// 	'field_6464c6cd785a8' => 'implementation_date_other', // implementation_date_other
		// 	'field_6464c6f2785a9' => 'financed_by', // financed_by
		// 	'field_6464c707785aa' => 'innovation', // innovation

		// 	'field_6464ca31a9fcd' => 'performance_evaluation', // performance_evaluation
		// 	'field_6464ca74a9fce' => 'longterm_sustain_maintain', // longterm_sustain_maintain

		// 	'field_6464cacdad7e9' => 'knowledge_capacities', // knowledge_capacities
		// 	'field_6464caf8ad7ea' => 'knowledge_importance', // knowledge_importance
		// 	'field_6464cb38ad7eb' => 'technology_capacities', // technology_capacities
		// 	'field_6464cb5aad7ec' => 'technology_importance', // technology_importance
		// 	'field_6464cb7cad7ed' => 'political_legal_capacities', // political_legal_capacities
		// 	'field_6464cba2ad7ee' => 'political_legal_importance', // political_legal_importance
		// 	'field_6464cbc5ad7ef' => 'institutional_capacities', // institutional_capacities
		// 	'field_6464cbe7ad7f0' => 'institutional_importance', // institutional_importance
		// 	'field_6464cc13ad7f1' => 'socio_cultural_capacities', // socio_cultural_capacities
		// 	'field_6464cc3cad7f2' => 'socio_cultural_importance', // socio_cultural_importance

		// 	'field_6467113a1b34f' => 'barriers_adverse_effects', // barriers_adverse_effects
		// 	'field_646711bfae78e' => 'transformation_future', // transformation_future
		// 	'field_646711f0ae78f' => 'upscaling_replication', // upscaling_replication

		// 	'field_64671268f9907' => 'acknowledgments', // acknowledgments
		// 	'field_6467128df9908' => 'anything_else', // anything_else
		// 	'field_646712b1f9909' => 'institutional_contacts', // institutional_contacts,

		// 	'field_64671be22ef49' => 'key_references_links', // key_references_links

		// 	'field_64671d5fab7d8' => 'name', // name
		// 	'field_64671d76ab7d9' => 'email_address', // email
		// 	'field_64671d83ab7da' => 'name_of_institution', // institution
		// 	'field_64671d9dab7db' => 'country', // country
		// ] as $wp_key => $drupal_key ) {
		// 	$value = wp_kses_post( fix_wp_content_media_only( get_db_field( $drupal_key, 'value', $node->ID, " AND bundle = '" . $node->type . "'" ) ) );

		// 	$logs .= '[' . $drupal_key . ']: ';
		// 	$logs .= ! empty( $value ) ? $value : '----';
		// 	$logs .= PHP_EOL;

		// 	if ( ! $debug ) {
		// 		update_field( $wp_key, $value, $post_ID );
		// 	}
		// }


		// // Taxonomies
		// foreach ( [
		// 	'solution-scale' => [
		// 		'solution_scale', // db field
		// 		'solution_scale'  // form_key field
		// 	],
		// 	'solution-ecosystem-type' => [
		// 		'ecosystem_type',
		// 		'mountain_ecosystem_type'
		// 	],
		// 	'solution-type' => [
		// 		'solution_type',
		// 		'solution_types'
		// 	],
		// 	'solution-sector' => [
		// 		'sectors',
		// 		'sectors'
		// 	],
		// 	'solution-climate-impact' => [
		// 		'climate_impact',
		// 		'climate_impacts_addressed'
		// 	],
		// 	'solution-climate-timescale' => [
		// 		'climate_impact_timescale',
		// 		'climate_impact_time_scales'
		// 	],
		// 	'solution-benefit' => [
		// 		'benefit_main',
		// 		'main_benefit_associated_with_the_solution_implementation'
		// 	],
		// 	'solution-co-benefit' => [
		// 		'cobenefits',
		// 		'co_benefits'
		// 	],
		// 	'solution-addressed-target' => [
		// 		'targets_addressed',
		// 		'sendai_framework_targets_addressed'
		// 	],
		// 	'solution-addressed-sdg' => [
		// 		'sdgs_addressed',
		// 		'sustainable_development_goals_addressed_1___17'
		// 	]
		// ] as $wp_key => $drupal_key ) {
		// 	$tax_IDs   = [];
		// 	$tax_logs  = [];
		// 	$tax_terms = get_db_webform_terms( $drupal_key[0], $drupal_key[1], $node->ID );

		// 	if ( ! empty( $tax_terms ) ) {
		// 		foreach ( $tax_terms as $temp_term_name ) {
		// 			if ( ! empty( trim( $temp_term_name ) ) ) {
		// 				$term_ID = 0;
		// 				$term    = get_term_by( 'name', $temp_term_name, $wp_key );

		// 				if ( empty( $term ) ) {
		// 					if ( ! $debug ) {
		// 						$term    = wp_insert_term( $temp_term_name, $wp_key );

		// 						if( is_wp_error( $term ) ){
		// 							s( $term->get_error_message() );
		// 						}
		// 						$term_ID = $term['term_id'];
		// 					}
		// 				}
		// 				elseif ( $term_ID === 0 ) {
		// 					$term_ID = $term->term_id;
		// 				}

		// 				$tax_IDs[]  = $term_ID;
		// 				$tax_logs[] = $temp_term_name . ' (' . $term_ID . ')';
		// 			}
		// 		}

		// 		if ( ! $debug ) {
		// 			wp_set_post_terms( $post_ID, $tax_IDs, $wp_key );

		// 			$field_data = acf_get_field( str_replace( '-', '_', $wp_key ) );

		// 			if ( ! empty( $field_data['key'] ) ) {
		// 				update_field( $field_data['key'], array_map('strval', $tax_IDs), $post_ID );
		// 			}
		// 		}
		// 	}

		// 	$logs .= '[' . $wp_key . ']: ';
		// 	$logs .= ! empty( $tax_logs ) ? implode( ', ', $tax_logs ) : '----';
		// 	$logs .= PHP_EOL;
		// }


		// // Documents
		// $documents_list = [];

		// $document_fid = get_db_field( 'document', 'fid', $node->ID, " AND bundle = '" . $node->type . "'" );

		// $logs .= '[Featured Document]: ';

		// if ( ! empty( $document_fid ) ) {
		// 	$document_url  = get_db_file_managed_url( $document_fid );

		// 	if ( ! empty( $document_url ) ) {
		// 		$document_desc = get_db_field( 'document', 'description', $node->ID, " AND bundle = '" . $node->type . "'" );
		// 		$document_ID   = upload_attachment_from_url( $document_url, $post_ID, $document_desc, '', $document_fid );

		// 		$documents_list[] = [
		// 			'file'        => $document_ID,
		// 			'description' => $document_desc,
		// 		];

		// 		// Logs
		// 		$temp_media_logs = PHP_EOL;
		// 		$temp_media_logs .= 'post_ID: ' . $post_ID . PHP_EOL;
		// 		$temp_media_logs .= 'node_ID: ' . $node->ID . PHP_EOL;
		// 		$temp_media_logs .= 'old src: ' . $document_url . PHP_EOL;
		// 		$temp_media_logs .= 'new src: ' . ( ! empty( $document_ID ) ? wp_get_attachment_url( $document_ID ) : 'Error !!!' ) . PHP_EOL;

		// 		$media_logs['document'] = $temp_media_logs;

		// 		if ( empty( $document_ID ) ) {
		// 			$error_logs .= PHP_EOL . 'Featured Document:' . PHP_EOL;
		// 			$error_logs .= $temp_media_logs;
		// 		}
		// 	}
		// }

		// $logs .= ! empty( $document_ID ) ? $document_ID . ' (' . $document_desc . ')' : '----';
		// $logs .= PHP_EOL;


		// $supporting_documents = get_db_target_ids( 'supporting_documents', $node->ID, '_fid' );

		// if ( ! empty( $supporting_documents ) ) {
		// 	foreach ( $supporting_documents as $document_key => $document_fid ) {
		// 		$logs .= '[Supporting Document ' . $document_key . ']: ';

		// 		if ( ! empty( $document_fid ) ) {
		// 			$document_url  = get_db_file_managed_url( $document_fid );

		// 			if ( ! empty( $document_url ) ) {
		// 				$document_desc = get_db_field( 'supporting_documents', 'description', $node->ID, " AND field_supporting_documents_fid = '" . $document_fid . "'" );
		// 				$document_ID   = upload_attachment_from_url( $document_url, $post_ID, $document_desc, '', $document_fid );

		// 				$documents_list[] = [
		// 					'file'        => $document_ID,
		// 					'description' => $document_desc,
		// 				];

		// 				// Logs
		// 				$temp_media_logs = PHP_EOL;
		// 				$temp_media_logs .= 'post_ID: ' . $post_ID . PHP_EOL;
		// 				$temp_media_logs .= 'node_ID: ' . $node->ID . PHP_EOL;
		// 				$temp_media_logs .= 'old src: ' . $document_url . PHP_EOL;
		// 				$temp_media_logs .= 'new src: ' . ( ! empty( $document_ID ) ? wp_get_attachment_url( $document_ID ) : 'Error !!!' ) . PHP_EOL;

		// 				$media_logs['supporting_documents_' . $document_key] = $temp_media_logs;

		// 				if ( empty( $document_ID ) ) {
		// 					$error_logs .= PHP_EOL . 'Supporting Document ' . $document_key . ':' . PHP_EOL;
		// 					$error_logs .= $temp_media_logs;
		// 				}
		// 			}
		// 		}

		// 		$logs .= ! empty( $document_ID ) ? $document_ID . ' (' . $document_desc . ')' : '----';
		// 		$logs .= PHP_EOL;
		// 	}
		// }

		// if ( ! $debug ) {
		// 	// document_list
		// 	update_field( 'field_64671341a1efa', $documents_list, $post_ID );
		// }


		// // Links List
		// $links_list      = [];
		// $temp_links_list = [];
		// $links_results = $drupal_DB->get_results("SELECT * FROM field_data_field_solution_links WHERE entity_id LIKE '$node->ID'");

		// if ( ! empty( $links_results ) ) {
		// 	foreach ( $links_results as $result ) {
		// 		if ( ! empty( $result->field_solution_links_url ) || ! empty( $result->field_solution_links_title ) ) {
		// 			if ( isset( $result->delta ) ) {
		// 				$delta          = (int) $result->delta;
		// 				$links_list[$delta] = [
		// 					'url'         => $result->field_solution_links_url,
		// 					'description' => $result->field_solution_links_title
		// 				];
		// 			}
		// 			else {
		// 				$temp_links_list[] = [
		// 					'url'         => $result->field_solution_links_url,
		// 					'description' => $result->field_solution_links_title
		// 				];
		// 			}
		// 		}
		// 	}
		// }

		// $all_links = array_merge( $links_list, $temp_links_list );

		// $logs .= '[Links List]:' . PHP_EOL;

		// if ( ! empty( $all_links ) ) {

		// 	if ( ! $debug ) {
		// 		// links_list
		// 		update_field( 'field_6467138ba1efd', $all_links, $post_ID );
		// 	}

		// 	foreach ( $all_links as $key => $link ) {
		// 		$logs .= $key . ') ' . $link['url'] . ' | ' . $link['description'] . PHP_EOL;
		// 	}
		// }
		// else {
		// 	$logs .= '----';
		// }

		// $logs .= PHP_EOL;



		// // Logs Report
		// if ( ! empty( $logs ) ) {
		// 	$logs .= PHP_EOL . "-------------------------" . PHP_EOL . PHP_EOL;

		// 	file_put_contents( '../logs/' . $import_type . '/' . get_logs_file_name( $i, 'logs' ), $logs, FILE_APPEND );
		// }

		// // Logs Error Report
		// if ( ! empty( $error_logs ) ) {
		// 	$error_logs = $error_logs_temp . $error_logs;

		// 	$error_logs .= PHP_EOL . "-------------------------" . PHP_EOL . PHP_EOL;

		// 	file_put_contents( '../logs/errors/' . $import_type . '.log', $error_logs, FILE_APPEND );
		// }

		// // Logs Media
		// if ( ! empty( $media_logs ) ) {
		// 	foreach ( $media_logs as $media_log ) {
		// 		$media_log .= PHP_EOL . "-------------------------" . PHP_EOL;

		// 		file_put_contents( '../logs/media/' . $import_type . '.log', $media_log, FILE_APPEND );
		// 	}
		// }


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

	s($error_logs);
	s($media_logs);
	if ( ! isset( $_GET['node'] ) ) {
		?>
			<script type="text/javascript">
				window.onload = function() {
					setTimeout(function() {
						window.location = '<?php echo get_home_url( null, '?import=aaa_portal_solution&key=013b0f890d204a522a7e462d1dfa93e5&item=' . $i ); ?>';
					}, 0 );
				};
			</script>
		<?php
	}

	die();
} );