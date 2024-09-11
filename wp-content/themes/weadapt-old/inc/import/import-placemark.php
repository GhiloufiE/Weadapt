<?php

/*

Articles:
http://weadapt/web/?import=placemarks&key=013b0f890d204a522a7e462d1dfa93e5&node=104111

*/

if (
	is_admin() ||
	! isset( $_GET['import'] ) ||
	! in_array( $_GET['import'], [
		// 'page',
		// 'article',         // Article
		// 'initiative',      // Theme/Network/Project
		'placemarks',         // Case Study
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
		// 	$post_name = sanitize_title( $node->ID );
		// }


		// Isset Post
		$isset_posts = get_posts( array(
			'numberposts'	=> -1,
			'post_type'		=> 'case-study',
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
		// 		'post_type'     => 'case-study',
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
		// 		'post_type'     => 'case-study',
		// 		'post_excerpt'  => $post_excerpt,
		// 		'post_date'     => date( 'Y-m-d H:i:s', $node->created + 60*60 ),
		// 		'post_date_gmt' => date( 'Y-m-d H:i:s', $node->created + 60*60 )
		// 	);

		// 	if ( ! $debug ) {
		// 		$post_ID = wp_insert_post( wp_slash( $post_data ) );

		// 		// old_id
		// 		update_field( 'field_645c817976759', $node->ID, $post_ID );
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
		// 	$post_url = get_home_url() . '/placemarks/maps/view/' . $post_url . '/';
		// }

		// $post_url_alias = rtrim( str_replace( get_home_url() . '/', '', $post_url ), '/' );

		// if ( $url_alias !== $post_url_alias ) {
		// 	$url_logs  = $url_alias . ' (Node ID: ' . $node->ID . ');' . PHP_EOL;
		// 	$url_logs .= $post_url_alias . ' (Post ID: ' . $post_ID . ');' . PHP_EOL;
		// 	$url_logs .= PHP_EOL;

		// 	file_put_contents( '../logs/url-alias/case-study.log', $url_logs, FILE_APPEND );
		// }


		// // Post ID
		// $logs .= '[Post ID]: ' . $post_ID . PHP_EOL;


		// // Is new
		// $logs .= '[Is New]: ';
		// $logs .= ( $is_new ) ? 1 : 0;
		// $logs .= PHP_EOL;


		// // Language
		// $languages_list = ['ab'=>'Abkhazian','aa'=>'Afar','af'=>'Afrikaans','ak'=>'Akan','sq'=>'Albanian','am'=>'Amharic','ar'=>'Arabic','hy'=>'Armenian','as'=>'Assamese','ast'=>'Asturian','av'=>'Avar','ae'=>'Avestan','ay'=>'Aymara','az'=>'Azerbaijani','bm'=>'Bambara','ba'=>'Bashkir','eu'=>'Basque','be'=>'Belarusian','bn'=>'Bengali','bh'=>'Bihari','bi'=>'Bislama','bs'=>'Bosnian','br'=>'Breton','bg'=>'Bulgarian','my'=>'Burmese','km'=>'Cambodian','ca'=>'Catalan','ch'=>'Chamorro','ce'=>'Chechen','ny'=>'Chichewa','zh-hans'=>'Chinese, Simplified','zh-hant'=>'Chinese, Traditional','cv'=>'Chuvash','kw'=>'Cornish','co'=>'Corsican','cr'=>'Cree','hr'=>'Croatian','cs'=>'Czech','da'=>'Danish','nl'=>'Dutch','dz'=>'Dzongkha','en'=>'English','en-gb'=>'English, British','eo'=>'Esperanto','et'=>'Estonian','ee'=>'Ewe','fo'=>'Faeroese','fj'=>'Fiji','fil'=>'Filipino','fi'=>'Finnish','fr'=>'French','fy'=>'Frisian','ff'=>'Fulah','gl'=>'Galician','ka'=>'Georgian','de'=>'German','el'=>'Greek','kl'=>'Greenlandic','gn'=>'Guarani','gu'=>'Gujarati','ht'=>'Haitian Creole','ha'=>'Hausa','he'=>'Hebrew','hz'=>'Herero','hi'=>'Hindi','ho'=>'Hiri Motu','hu'=>'Hungarian','is'=>'Icelandic','ig'=>'Igbo','id'=>'Indonesian','ia'=>'Interlingua','ie'=>'Interlingue','iu'=>'Inuktitut','ik'=>'Inupiak','ga'=>'Irish','it'=>'Italian','ja'=>'Japanese','jv'=>'Javanese','kn'=>'Kannada','kr'=>'Kanuri','ks'=>'Kashmiri','kk'=>'Kazakh','ki'=>'Kikuyu','rw'=>'Kinyarwanda','rn'=>'Kirundi','kv'=>'Komi','kg'=>'Kongo','ko'=>'Korean','ku'=>'Kurdish','kj'=>'Kwanyama','ky'=>'Kyrgyz','lo'=>'Laothian','la'=>'Latin','lv'=>'Latvian','ln'=>'Lingala','lt'=>'Lithuanian','xx-lolspeak'=>'Lolspeak','lg'=>'Luganda','lb'=>'Luxembourgish','mk'=>'Macedonian','mg'=>'Malagasy','ms'=>'Malay','ml'=>'Malayalam','dv'=>'Maldivian','mt'=>'Maltese','gv'=>'Manx','mr'=>'Marathi','mh'=>'Marshallese','mo'=>'Moldavian','mn'=>'Mongolian','mi'=>'Māori','na'=>'Nauru','nv'=>'Navajo','ng'=>'Ndonga','ne'=>'Nepali','nd'=>'North Ndebele','se'=>'Northern Sami','nb'=>'Norwegian Bokmål','nn'=>'Norwegian Nynorsk','oc'=>'Occitan','cu'=>'Old Slavonic','or'=>'Oriya','om'=>'Oromo','os'=>'Ossetian','pi'=>'Pali','ps'=>'Pashto','fa'=>'Persian','pl'=>'Polish','pt-br'=>'Portuguese, Brazil','pt'=>'Portuguese, International','pt-pt'=>'Portuguese, Portugal','pa'=>'Punjabi','qu'=>'Quechua','rm'=>'Rhaeto-Romance','ro'=>'Romanian','ru'=>'Russian','sm'=>'Samoan','sg'=>'Sango','sa'=>'Sanskrit','sc'=>'Sardinian','sco'=>'Scots','gd'=>'Scots Gaelic','sr'=>'Serbian','sh'=>'Serbo-Croatian','st'=>'Sesotho','tn'=>'Setswana','sn'=>'Shona','sd'=>'Sindhi','si'=>'Sinhala','ss'=>'Siswati','sk'=>'Slovak','sl'=>'Slovenian','so'=>'Somali','nr'=>'South Ndebele','es'=>'Spanish','su'=>'Sudanese','sw'=>'Swahili','sv'=>'Swedish','gsw-berne'=>'Swiss German','tl'=>'Tagalog','ty'=>'Tahitian','tg'=>'Tajik','ta'=>'Tamil','tt'=>'Tatar','te'=>'Telugu','th'=>'Thai','bo'=>'Tibetan','ti'=>'Tigrinya','to'=>'Tonga','ts'=>'Tsonga','tr'=>'Turkish','tk'=>'Turkmen','tw'=>'Twi','uk'=>'Ukrainian','ur'=>'Urdu','ug'=>'Uyghur','uz'=>'Uzbek','ve'=>'Venda','vi'=>'Vietnamese','cy'=>'Welsh','wo'=>'Wolof','xh'=>'Xhosa','yi'=>'Yiddish','yo'=>'Yoruba','za'=>'Zhuang','zu'=>'Zulu'];
		// $language_code  = get_db_value( 'field_input_language', $node->ID );

		// if ( ! $debug && ! empty( $languages_list[$language_code] ) ) {
		// 	// language
		// 	update_field( 'field_645c81797dad5', $languages_list[$language_code], $post_ID );
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
		// 	update_field( 'field_645c81797a1e8', $short_title, $post_ID );
		// }

		// // Location method
		// $logs .= '[Location]:' . PHP_EOL;
		// $map = [
		// 	'zoom' => 14
		// ];
		// $location_method = get_db_field( 'location_method', 'value', $node->ID );

		// switch( $location_method ) {
		// 	case 'address':
		// 	case 'locationtext':
		// 		$address = '';

		// 		if ( $location_method === 'address' ) {
		// 			$temp_address   = [];
		// 			$temp_address[] = trim( get_db_field( 'address', 'thoroughfare', $node->ID, " AND bundle = '" . $node->type . "'" ) );
		// 			$temp_address[] = trim( get_db_field( 'address', 'premise', $node->ID, " AND bundle = '" . $node->type . "'" ) );

		// 			$city = get_db_field( 'address', 'locality', $node->ID, " AND bundle = '" . $node->type . "'" );

		// 			if ( ! empty( $city ) ) {
		// 				// $map['city'] = trim( $city );
		// 				$temp_address[] = trim( $city );
		// 			}

		// 			// $state = get_db_field( 'address', 'administrative_area', $node->ID, " AND bundle = '" . $node->type . "'" );

		// 			// if ( ! empty( $state ) ) {
		// 			// 	$map['state'] = trim( $state );
		// 			// }

		// 			$post_code = get_db_field( 'address', 'postal_code', $node->ID, " AND bundle = '" . $node->type . "'" );

		// 			if ( ! empty( $post_code ) ) {
		// 				// $map['post_code'] = trim( $post_code );
		// 				$temp_address[] = trim( $post_code );
		// 			}

		// 			// $country_list = ["AF"=>"Afghanistan","AX"=>"Aland Islands","AL"=>"Albania","DZ"=>"Algeria","AS"=>"American Samoa","AD"=>"Andorra","AO"=>"Angola","AI"=>"Anguilla","AQ"=>"Antarctica","AG"=>"Antigua and Barbuda","AR"=>"Argentina","AM"=>"Armenia","AW"=>"Aruba","AU"=>"Australia","AT"=>"Austria","AZ"=>"Azerbaijan","BS"=>"Bahamas","BH"=>"Bahrain","BD"=>"Bangladesh","BB"=>"Barbados","BY"=>"Belarus","BE"=>"Belgium","BZ"=>"Belize","BJ"=>"Benin","BM"=>"Bermuda","BT"=>"Bhutan","BO"=>"Bolivia","BA"=>"Bosnia and Herzegovina","BW"=>"Botswana","BV"=>"Bouvet Island","BR"=>"Brazil","IO"=>"British Indian Ocean Territory","VG"=>"British Virgin Islands","BN"=>"Brunei","BG"=>"Bulgaria","BF"=>"Burkina Faso","BI"=>"Burundi","KH"=>"Cambodia","CM"=>"Cameroon","CA"=>"Canada","CV"=>"Cape Verde","BQ"=>"Caribbean Netherlands","KY"=>"Cayman Islands","CF"=>"Central African Republic","TD"=>"Chad","CL"=>"Chile","CN"=>"China","CX"=>"Christmas Island","CC"=>"Cocos (Keeling) Islands","CO"=>"Colombia","KM"=>"Comoros","CG"=>"Congo (Brazzaville)","CD"=>"Congo (Kinshasa)","CK"=>"Cook Islands","CR"=>"Costa Rica","HR"=>"Croatia","CU"=>"Cuba","CW"=>"Curaçao","CY"=>"Cyprus","CZ"=>"Czech Republic","DK"=>"Denmark","DJ"=>"Djibouti","DM"=>"Dominica","DO"=>"Dominican Republic","EC"=>"Ecuador","EG"=>"Egypt","SV"=>"El Salvador","GQ"=>"Equatorial Guinea","ER"=>"Eritrea","EE"=>"Estonia","ET"=>"Ethiopia","FK"=>"Falkland Islands","FO"=>"Faroe Islands","FJ"=>"Fiji","FI"=>"Finland","FR"=>"France","GF"=>"French Guiana","PF"=>"French Polynesia","TF"=>"French Southern Territories","GA"=>"Gabon","GM"=>"Gambia","GE"=>"Georgia","DE"=>"Germany","GH"=>"Ghana","GI"=>"Gibraltar","GR"=>"Greece","GL"=>"Greenland","GD"=>"Grenada","GP"=>"Guadeloupe","GU"=>"Guam","GT"=>"Guatemala","GG"=>"Guernsey","GN"=>"Guinea","GW"=>"Guinea-Bissau","GY"=>"Guyana","HT"=>"Haiti","HM"=>"Heard Island and McDonald Islands","HN"=>"Honduras","HK"=>"Hong Kong S.A.R., China","HU"=>"Hungary","IS"=>"Iceland","IN"=>"India","ID"=>"Indonesia","IR"=>"Iran","IQ"=>"Iraq","IE"=>"Ireland","IM"=>"Isle of Man","IL"=>"Israel","IT"=>"Italy","CI"=>"Ivory Coast","JM"=>"Jamaica","JP"=>"Japan","JE"=>"Jersey","JO"=>"Jordan","KZ"=>"Kazakhstan","KE"=>"Kenya","KI"=>"Kiribati","KW"=>"Kuwait","KG"=>"Kyrgyzstan","LA"=>"Laos","LV"=>"Latvia","LB"=>"Lebanon","LS"=>"Lesotho","LR"=>"Liberia","LY"=>"Libya","LI"=>"Liechtenstein","LT"=>"Lithuania","LU"=>"Luxembourg","MO"=>"Macao S.A.R., China","MK"=>"Macedonia","MG"=>"Madagascar","MW"=>"Malawi","MY"=>"Malaysia","MV"=>"Maldives","ML"=>"Mali","MT"=>"Malta","MH"=>"Marshall Islands","MQ"=>"Martinique","MR"=>"Mauritania","MU"=>"Mauritius","YT"=>"Mayotte","MX"=>"Mexico","FM"=>"Micronesia","MD"=>"Moldova","MC"=>"Monaco","MN"=>"Mongolia","ME"=>"Montenegro","MS"=>"Montserrat","MA"=>"Morocco","MZ"=>"Mozambique","MM"=>"Myanmar","NA"=>"Namibia","NR"=>"Nauru","NP"=>"Nepal","NL"=>"Netherlands","AN"=>"Netherlands Antilles","NC"=>"New Caledonia","NZ"=>"New Zealand","NI"=>"Nicaragua","NE"=>"Niger","NG"=>"Nigeria","NU"=>"Niue","NF"=>"Norfolk Island","MP"=>"Northern Mariana Islands","KP"=>"North Korea","NO"=>"Norway","OM"=>"Oman","PK"=>"Pakistan","PW"=>"Palau","PS"=>"Palestinian Territory","PA"=>"Panama","PG"=>"Papua New Guinea","PY"=>"Paraguay","PE"=>"Peru","PH"=>"Philippines","PN"=>"Pitcairn","PL"=>"Poland","PT"=>"Portugal","PR"=>"Puerto Rico","QA"=>"Qatar","RE"=>"Reunion","RO"=>"Romania","RU"=>"Russia","RW"=>"Rwanda","BL"=>"Saint Barthélemy","SH"=>"Saint Helena","KN"=>"Saint Kitts and Nevis","LC"=>"Saint Lucia","MF"=>"Saint Martin (French part)","PM"=>"Saint Pierre and Miquelon","VC"=>"Saint Vincent and the Grenadines","WS"=>"Samoa","SM"=>"San Marino","ST"=>"Sao Tome and Principe","SA"=>"Saudi Arabia","SN"=>"Senegal","RS"=>"Serbia","SC"=>"Seychelles","SL"=>"Sierra Leone","SG"=>"Singapore","SX"=>"Sint Maarten","SK"=>"Slovakia","SI"=>"Slovenia","SB"=>"Solomon Islands","SO"=>"Somalia","ZA"=>"South Africa","GS"=>"South Georgia and the South Sandwich Islands","KR"=>"South Korea","SS"=>"South Sudan","ES"=>"Spain","LK"=>"Sri Lanka","SD"=>"Sudan","SR"=>"Suriname","SJ"=>"Svalbard and Jan Mayen","SZ"=>"Swaziland","SE"=>"Sweden","CH"=>"Switzerland","SY"=>"Syria","TW"=>"Taiwan","TJ"=>"Tajikistan","TZ"=>"Tanzania","TH"=>"Thailand","TL"=>"Timor-Leste","TG"=>"Togo","TK"=>"Tokelau","TO"=>"Tonga","TT"=>"Trinidad and Tobago","TN"=>"Tunisia","TR"=>"Turkey","TM"=>"Turkmenistan","TC"=>"Turks and Caicos Islands","TV"=>"Tuvalu","VI"=>"U.S. Virgin Islands","UG"=>"Uganda","UA"=>"Ukraine","AE"=>"United Arab Emirates","GB"=>"United Kingdom","US"=>"United States","UM"=>"United States Minor Outlying Islands","UY"=>"Uruguay","UZ"=>"Uzbekistan","VU"=>"Vanuatu","VA"=>"Vatican","VE"=>"Venezuela","VN"=>"Vietnam","WF"=>"Wallis and Futuna","EH"=>"Western Sahara","YE"=>"Yemen","ZM"=>"Zambia","ZW"=>"Zimbabwe"];
		// 			// $country_code = get_db_field( 'address', 'country', $node->ID, " AND bundle = '" . $node->type . "'" );

		// 			// if ( ! empty( $country_list[$country_code] ) ) {
		// 			// 	$map['country'] = trim( $country_list[$country_code] );
		// 			// 	$map['country_short'] = trim( $country_code );
		// 			// }

		// 			if ( ! empty( $temp_address ) ) {
		// 				$address = implode( ' ', $temp_address );
		// 			}
		// 		}
		// 		else {
		// 			$address = trim( get_db_field( 'locationtext', 'value', $node->ID, " AND bundle = '" . $node->type . "'" ) );
		// 		}

		// 		if ( ! empty( $address ) ) {
		// 			$response = wp_remote_get( "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=AIzaSyB-9icjn5KC37L9xV1tof4BGN3xwHUdINg", array('timeout' => 5));

		// 			if( $response['response']['code'] == 200 ) {
		// 				$api_response = json_decode(wp_remote_retrieve_body($response), true);

		// 				if ( ! empty( $api_response['results'][0]['geometry']['location']['lat'] ) ) {
		// 					$map['lat'] = $api_response['results'][0]['geometry']['location']['lat'];
		// 				}
		// 				if ( ! empty( $api_response['results'][0]['geometry']['location']['lng'] ) ) {
		// 					$map['lng'] = $api_response['results'][0]['geometry']['location']['lng'];
		// 				}
		// 				if ( ! empty( $api_response['results'][0]['formatted_address'] ) ) {
		// 					$map['address'] = $api_response['results'][0]['formatted_address'];
		// 				}
		// 			}
		// 			else {
		// 				$map['address'] = $address;
		// 			}
		// 		}

		// 		break;

		// 	case 'location':
		// 		$lat = trim( get_db_field( 'location', 'lat', $node->ID ) );
		// 		$lng = trim( get_db_field( 'location', 'lng', $node->ID ) );

		// 		if ( ! empty( $lat ) && ! empty( $lng ) ) {
		// 			$map['lat'] = $lat;
		// 			$map['lng'] = $lng;

		// 			$response = wp_remote_get( "https://maps.googleapis.com/maps/api/geocode/json?latlng={$lat},{$lng}&key=AIzaSyB-9icjn5KC37L9xV1tof4BGN3xwHUdINg", array('timeout' => 5));

		// 			if($response['response']['code'] == 200) {
		// 				$api_response = json_decode(wp_remote_retrieve_body($response), true);

		// 				if ( ! empty( $api_response['results'][0]['formatted_address'] ) ) {
		// 					$map['address'] = $api_response['results'][0]['formatted_address'];
		// 				}
		// 			}
		// 		}
		// 		break;
		// }

		// foreach ( $map as $key => $value ) {
		// 	$logs .= ' - [' . $key . ']: ' . $value . PHP_EOL;
		// }
		// $logs .= PHP_EOL;

		// if ( ! $debug ) {
		// 	// location
		// 	update_field( 'field_645c817981697', $map, $post_ID );
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
		// 		update_field( 'field_645c8179852d7', $image_ID, $post_ID );
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
		// 		update_field( 'field_645c817988e0e', $videos, $post_ID );
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
		// 			update_field( 'field_645c81798c799', [ [
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


		// // Relevant
		// $relevant = [
		// 	'main_theme_network' => 0,
		// 	'themes_networks'    => [],
		// 	'organizations'      => [],
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


		// // Selected resources
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
		// 		update_field( 'field_645c817990207', $all_links, $post_ID );
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
						window.location = '<?php echo get_home_url( null, '?import=placemarks&key=013b0f890d204a522a7e462d1dfa93e5&item=' . $i ); ?>';
					}, 0 );
				};
			</script>
		<?php
	}

	die();
} );