<?php

/*

Articles:
http://weadapt/web/?import=initiative&key=013b0f890d204a522a7e462d1dfa93e5&node=1105
http://weadapt/web/?import=initiative&key=013b0f890d204a522a7e462d1dfa93e5&node=1095

*/

if (
	is_admin() ||
	! isset( $_GET['import'] ) ||
	! in_array( $_GET['import'], [
		// 'page',
		// 'article',         // Article
		'initiative',         // Theme/Network/Project
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
	$loop_end   = isset( $_GET['node'] ) ? count($all_nodes) : ($loop_start + 4);

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


		// Initiative type
		$initiative_type = get_db_field( 'initiative_type', 'value', $node->ID );

		$logs .= '[Initiative type]: ';
		$logs .= ! empty( $initiative_type ) ? $initiative_type : '----';
		$logs .= PHP_EOL;


		// Post type
		$post_type = 'theme';

		switch ( $initiative_type ) {
			case 'Theme':   $post_type = 'theme'; break;
			case 'Network': $post_type = 'network'; break;
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
		// 	$post_name = sanitize_title( wp_strip_all_tags( $node->title ) );
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
		// 		update_field( 'field_64535c4d8bd76', $node->ID, $post_ID );
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
		// 	$post_url = get_home_url() . '/knowledge-base/' . $post_url . '/';
		// }

		// $post_url_alias = rtrim( str_replace( get_home_url() . '/', '', $post_url ), '/' );

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


		// // Featured
		// $is_featured = wp_validate_boolean( get_db_field( 'featured', 'value', $node->ID ) );

		// $logs .= '[Featured]: ';
		// $logs .= $is_featured ? '1' : '0';
		// $logs .= PHP_EOL;

		// if ( ! $debug ) {
		// 	// featured
		// 	update_field( 'field_6459ecda684f3', $is_featured, $post_ID );
		// }


		// // Title
		// $short_title = get_db_field( 'name', 'value', $node->ID );

		// $logs .= '[Title]: ';
		// $logs .= ! empty( $short_title ) ? $short_title : '----';
		// $logs .= PHP_EOL;

		// if ( ! $debug ) {
		// 	// title
		// 	update_field( 'field_637485a48d708', $short_title, $post_ID );
		// }


		// // Thumbnail
		// $thumbnail_ID  = get_db_field( 'thumbnail', 'fid', $node->ID, " AND bundle = '" . $node->type . "'" );
		// $thumbnail_url = get_db_file_managed_url( $thumbnail_ID );
		// $thumbnail_alt = get_db_file_alt( 'thumbnail', $thumbnail_ID );

		// if ( ! empty( $thumbnail_url ) ) {
		// 	$thumbnail_ID = upload_attachment_from_url( $thumbnail_url, $post_ID, $thumbnail_alt, '', $thumbnail_ID );

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
		// 	$image_ID = upload_attachment_from_url( $image_url, $post_ID, $image_alt, $image_caption, $image_ID );

		// 	if ( ! $debug ) {
		// 		// image
		// 		update_field( 'field_6461ddeb8d3cc', $image_ID, $post_ID );
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
		// $post_content = '';

		// // Content | Body
		// $body = get_db_value( 'body', $node->ID );

		// if ( ! empty( $body ) ) {
		// 	$post_content .= $body;
		// }

		// // Content | Additional Text
		// $field_additional_text = get_db_value( 'field_additional_text', $node->ID );

		// if ( ! empty( $field_additional_text ) ) {
		// 	$post_content .= $field_additional_text;
		// }

		// // Content | Related content
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

		// $logs .= '[Content]:' . PHP_EOL;
		// $logs .= ! empty( $post_content ) ? $post_content : '----';
		// $logs .= PHP_EOL;


		// // Relevant
		// $relevant = [
		// 	'themes_networks' => [],
		// 	'organizations'   => [],
		// ];


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
		// 	update_field( 'field_637f2404e44f0', $relevant, $post_ID );
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


		// // Publish To
		// $publish_to = get_db_domain_access( $node->ID );

		// if ( ! $debug ) {
		// 	// publish_to
		// 	update_field( 'field_6374a3364bb73', $publish_to, $post_ID );
		// }

		// $logs .= '[Publish to]: ';
		// $logs .= ! empty( $publish_to ) ? implode( ', ', $publish_to ) : '----';
		// $logs .= PHP_EOL;


		// // Users
		// $people = [
		// 	'creator'   => [],
		// 	'publisher' => [],
		// 	'editors'  => [],
		// 	'contacts'  => [],
		// ];

		// foreach( [
		// 	'creator'   => 'creator',
		// 	'publisher' => 'publisher',
		// 	'editors'   => 'editors',
		// 	'contacts'  => 'contacts'
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
		// 	update_field( 'field_637c78d8d8fb9', $people, $post_ID );
		// }


		// // Language
		// $languages_list = ['ab'=>'Abkhazian','aa'=>'Afar','af'=>'Afrikaans','ak'=>'Akan','sq'=>'Albanian','am'=>'Amharic','ar'=>'Arabic','hy'=>'Armenian','as'=>'Assamese','ast'=>'Asturian','av'=>'Avar','ae'=>'Avestan','ay'=>'Aymara','az'=>'Azerbaijani','bm'=>'Bambara','ba'=>'Bashkir','eu'=>'Basque','be'=>'Belarusian','bn'=>'Bengali','bh'=>'Bihari','bi'=>'Bislama','bs'=>'Bosnian','br'=>'Breton','bg'=>'Bulgarian','my'=>'Burmese','km'=>'Cambodian','ca'=>'Catalan','ch'=>'Chamorro','ce'=>'Chechen','ny'=>'Chichewa','zh-hans'=>'Chinese, Simplified','zh-hant'=>'Chinese, Traditional','cv'=>'Chuvash','kw'=>'Cornish','co'=>'Corsican','cr'=>'Cree','hr'=>'Croatian','cs'=>'Czech','da'=>'Danish','nl'=>'Dutch','dz'=>'Dzongkha','en'=>'English','en-gb'=>'English, British','eo'=>'Esperanto','et'=>'Estonian','ee'=>'Ewe','fo'=>'Faeroese','fj'=>'Fiji','fil'=>'Filipino','fi'=>'Finnish','fr'=>'French','fy'=>'Frisian','ff'=>'Fulah','gl'=>'Galician','ka'=>'Georgian','de'=>'German','el'=>'Greek','kl'=>'Greenlandic','gn'=>'Guarani','gu'=>'Gujarati','ht'=>'Haitian Creole','ha'=>'Hausa','he'=>'Hebrew','hz'=>'Herero','hi'=>'Hindi','ho'=>'Hiri Motu','hu'=>'Hungarian','is'=>'Icelandic','ig'=>'Igbo','id'=>'Indonesian','ia'=>'Interlingua','ie'=>'Interlingue','iu'=>'Inuktitut','ik'=>'Inupiak','ga'=>'Irish','it'=>'Italian','ja'=>'Japanese','jv'=>'Javanese','kn'=>'Kannada','kr'=>'Kanuri','ks'=>'Kashmiri','kk'=>'Kazakh','ki'=>'Kikuyu','rw'=>'Kinyarwanda','rn'=>'Kirundi','kv'=>'Komi','kg'=>'Kongo','ko'=>'Korean','ku'=>'Kurdish','kj'=>'Kwanyama','ky'=>'Kyrgyz','lo'=>'Laothian','la'=>'Latin','lv'=>'Latvian','ln'=>'Lingala','lt'=>'Lithuanian','xx-lolspeak'=>'Lolspeak','lg'=>'Luganda','lb'=>'Luxembourgish','mk'=>'Macedonian','mg'=>'Malagasy','ms'=>'Malay','ml'=>'Malayalam','dv'=>'Maldivian','mt'=>'Maltese','gv'=>'Manx','mr'=>'Marathi','mh'=>'Marshallese','mo'=>'Moldavian','mn'=>'Mongolian','mi'=>'Māori','na'=>'Nauru','nv'=>'Navajo','ng'=>'Ndonga','ne'=>'Nepali','nd'=>'North Ndebele','se'=>'Northern Sami','nb'=>'Norwegian Bokmål','nn'=>'Norwegian Nynorsk','oc'=>'Occitan','cu'=>'Old Slavonic','or'=>'Oriya','om'=>'Oromo','os'=>'Ossetian','pi'=>'Pali','ps'=>'Pashto','fa'=>'Persian','pl'=>'Polish','pt-br'=>'Portuguese, Brazil','pt'=>'Portuguese, International','pt-pt'=>'Portuguese, Portugal','pa'=>'Punjabi','qu'=>'Quechua','rm'=>'Rhaeto-Romance','ro'=>'Romanian','ru'=>'Russian','sm'=>'Samoan','sg'=>'Sango','sa'=>'Sanskrit','sc'=>'Sardinian','sco'=>'Scots','gd'=>'Scots Gaelic','sr'=>'Serbian','sh'=>'Serbo-Croatian','st'=>'Sesotho','tn'=>'Setswana','sn'=>'Shona','sd'=>'Sindhi','si'=>'Sinhala','ss'=>'Siswati','sk'=>'Slovak','sl'=>'Slovenian','so'=>'Somali','nr'=>'South Ndebele','es'=>'Spanish','su'=>'Sudanese','sw'=>'Swahili','sv'=>'Swedish','gsw-berne'=>'Swiss German','tl'=>'Tagalog','ty'=>'Tahitian','tg'=>'Tajik','ta'=>'Tamil','tt'=>'Tatar','te'=>'Telugu','th'=>'Thai','bo'=>'Tibetan','ti'=>'Tigrinya','to'=>'Tonga','ts'=>'Tsonga','tr'=>'Turkish','tk'=>'Turkmen','tw'=>'Twi','uk'=>'Ukrainian','ur'=>'Urdu','ug'=>'Uyghur','uz'=>'Uzbek','ve'=>'Venda','vi'=>'Vietnamese','cy'=>'Welsh','wo'=>'Wolof','xh'=>'Xhosa','yi'=>'Yiddish','yo'=>'Yoruba','za'=>'Zhuang','zu'=>'Zulu'];
		// $language_code  = get_db_value( 'field_input_language', $node->ID );

		// if ( ! $debug && ! empty( $languages_list[$language_code] ) ) {
		// 	// language
		// 	update_field( 'field_6461de632cb66', $languages_list[$language_code], $post_ID );
		// }

		// $logs .= '[Language]: ';
		// $logs .= ! empty( $languages_list[$language_code] ) ? $language_code . ' | ' . $languages_list[$language_code] : '----';
		// $logs .= PHP_EOL;


		// // Featured Articles
		// $logs .= '[Featured Articles]:' . PHP_EOL;
		// $featured_articles     = [];
		// $featured_articles_IDs = get_db_target_ids( 'featured_articles', $node->ID, '_value' );

		// if ( ! empty( $featured_articles_IDs ) ) {
		// 	foreach ( $featured_articles_IDs as $key => $featured_articles_ID ) {
		// 		$articles = get_post_ids_by_target_ids( 'referenced_items', $featured_articles_ID, ['article', 'blog', 'event'] );

		// 		$temp_article = [
		// 			'title'       => get_db_field( 'label', 'value', $featured_articles_ID, " AND bundle = 'featured_listing_section'" ),
		// 			'description' => get_db_field( 'introduction', 'value', $featured_articles_ID, " AND bundle = 'featured_listing_section'" ),
		// 			'articles'    => ! empty( $articles ) ? array_keys( $articles ) : []
		// 		];

		// 		$logs .= ( $key + 1 ) . ') ' . $temp_article['title'] . ' | ' . $featured_articles_ID . PHP_EOL;
		// 		$logs .= ' - [description]: ' . $temp_article['description'] . PHP_EOL;

		// 		$logs .= ' - [articles]: ';
		// 		$logs .= ! empty( $articles ) ? implode(', ', array_map(function ($key, $value) {
		// 			return $value . ' (' . $key . ')';
		// 		}, array_keys($articles), $articles)) : '----';
		// 		$logs .= PHP_EOL . PHP_EOL;

		// 		if (
		// 			! empty( $temp_article['title'] ) ||
		// 			! empty( $temp_article['description'] ) ||
		// 			! empty( $temp_article['articles'] )
		// 		) {
		// 			$featured_articles[] = $temp_article;
		// 		}
		// 	}
		// }

		// if ( ! $debug ) {
		// 	// featured_articles
		// 	update_field( 'field_637dd74b3a76b', $featured_articles, $post_ID );
		// }


		// // Categories
		// $logs .= '[Categories]:' . PHP_EOL;
		// $featured_categories     = [];
		// $featured_categories_IDs = get_db_target_ids( 'categories', $node->ID, '_target_id' );

		// if ( ! empty( $featured_categories_IDs ) ) {
		// 	foreach ( $featured_categories_IDs as $key => $featured_category_ID ) {
		// 		$temp_category = [];
		// 		$category_title_result = $drupal_DB->get_row("SELECT title FROM node WHERE nid LIKE '$featured_category_ID' AND type = 'theme_category'");

		// 		if ( ! empty( trim( $category_title_result->title ) ) ) {
		// 			$term_ID = 0;
		// 			$term    = get_term_by( 'name', $category_title_result->title, 'category' );

		// 			if ( empty( $term ) ) {
		// 				if ( ! $debug ) {
		// 					$term = wp_insert_term( $category_title_result->title, 'category' );

		// 					if( is_wp_error( $term ) ){
		// 						s( $term->get_error_message() );
		// 					}
		// 					$term_ID = $term['term_id'];
		// 				}
		// 			}
		// 			elseif ( $term_ID === 0 ) {
		// 				$term_ID = $term->term_id;
		// 			}

		// 			$temp_category['category'] = $term_ID;

		// 			$logs .= ( $key + 1 ) . ') ' . $category_title_result->title . ' | ' . $featured_category_ID . PHP_EOL;
		// 		}

		// 		$temp_category['description'] = get_db_value( 'body', $featured_category_ID );

		// 		$logs .= ' - [description]: ' . $temp_category['description'] . PHP_EOL;

		// 		if (
		// 			! empty( $temp_category['category'] ) ||
		// 			! empty( $temp_category['description'] )
		// 		) {
		// 			$featured_categories[] = $temp_category;
		// 		}
		// 	}
		// }

		// if ( ! $debug ) {
		// 	// featured_categories
		// 	update_field( 'field_637df440f0402', $featured_categories, $post_ID );
		// }



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
						window.location = '<?php echo get_home_url( null, '?import=initiative&key=013b0f890d204a522a7e462d1dfa93e5&item=' . $i ); ?>';
					}, 0 );
				};
			</script>
		<?php
	}

	die();
} );