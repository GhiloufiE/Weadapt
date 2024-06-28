<?php

/*

Articles:
http://weadapt/web/?import=article&key=013b0f890d204a522a7e462d1dfa93e5&node=110056


*/

if (
	is_admin() ||
	! isset( $_GET['import'] ) ||
	! in_array( $_GET['import'], [
		// 'page',
		'article',            // Article
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
		// 'aaa_portal_solution' // Solutions
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
					'comment' => ! empty( $node->comment ) ? (int) $node->comment : 0,
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


		// Content type
		$content_type = get_db_field( 'w_subtype', 'value', $node->ID );

		$logs .= '[Content type]: ';
		$logs .= ! empty( $content_type ) ? $content_type : '----';
		$logs .= PHP_EOL;


		// Post type
		$post_type = $node->type;

		/*
			--- are articles,
			publication are articles
			article are articles
			news_and_events are events
			event are events
			blog are blog posts
		*/
		switch ( $content_type ) {
			case 'publication':     $post_type = 'article'; break;
			case 'news_and_events': $post_type = 'event'; break;
			case 'event':           $post_type = 'event'; break;
			case 'blog':            $post_type = 'blog'; break;
		}

		$logs .= '[Post type]: ';
		$logs .= ! empty( $post_type ) ? $post_type : '----';
		$logs .= PHP_EOL;


		// // Post excerpt
		// $post_excerpt = fix_wp_excerpt( get_db_field( 'introduction', 'value', $node->ID ) );

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
			'post_type'		=> $post_type,
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
		// 		'post_type'     => $post_type,
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
		// 		'post_type'     => $post_type,
		// 		'post_excerpt'  => $post_excerpt,
		// 		'post_date'     => date( 'Y-m-d H:i:s', $node->created + 60*60 ),
		// 		'post_date_gmt' => date( 'Y-m-d H:i:s', $node->created + 60*60 )
		// 	);

		// 	if ( ! $debug ) {
		// 		$post_ID = wp_insert_post( wp_slash( $post_data ) );

		// 		// old_id
		// 		update_field( 'field_64535c5cef012', $node->ID, $post_ID );
		// 	}

		// 	$is_new  = true;
		// }

		// // if ( ! $debug ) {
		// // 	update_post_meta( $post_ID, 'is_new_import', 1);
		// // }


		// // Relevant
		// $relevant = [
		// 	'main_theme_network' => 0,
		// 	'themes_networks'    => [],
		// 	'organizations'      => [],
		// 	'articles'           => [],
		// ];

		// // Main Theme/Network/Project
		// $logs .= '[Main Theme]: ';

		// $node_access = $drupal_DB->get_results("SELECT gid FROM og_membership WHERE etid LIKE '$node->ID' AND type = 'og_membership_type_default' AND field_name = 'og_group_ref'");

		// if ( ! empty( $node_access[0]->gid ) ) {
		// 	$theme_data = get_post_id_by_node_id( $node_access[0]->gid, ['theme', 'network'] );

		// 	if ( ! empty( $theme_data ) ) {
		// 		$relevant['main_theme_network'] = array_key_first( $theme_data );
		// 	}
		// }

		// $logs .= ! empty( $theme_data ) ? implode(', ', array_map(function ($key, $value) {
		// 	return $value . ' (' . $key . ')';
		// }, array_keys($theme_data), $theme_data)) : '----';
		// $logs .= PHP_EOL;


		// // Relevant other Themes/Networks/Projects
		// $initiatives = get_post_ids_by_target_ids( 'related_initiatives', $node->ID, ['theme', 'network'] );

		// if ( ! empty( $initiatives ) ) {
		// 	$relevant['themes_networks'] = array_keys( $initiatives );
		// }

		// $logs .= '[Relevant Themes/Networks]: ';
		// $logs .= ! empty( $initiatives ) ? implode(', ', array_map(function ($key, $value) {
		// 	return $value . ' (' . $key . ')';
		// }, array_keys($initiatives), $initiatives)) : '----';
		// $logs .= PHP_EOL;


		// // Participating/Implementing organisations
		// $organisations = get_post_ids_by_target_ids( 'related_organisations', $node->ID, ['organisation'] );

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
		// 	update_field( 'field_637f257f6bad4', $relevant, $post_ID );
		// }


		// // Check Post Name VS URL Alias
		// if ( get_post_status( $post_ID ) === 'publish' ) {
		// 	$post_url = get_permalink( $post_ID );
		// }
		// else {
		// 	global $wpdb;

		// 	$post_url = $wpdb->get_var( "SELECT post_name FROM $wpdb->posts WHERE ID = $post_ID" );

		// 	$main_theme_network_ID = get_field( 'relevant_main_theme_network', $post_ID );

		// 	if ( ! empty( $main_theme_network_ID ) ) {
		// 		$main_theme_network_url = $wpdb->get_var( "SELECT post_name FROM $wpdb->posts WHERE ID = $main_theme_network_ID" );

		// 		$post_url = $main_theme_network_url . '/' . $post_url;
		// 	}
		// 	$post_url = get_home_url() . '/knowledge-base/' . $post_url . '/';
		// }

		// $post_url_alias = rtrim( str_replace( get_home_url() . '/', '', $post_url ), '/' );

		// // s($url_alias);
		// // s($post_url_alias);

		// if ( $url_alias !== $post_url_alias ) {
		// 	$url_logs  = $url_alias . ' (Node ID: ' . $node->ID . ');' . PHP_EOL;
		// 	$url_logs .= $post_url_alias . ' (Post ID: ' . $post_ID . ');' . PHP_EOL;
		// 	$url_logs .= PHP_EOL;

		// 	file_put_contents( '../logs/url-alias/' . $post_type . '.log', $url_logs, FILE_APPEND );
		// }


		// // Post ID
		// $logs .= '[Post ID]: ' . $post_ID . PHP_EOL;


		// // Is new
		// $logs .= '[Is New]: ';
		// $logs .= ( $is_new ) ? 1 : 0;
		// $logs .= PHP_EOL;


		// // Event new fields
		// if ( $post_type === 'event' ) {

		// 	// Event Type
		// 	$event_type = get_db_field( 'event_type', 'value',  $node->ID );

		// 	if ( ! empty( $event_type ) ) {
		// 		// Event Type
		// 		update_field( 'field_64a7bd6456f4b', $event_type, $post_ID );
		// 	}

		// 	$logs .= '[Event Type]: ';
		// 	$logs .= ! empty( $event_type ) ? $event_type : '----';
		// 	$logs .= PHP_EOL;


		// 	// Start Date
		// 	$start_date = get_db_field( 'date', 'value', $node->ID );

		// 	if ( ! empty( $start_date ) ) {
		// 		$start_date = DateTime::createFromFormat('Y-m-d H:i:s', $start_date);
		// 		$start_date = $start_date->format('Y-m-d H:i:s');

		// 		// Start Date
		// 		update_field( 'field_64a7b771186eb', $start_date, $post_ID );
		// 	}

		// 	$logs .= '[Start Date]: ';
		// 	$logs .= ! empty( $start_date ) ? $start_date : '----';
		// 	$logs .= PHP_EOL;


		// 	// End Date
		// 	$end_date = get_db_field( 'date', 'value2', $node->ID );

		// 	if ( ! empty( $end_date ) ) {
		// 		$end_date = DateTime::createFromFormat('Y-m-d H:i:s', $end_date);
		// 		$end_date = $end_date->format('Y-m-d H:i:s');

		// 		// End Date
		// 		update_field( 'field_64a7b79c186ec', $end_date, $post_ID );
		// 	}

		// 	$logs .= '[End Date]: ';
		// 	$logs .= ! empty( $end_date ) ? $end_date : '----';
		// 	$logs .= PHP_EOL;


		// 	// Timezone
		// 	$timezone = get_db_field( 'timezone', 'value', $node->ID);

		// 	if ( ! empty( $timezone ) ) {
		// 		// Timezone
		// 		update_field( 'field_64a7b868186ed', $timezone, $post_ID );
		// 	}

		// 	$logs .= '[Timezone]: ';
		// 	$logs .= ! empty( $timezone ) ? $timezone : '----';
		// 	$logs .= PHP_EOL;

		// }


		// // Language
		// $languages_list = ['ab'=>'Abkhazian','aa'=>'Afar','af'=>'Afrikaans','ak'=>'Akan','sq'=>'Albanian','am'=>'Amharic','ar'=>'Arabic','hy'=>'Armenian','as'=>'Assamese','ast'=>'Asturian','av'=>'Avar','ae'=>'Avestan','ay'=>'Aymara','az'=>'Azerbaijani','bm'=>'Bambara','ba'=>'Bashkir','eu'=>'Basque','be'=>'Belarusian','bn'=>'Bengali','bh'=>'Bihari','bi'=>'Bislama','bs'=>'Bosnian','br'=>'Breton','bg'=>'Bulgarian','my'=>'Burmese','km'=>'Cambodian','ca'=>'Catalan','ch'=>'Chamorro','ce'=>'Chechen','ny'=>'Chichewa','zh-hans'=>'Chinese, Simplified','zh-hant'=>'Chinese, Traditional','cv'=>'Chuvash','kw'=>'Cornish','co'=>'Corsican','cr'=>'Cree','hr'=>'Croatian','cs'=>'Czech','da'=>'Danish','nl'=>'Dutch','dz'=>'Dzongkha','en'=>'English','en-gb'=>'English, British','eo'=>'Esperanto','et'=>'Estonian','ee'=>'Ewe','fo'=>'Faeroese','fj'=>'Fiji','fil'=>'Filipino','fi'=>'Finnish','fr'=>'French','fy'=>'Frisian','ff'=>'Fulah','gl'=>'Galician','ka'=>'Georgian','de'=>'German','el'=>'Greek','kl'=>'Greenlandic','gn'=>'Guarani','gu'=>'Gujarati','ht'=>'Haitian Creole','ha'=>'Hausa','he'=>'Hebrew','hz'=>'Herero','hi'=>'Hindi','ho'=>'Hiri Motu','hu'=>'Hungarian','is'=>'Icelandic','ig'=>'Igbo','id'=>'Indonesian','ia'=>'Interlingua','ie'=>'Interlingue','iu'=>'Inuktitut','ik'=>'Inupiak','ga'=>'Irish','it'=>'Italian','ja'=>'Japanese','jv'=>'Javanese','kn'=>'Kannada','kr'=>'Kanuri','ks'=>'Kashmiri','kk'=>'Kazakh','ki'=>'Kikuyu','rw'=>'Kinyarwanda','rn'=>'Kirundi','kv'=>'Komi','kg'=>'Kongo','ko'=>'Korean','ku'=>'Kurdish','kj'=>'Kwanyama','ky'=>'Kyrgyz','lo'=>'Laothian','la'=>'Latin','lv'=>'Latvian','ln'=>'Lingala','lt'=>'Lithuanian','xx-lolspeak'=>'Lolspeak','lg'=>'Luganda','lb'=>'Luxembourgish','mk'=>'Macedonian','mg'=>'Malagasy','ms'=>'Malay','ml'=>'Malayalam','dv'=>'Maldivian','mt'=>'Maltese','gv'=>'Manx','mr'=>'Marathi','mh'=>'Marshallese','mo'=>'Moldavian','mn'=>'Mongolian','mi'=>'Māori','na'=>'Nauru','nv'=>'Navajo','ng'=>'Ndonga','ne'=>'Nepali','nd'=>'North Ndebele','se'=>'Northern Sami','nb'=>'Norwegian Bokmål','nn'=>'Norwegian Nynorsk','oc'=>'Occitan','cu'=>'Old Slavonic','or'=>'Oriya','om'=>'Oromo','os'=>'Ossetian','pi'=>'Pali','ps'=>'Pashto','fa'=>'Persian','pl'=>'Polish','pt-br'=>'Portuguese, Brazil','pt'=>'Portuguese, International','pt-pt'=>'Portuguese, Portugal','pa'=>'Punjabi','qu'=>'Quechua','rm'=>'Rhaeto-Romance','ro'=>'Romanian','ru'=>'Russian','sm'=>'Samoan','sg'=>'Sango','sa'=>'Sanskrit','sc'=>'Sardinian','sco'=>'Scots','gd'=>'Scots Gaelic','sr'=>'Serbian','sh'=>'Serbo-Croatian','st'=>'Sesotho','tn'=>'Setswana','sn'=>'Shona','sd'=>'Sindhi','si'=>'Sinhala','ss'=>'Siswati','sk'=>'Slovak','sl'=>'Slovenian','so'=>'Somali','nr'=>'South Ndebele','es'=>'Spanish','su'=>'Sudanese','sw'=>'Swahili','sv'=>'Swedish','gsw-berne'=>'Swiss German','tl'=>'Tagalog','ty'=>'Tahitian','tg'=>'Tajik','ta'=>'Tamil','tt'=>'Tatar','te'=>'Telugu','th'=>'Thai','bo'=>'Tibetan','ti'=>'Tigrinya','to'=>'Tonga','ts'=>'Tsonga','tr'=>'Turkish','tk'=>'Turkmen','tw'=>'Twi','uk'=>'Ukrainian','ur'=>'Urdu','ug'=>'Uyghur','uz'=>'Uzbek','ve'=>'Venda','vi'=>'Vietnamese','cy'=>'Welsh','wo'=>'Wolof','xh'=>'Xhosa','yi'=>'Yiddish','yo'=>'Yoruba','za'=>'Zhuang','zu'=>'Zulu'];
		// $language_code  = get_db_value( 'field_input_language', $node->ID );

		// if ( ! $debug && ! empty( $languages_list[$language_code] ) ) {
		// 	// language
		// 	update_field( 'field_637f205a88c6b', $languages_list[$language_code], $post_ID );
		// }

		// $logs .= '[Language]: ';
		// $logs .= ! empty( $languages_list[$language_code] ) ? $language_code . ' | ' . $languages_list[$language_code] : '----';
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


		// // Categories
		// $categories_IDs  = [];
		// $categories_logs = [];
		// $categories      = get_db_terms( 'category', $node->ID );

		// if ( ! empty( $categories ) ) {
		// 	foreach ( $categories as $temp_term_name ) {
		// 		if ( ! empty( trim( $temp_term_name ) ) ) {
		// 			$term_ID = 0;
		// 			$term    = get_term_by( 'name', $temp_term_name, 'category' );

		// 			if ( empty( $term ) ) {
		// 				if ( ! $debug ) {
		// 					$term    = wp_insert_term( $temp_term_name, 'category' );

		// 					if( is_wp_error( $term ) ){
		// 						s( $term->get_error_message() );
		// 					}
		// 					$term_ID = $term['term_id'];
		// 				}
		// 			}
		// 			elseif ( $term_ID === 0 ) {
		// 				$term_ID = $term->term_id;
		// 			}

		// 			$categories_IDs[]  = $term_ID;
		// 			$categories_logs[] = $temp_term_name . ' (' . $term_ID . ')';
		// 		}
		// 	}

		// 	if ( ! $debug ) {
		// 		wp_set_post_terms( $post_ID, $categories_IDs, 'category' );
		// 	}
		// }

		// $logs .= '[Categories]: ';
		// $logs .= ! empty( $categories_logs ) ? implode( ', ', $categories_logs ) : '----';
		// $logs .= PHP_EOL;


		// // Users
		// $people = [
		// 	'creator'      => [],
		// 	'queuer'       => [],
		// 	'publisher'    => [],
		// 	'contributors' => [],
		// ];

		// foreach( [
		// 	'creator'   => 'creator',
		// 	'queuer'    => 'queuer',
		// 	'publisher' => 'publisher',
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
		// 	update_field( 'field_637f1ee7327b4', $people, $post_ID );
		// }


		// // Short Title
		// $short_title = get_db_field( 'name', 'value', $node->ID );

		// $logs .= '[Short Title]: ';
		// $logs .= ! empty( $short_title ) ? $short_title : '----';
		// $logs .= PHP_EOL;

		// if ( ! $debug ) {
		// 	// title
		// 	update_field( 'field_637f292b3a55c', $short_title, $post_ID );
		// }


		// // Thumbnail
		// $thumbnail_ID  = get_db_field( 'thumbnail', 'fid', $node->ID, " AND bundle = '" . $node->type . "'" );
		// $thumbnail_url = get_db_file_managed_url( $thumbnail_ID );
		// $thumbnail_alt = get_db_file_alt( 'thumbnail', $thumbnail_ID );

		// if ( ! empty( $thumbnail_url ) ) {
		// 	$thumbnail_ID = upload_attachment_from_url( $thumbnail_url, $post_ID, $thumbnail_alt ); // , '', $thumbnail_ID

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


		// // Main Image
		// $image_ID      = get_db_field( 'image', 'fid', $node->ID, " AND bundle = '" . $node->type . "'" );
		// $image_url     = get_db_file_managed_url( $image_ID );
		// $image_alt     = get_db_file_alt( 'image', $image_ID );
		// $image_caption = fix_wp_excerpt( get_db_image_caption( $node->ID ) );

		// if ( ! empty( $image_url ) ) {
		// 	$image_ID = upload_attachment_from_url( $image_url, $post_ID, $image_alt, $image_caption ); // , $image_ID

		// 	if ( ! $debug ) {
		// 		// image
		// 		update_field( 'field_64227ea9e44d8', $image_ID, $post_ID );
		// 	}

		// 	// Logs
		// 	$temp_media_logs = PHP_EOL;
		// 	$temp_media_logs .= 'post_ID: ' . $post_ID . PHP_EOL;
		// 	$temp_media_logs .= 'node_ID: ' . $node->ID . PHP_EOL;
		// 	$temp_media_logs .= 'old src: ' . $image_url . PHP_EOL;
		// 	$temp_media_logs .= 'new src: ' . ( ! empty( $image_ID ) ? wp_get_attachment_image_url( $image_ID, 'full' ) : 'Error !!!' ) . PHP_EOL;

		// 	$media_logs['main-image'] = $temp_media_logs;

		// 	if ( empty( $image_ID ) ) {
		// 		$error_logs .= PHP_EOL . 'Content Image:' . PHP_EOL;
		// 		$error_logs .= $temp_media_logs;
		// 	}
		// }

		// $logs .= '[Main image]:' . PHP_EOL;
		// $logs .= ! empty( $image_ID ) ? '<img src="' . wp_get_attachment_image_url( $image_ID, 'full' ) . '">' :'----';
		// $logs .= PHP_EOL;


		// // Content
		// $post_content     = '';
		// $content_sections = [];

		// // Content | Annotation
		// $field_annotation = get_db_value( 'field_annotation', $node->ID );

		// if ( ! empty( $field_annotation ) ) {
		// 	$post_content .= $field_annotation;
		// }

		// if ( ! empty( $sections_IDs = get_db_target_ids( 'sections', $node->ID ) ) ) {
		// 	foreach ( $sections_IDs as $sections_ID ) {
		// 		$content_sections[] = [
		// 			'ID'     => $sections_ID,
		// 			'status' => get_db_node( 'status', $sections_ID ),
		// 			'body'   => get_db_value( 'body', $sections_ID ),
		// 			'title'  => get_db_node( 'title', $sections_ID ),
		// 			'type'   => get_db_term_name( get_db_field( 'section_type', 'tid', $sections_ID ) ),
		// 		];
		// 	}
		// }

		// $logs .= '[Content]:' . PHP_EOL;

		// if ( ! empty( $content_sections ) ) {
		// 	foreach ( $content_sections as $key => $section ) {
		// 		if ( ! empty( $section['title'] ) ) {
		// 			$post_content .= '<h2>' . $section['title'] . '</h2>';
		// 		}
		// 		if ( ! empty( $section['body'] ) ) {
		// 			$post_content .= $section['body'];
		// 		}

		// 		// $logs .= $key . ') ' . $section['title'] . ' | ' . $section['ID'] . PHP_EOL;
		// 		// $logs .= ' - Status: ' . ( ( $section['status'] !== '1' ) ? 'draft' : 'publish' ) . PHP_EOL;
		// 		// $logs .= ' - Type: ' . $section['type'] . PHP_EOL;
		// 		// $logs .= ' - Body: ' . $section['body'] . PHP_EOL . PHP_EOL;
		// 	}
		// }

		// // Content | Body Depreciated
		// $field_body_depreciated = get_db_value( 'field_body_depreciated', $node->ID );

		// if ( ! empty( $field_body_depreciated ) ) {
		// 	$post_content .= $field_body_depreciated;
		// }

		// // Content | Related content Depreciated
		// $field_related_content = get_db_value( 'field_related_content', $node->ID );

		// if ( ! empty( $field_related_content ) ) {
		// 	$post_content .= $field_related_content;
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


		// // Video List
		// $videos = get_db_videos( $node->ID );

		// $logs .= '[Video list]:' . PHP_EOL;

		// if ( ! empty( $videos ) ) {

		// 	if ( ! $debug ) {
		// 		// video_list
		// 		update_field( 'field_637f2ba4dd94a', $videos, $post_ID );
		// 	}

		// 	foreach ( $videos as $key => $video ) {
		// 		$logs .= $key . ') ' . $video['url'] . ' | ' . $video['description'] . PHP_EOL;
		// 	}
		// }
		// else {
		// 	$logs .= '----';
		// }

		// $logs .= PHP_EOL;


		// // Featured Document
		// $document_fid = get_db_field( 'document', 'fid', $node->ID, " AND bundle = '" . $node->type . "'" );

		// $logs .= '[Featured Document]: ';

		// if ( ! empty( $document_fid ) ) {
		// 	$document_url  = get_db_file_managed_url( $document_fid );

		// 	if ( ! empty( $document_url ) ) {
		// 		$document_desc = get_db_field( 'document', 'description', $node->ID, " AND bundle = '" . $node->type . "'" );
		// 		$document_ID   = upload_attachment_from_url( $document_url, $post_ID, $document_desc, '', $document_fid );

		// 		if ( ! $debug ) {
		// 			// document_list
		// 			update_field( 'field_644a54a225740', [ [
		// 				'file'        => $document_ID,
		// 				'description' => $document_desc,
		// 			] ], $post_ID );
		// 		}

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


		// // Links List
		// $links_list      = [];
		// $temp_links_list = [];
		// $resorses_ids    = get_db_target_ids( 'related_content_list', $node->ID );

		// if ( ! empty( $resorses_ids ) ) {
		// 	foreach ( $resorses_ids as $resorses_ID) {
		// 		$results = $drupal_DB->get_results("SELECT * FROM field_data_field_links WHERE entity_id LIKE '$resorses_ID'");

		// 		if ( ! empty( $results ) ) {
		// 			foreach ( $results as $result ) {
		// 				if ( ! empty( $result->field_links_url ) || ! empty( $result->field_links_title ) ) {
		// 					if ( isset( $result->delta ) ) {
		// 						$delta          = (int) $result->delta;
		// 						$links_list[$delta] = [
		// 							'url'         => $result->field_links_url,
		// 							'description' => $result->field_links_title
		// 						];
		// 					}
		// 					else {
		// 						$temp_links_list[] = [
		// 							'url'         => $result->field_links_url,
		// 							'description' => $result->field_links_title
		// 						];
		// 					}
		// 				}
		// 			}
		// 		}
		// 	}
		// }

		// $all_links = array_merge( $links_list, $temp_links_list );

		// $logs .= '[Selected resources]:' . PHP_EOL;

		// if ( ! empty( $all_links ) ) {

		// 	if ( ! $debug ) {
		// 		// links_list
		// 		update_field( 'field_644a6acbc6931', $all_links, $post_ID );
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
						window.location = '<?php echo get_home_url( null, '?import=article&key=013b0f890d204a522a7e462d1dfa93e5&item=' . $i ); ?>';
					}, 0 );
				};
			</script>
		<?php
	}

	die();
} );