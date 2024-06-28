<?php

/*

Articles:
http://weadapt/web/?import=organisation&key=013b0f890d204a522a7e462d1dfa93e5&node=6016
http://weadapt/web/?import=organisation&key=013b0f890d204a522a7e462d1dfa93e5&node=81171

*/

if (
	is_admin() ||
	! isset( $_GET['import'] ) ||
	! in_array( $_GET['import'], [
		// 'page',
		// 'article',         // Article
		// 'initiative',      // Theme/Network/Project
		// 'placemarks',      // Case Study
		'organisation',       // Organizations
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
	$import_type  = ! empty( $_GET['import'] ) ? esc_attr( $_GET['import'] ) : false;
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


		// Post type
		$post_type = $node->type;

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
		// 		update_field( 'field_6458888423b79', $node->ID, $post_ID );
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
		// 	$post_url = get_home_url() . '/organisation/' . $post_url . '/';
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


		// // Name
		// $short_title = get_db_field( 'name', 'value', $node->ID );

		// $logs .= '[Name]: ';
		// $logs .= ! empty( $short_title ) ? $short_title : '----';
		// $logs .= PHP_EOL;

		// if ( ! $debug ) {
		// 	// name
		// 	update_field( 'field_645888842762f', $short_title, $post_ID );
		// }


		// // Content
		// $post_content = '';

		// // Content | Body
		// $body = get_db_value( 'body', $node->ID );

		// if ( ! empty( $body ) ) {
		// 	$post_content .= $body;
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

		// $logs .= '[Content]:' . PHP_EOL;
		// $logs .= ! empty( $post_content ) ? $post_content : '----';
		// $logs .= PHP_EOL;


		// // Address
		// $address_1 = get_db_field( 'address', 'thoroughfare', $node->ID, " AND bundle = '" . $node->type . "'" );
		// $address_2 = get_db_field( 'address', 'premise', $node->ID, " AND bundle = '" . $node->type . "'" );
		// $city      = get_db_field( 'address', 'locality', $node->ID, " AND bundle = '" . $node->type . "'" );
		// $county    = get_db_field( 'address', 'administrative_area', $node->ID, " AND bundle = '" . $node->type . "'" );
		// $postcode  = get_db_field( 'address', 'postal_code', $node->ID, " AND bundle = '" . $node->type . "'" );

		// $address = [
		// 	'country'   => '',
		// 	'address_1' => $address_1,
		// 	'address_2' => $address_2,
		// 	'city'      => $city,
		// 	'county'    => $county,
		// 	'postcode'  => $postcode,
		// ];

		// $country_list = ["AF"=>"Afghanistan","AX"=>"Aland Islands","AL"=>"Albania","DZ"=>"Algeria","AS"=>"American Samoa","AD"=>"Andorra","AO"=>"Angola","AI"=>"Anguilla","AQ"=>"Antarctica","AG"=>"Antigua and Barbuda","AR"=>"Argentina","AM"=>"Armenia","AW"=>"Aruba","AU"=>"Australia","AT"=>"Austria","AZ"=>"Azerbaijan","BS"=>"Bahamas","BH"=>"Bahrain","BD"=>"Bangladesh","BB"=>"Barbados","BY"=>"Belarus","BE"=>"Belgium","BZ"=>"Belize","BJ"=>"Benin","BM"=>"Bermuda","BT"=>"Bhutan","BO"=>"Bolivia","BA"=>"Bosnia and Herzegovina","BW"=>"Botswana","BV"=>"Bouvet Island","BR"=>"Brazil","IO"=>"British Indian Ocean Territory","VG"=>"British Virgin Islands","BN"=>"Brunei","BG"=>"Bulgaria","BF"=>"Burkina Faso","BI"=>"Burundi","KH"=>"Cambodia","CM"=>"Cameroon","CA"=>"Canada","CV"=>"Cape Verde","BQ"=>"Caribbean Netherlands","KY"=>"Cayman Islands","CF"=>"Central African Republic","TD"=>"Chad","CL"=>"Chile","CN"=>"China","CX"=>"Christmas Island","CC"=>"Cocos (Keeling) Islands","CO"=>"Colombia","KM"=>"Comoros","CG"=>"Congo (Brazzaville)","CD"=>"Congo (Kinshasa)","CK"=>"Cook Islands","CR"=>"Costa Rica","HR"=>"Croatia","CU"=>"Cuba","CW"=>"Curaçao","CY"=>"Cyprus","CZ"=>"Czech Republic","DK"=>"Denmark","DJ"=>"Djibouti","DM"=>"Dominica","DO"=>"Dominican Republic","EC"=>"Ecuador","EG"=>"Egypt","SV"=>"El Salvador","GQ"=>"Equatorial Guinea","ER"=>"Eritrea","EE"=>"Estonia","ET"=>"Ethiopia","FK"=>"Falkland Islands","FO"=>"Faroe Islands","FJ"=>"Fiji","FI"=>"Finland","FR"=>"France","GF"=>"French Guiana","PF"=>"French Polynesia","TF"=>"French Southern Territories","GA"=>"Gabon","GM"=>"Gambia","GE"=>"Georgia","DE"=>"Germany","GH"=>"Ghana","GI"=>"Gibraltar","GR"=>"Greece","GL"=>"Greenland","GD"=>"Grenada","GP"=>"Guadeloupe","GU"=>"Guam","GT"=>"Guatemala","GG"=>"Guernsey","GN"=>"Guinea","GW"=>"Guinea-Bissau","GY"=>"Guyana","HT"=>"Haiti","HM"=>"Heard Island and McDonald Islands","HN"=>"Honduras","HK"=>"Hong Kong S.A.R., China","HU"=>"Hungary","IS"=>"Iceland","IN"=>"India","ID"=>"Indonesia","IR"=>"Iran","IQ"=>"Iraq","IE"=>"Ireland","IM"=>"Isle of Man","IL"=>"Israel","IT"=>"Italy","CI"=>"Ivory Coast","JM"=>"Jamaica","JP"=>"Japan","JE"=>"Jersey","JO"=>"Jordan","KZ"=>"Kazakhstan","KE"=>"Kenya","KI"=>"Kiribati","KW"=>"Kuwait","KG"=>"Kyrgyzstan","LA"=>"Laos","LV"=>"Latvia","LB"=>"Lebanon","LS"=>"Lesotho","LR"=>"Liberia","LY"=>"Libya","LI"=>"Liechtenstein","LT"=>"Lithuania","LU"=>"Luxembourg","MO"=>"Macao S.A.R., China","MK"=>"Macedonia","MG"=>"Madagascar","MW"=>"Malawi","MY"=>"Malaysia","MV"=>"Maldives","ML"=>"Mali","MT"=>"Malta","MH"=>"Marshall Islands","MQ"=>"Martinique","MR"=>"Mauritania","MU"=>"Mauritius","YT"=>"Mayotte","MX"=>"Mexico","FM"=>"Micronesia","MD"=>"Moldova","MC"=>"Monaco","MN"=>"Mongolia","ME"=>"Montenegro","MS"=>"Montserrat","MA"=>"Morocco","MZ"=>"Mozambique","MM"=>"Myanmar","NA"=>"Namibia","NR"=>"Nauru","NP"=>"Nepal","NL"=>"Netherlands","AN"=>"Netherlands Antilles","NC"=>"New Caledonia","NZ"=>"New Zealand","NI"=>"Nicaragua","NE"=>"Niger","NG"=>"Nigeria","NU"=>"Niue","NF"=>"Norfolk Island","MP"=>"Northern Mariana Islands","KP"=>"North Korea","NO"=>"Norway","OM"=>"Oman","PK"=>"Pakistan","PW"=>"Palau","PS"=>"Palestinian Territory","PA"=>"Panama","PG"=>"Papua New Guinea","PY"=>"Paraguay","PE"=>"Peru","PH"=>"Philippines","PN"=>"Pitcairn","PL"=>"Poland","PT"=>"Portugal","PR"=>"Puerto Rico","QA"=>"Qatar","RE"=>"Reunion","RO"=>"Romania","RU"=>"Russia","RW"=>"Rwanda","BL"=>"Saint Barthélemy","SH"=>"Saint Helena","KN"=>"Saint Kitts and Nevis","LC"=>"Saint Lucia","MF"=>"Saint Martin (French part)","PM"=>"Saint Pierre and Miquelon","VC"=>"Saint Vincent and the Grenadines","WS"=>"Samoa","SM"=>"San Marino","ST"=>"Sao Tome and Principe","SA"=>"Saudi Arabia","SN"=>"Senegal","RS"=>"Serbia","SC"=>"Seychelles","SL"=>"Sierra Leone","SG"=>"Singapore","SX"=>"Sint Maarten","SK"=>"Slovakia","SI"=>"Slovenia","SB"=>"Solomon Islands","SO"=>"Somalia","ZA"=>"South Africa","GS"=>"South Georgia and the South Sandwich Islands","KR"=>"South Korea","SS"=>"South Sudan","ES"=>"Spain","LK"=>"Sri Lanka","SD"=>"Sudan","SR"=>"Suriname","SJ"=>"Svalbard and Jan Mayen","SZ"=>"Swaziland","SE"=>"Sweden","CH"=>"Switzerland","SY"=>"Syria","TW"=>"Taiwan","TJ"=>"Tajikistan","TZ"=>"Tanzania","TH"=>"Thailand","TL"=>"Timor-Leste","TG"=>"Togo","TK"=>"Tokelau","TO"=>"Tonga","TT"=>"Trinidad and Tobago","TN"=>"Tunisia","TR"=>"Turkey","TM"=>"Turkmenistan","TC"=>"Turks and Caicos Islands","TV"=>"Tuvalu","VI"=>"U.S. Virgin Islands","UG"=>"Uganda","UA"=>"Ukraine","AE"=>"United Arab Emirates","GB"=>"United Kingdom","US"=>"United States","UM"=>"United States Minor Outlying Islands","UY"=>"Uruguay","UZ"=>"Uzbekistan","VU"=>"Vanuatu","VA"=>"Vatican","VE"=>"Venezuela","VN"=>"Vietnam","WF"=>"Wallis and Futuna","EH"=>"Western Sahara","YE"=>"Yemen","ZM"=>"Zambia","ZW"=>"Zimbabwe"];
		// $country_code = get_db_field( 'address', 'country', $node->ID, " AND bundle = '" . $node->type . "'" );

		// if ( ! empty( $country_list[$country_code] ) ) {
		// 	$address['country'] = $country_list[$country_code];
		// }

		// $logs .= '[Address]:' . PHP_EOL;
		// $logs .= ' - [country]: ';
		// $logs .= ! empty( $country_list[$country_code] ) ? $country_code . ' | ' . $country_list[$country_code] : '----';
		// $logs .= PHP_EOL;

		// $logs .= ' - [address_1]: ';
		// $logs .= ! empty( $address_1 ) ? $address_1 : '----';
		// $logs .= PHP_EOL;

		// $logs .= ' - [address_2]: ';
		// $logs .= ! empty( $address_2 ) ? $address_2 : '----';
		// $logs .= PHP_EOL;

		// $logs .= ' - [city]: ';
		// $logs .= ! empty( $city ) ? $city : '----';
		// $logs .= PHP_EOL;

		// $logs .= ' - [county]: ';
		// $logs .= ! empty( $county ) ? $county : '----';
		// $logs .= PHP_EOL;

		// $logs .= ' - [postcode]: ';
		// $logs .= ! empty( $postcode ) ? $postcode : '----';
		// $logs .= PHP_EOL;

		// if ( ! $debug ) {
		// 	// address
		// 	update_field( 'field_645892e090e2a', $address, $post_ID );
		// }


		// // Language
		// $languages_list = ['ab'=>'Abkhazian','aa'=>'Afar','af'=>'Afrikaans','ak'=>'Akan','sq'=>'Albanian','am'=>'Amharic','ar'=>'Arabic','hy'=>'Armenian','as'=>'Assamese','ast'=>'Asturian','av'=>'Avar','ae'=>'Avestan','ay'=>'Aymara','az'=>'Azerbaijani','bm'=>'Bambara','ba'=>'Bashkir','eu'=>'Basque','be'=>'Belarusian','bn'=>'Bengali','bh'=>'Bihari','bi'=>'Bislama','bs'=>'Bosnian','br'=>'Breton','bg'=>'Bulgarian','my'=>'Burmese','km'=>'Cambodian','ca'=>'Catalan','ch'=>'Chamorro','ce'=>'Chechen','ny'=>'Chichewa','zh-hans'=>'Chinese, Simplified','zh-hant'=>'Chinese, Traditional','cv'=>'Chuvash','kw'=>'Cornish','co'=>'Corsican','cr'=>'Cree','hr'=>'Croatian','cs'=>'Czech','da'=>'Danish','nl'=>'Dutch','dz'=>'Dzongkha','en'=>'English','en-gb'=>'English, British','eo'=>'Esperanto','et'=>'Estonian','ee'=>'Ewe','fo'=>'Faeroese','fj'=>'Fiji','fil'=>'Filipino','fi'=>'Finnish','fr'=>'French','fy'=>'Frisian','ff'=>'Fulah','gl'=>'Galician','ka'=>'Georgian','de'=>'German','el'=>'Greek','kl'=>'Greenlandic','gn'=>'Guarani','gu'=>'Gujarati','ht'=>'Haitian Creole','ha'=>'Hausa','he'=>'Hebrew','hz'=>'Herero','hi'=>'Hindi','ho'=>'Hiri Motu','hu'=>'Hungarian','is'=>'Icelandic','ig'=>'Igbo','id'=>'Indonesian','ia'=>'Interlingua','ie'=>'Interlingue','iu'=>'Inuktitut','ik'=>'Inupiak','ga'=>'Irish','it'=>'Italian','ja'=>'Japanese','jv'=>'Javanese','kn'=>'Kannada','kr'=>'Kanuri','ks'=>'Kashmiri','kk'=>'Kazakh','ki'=>'Kikuyu','rw'=>'Kinyarwanda','rn'=>'Kirundi','kv'=>'Komi','kg'=>'Kongo','ko'=>'Korean','ku'=>'Kurdish','kj'=>'Kwanyama','ky'=>'Kyrgyz','lo'=>'Laothian','la'=>'Latin','lv'=>'Latvian','ln'=>'Lingala','lt'=>'Lithuanian','xx-lolspeak'=>'Lolspeak','lg'=>'Luganda','lb'=>'Luxembourgish','mk'=>'Macedonian','mg'=>'Malagasy','ms'=>'Malay','ml'=>'Malayalam','dv'=>'Maldivian','mt'=>'Maltese','gv'=>'Manx','mr'=>'Marathi','mh'=>'Marshallese','mo'=>'Moldavian','mn'=>'Mongolian','mi'=>'Māori','na'=>'Nauru','nv'=>'Navajo','ng'=>'Ndonga','ne'=>'Nepali','nd'=>'North Ndebele','se'=>'Northern Sami','nb'=>'Norwegian Bokmål','nn'=>'Norwegian Nynorsk','oc'=>'Occitan','cu'=>'Old Slavonic','or'=>'Oriya','om'=>'Oromo','os'=>'Ossetian','pi'=>'Pali','ps'=>'Pashto','fa'=>'Persian','pl'=>'Polish','pt-br'=>'Portuguese, Brazil','pt'=>'Portuguese, International','pt-pt'=>'Portuguese, Portugal','pa'=>'Punjabi','qu'=>'Quechua','rm'=>'Rhaeto-Romance','ro'=>'Romanian','ru'=>'Russian','sm'=>'Samoan','sg'=>'Sango','sa'=>'Sanskrit','sc'=>'Sardinian','sco'=>'Scots','gd'=>'Scots Gaelic','sr'=>'Serbian','sh'=>'Serbo-Croatian','st'=>'Sesotho','tn'=>'Setswana','sn'=>'Shona','sd'=>'Sindhi','si'=>'Sinhala','ss'=>'Siswati','sk'=>'Slovak','sl'=>'Slovenian','so'=>'Somali','nr'=>'South Ndebele','es'=>'Spanish','su'=>'Sudanese','sw'=>'Swahili','sv'=>'Swedish','gsw-berne'=>'Swiss German','tl'=>'Tagalog','ty'=>'Tahitian','tg'=>'Tajik','ta'=>'Tamil','tt'=>'Tatar','te'=>'Telugu','th'=>'Thai','bo'=>'Tibetan','ti'=>'Tigrinya','to'=>'Tonga','ts'=>'Tsonga','tr'=>'Turkish','tk'=>'Turkmen','tw'=>'Twi','uk'=>'Ukrainian','ur'=>'Urdu','ug'=>'Uyghur','uz'=>'Uzbek','ve'=>'Venda','vi'=>'Vietnamese','cy'=>'Welsh','wo'=>'Wolof','xh'=>'Xhosa','yi'=>'Yiddish','yo'=>'Yoruba','za'=>'Zhuang','zu'=>'Zulu'];
		// $language_code  = get_db_value( 'field_input_language', $node->ID );

		// if ( ! $debug && ! empty( $languages_list[$language_code] ) ) {
		// 	// language
		// 	update_field( 'field_645888842b179', $languages_list[$language_code], $post_ID );
		// }

		// $logs .= '[Language]: ';
		// $logs .= ! empty( $languages_list[$language_code] ) ? $language_code . ' | ' . $languages_list[$language_code] : '----';
		// $logs .= PHP_EOL;


		// // Website
		// $website_title = get_db_field( 'website', 'title', $node->ID, " AND bundle = '" . $node->type . "'" );
		// $website_url   = get_db_field( 'website', 'url', $node->ID, " AND bundle = '" . $node->type . "'" );
		// $website = [
		// 	'title' => $website_title,
		// 	'url'   => $website_url
		// ];

		// $logs .= '[Website]:' . PHP_EOL;

		// $logs .= ' - [title]: ';
		// $logs .= ! empty( $website_title ) ? $website_title : '----';
		// $logs .= PHP_EOL;

		// $logs .= ' - [url]: ';
		// $logs .= ! empty( $website_url ) ? $website_url : '----';
		// $logs .= PHP_EOL;

		// if ( ! $debug ) {
		// 	// website
		// 	update_field( 'field_64589910aa4aa', $website, $post_ID );
		// }


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
		// 	'contacts'  => [],
		// ];

		// foreach( [
		// 	'creator'   => 'creator',
		// 	'publisher' => 'publisher',
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
		// 	update_field( 'field_64589a2d627b0', $people, $post_ID );
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


		// // Classification
		// $services_class_IDs  = [];
		// $services_class_logs = [];
		// $services_class      = get_db_terms( 'services_class', $node->ID );

		// if ( ! empty( $services_class ) ) {
		// 	foreach ( $services_class as $temp_term_name ) {
		// 		if ( ! empty( trim( $temp_term_name ) ) ) {
		// 			$term_ID = 0;
		// 			$term    = get_term_by( 'name', $temp_term_name, 'classification' );

		// 			if ( empty( $term ) ) {
		// 				if ( ! $debug ) {
		// 					$term    = wp_insert_term( $temp_term_name, 'classification' );

		// 					if( is_wp_error( $term ) ){
		// 						s( $term->get_error_message() );
		// 					}
		// 					$term_ID = $term['term_id'];
		// 				}
		// 			}
		// 			elseif ( $term_ID === 0 ) {
		// 				$term_ID = $term->term_id;
		// 			}

		// 			$services_class_IDs[]  = $term_ID;
		// 			$services_class_logs[] = $temp_term_name . ' (' . $term_ID . ')';
		// 		}
		// 	}

		// 	if ( ! $debug ) {
		// 		wp_set_post_terms( $post_ID, $services_class_IDs, 'classification' );
		// 	}
		// }

		// $logs .= '[Classification]: ';
		// $logs .= ! empty( $services_class_logs ) ? implode( ', ', $services_class_logs ) : '----';
		// $logs .= PHP_EOL;


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
						window.location = '<?php echo get_home_url( null, '?import=organisation&key=013b0f890d204a522a7e462d1dfa93e5&item=' . $i ); ?>';
					}, 0 );
				};
			</script>
		<?php
	}

	die();
} );