<?php

/*

Users:
http://weadapt/web/?import=users&key=013b0f890d204a522a7e462d1dfa93e5&node=47661


mike-dev
85dCXpavMK!zyZAQ

*/

if (
	is_admin() ||
	! isset( $_GET['import'] ) ||
	$_GET['import'] != 'users' ||
	! isset( $_GET['key'] ) ||
	$_GET['key'] != '013b0f890d204a522a7e462d1dfa93e5'
) return;


add_action( 'init', function() {

	// Database
	global $wpdb;
	global $debug;
	global $error_logs;
	global $media_logs;
	global $drupal_DB;

	$temp_logs = [];

	$drupal_DB = new wpdb( 'lenvan_weadapt_2', 'ItRmj6IEM', 'lenvan_weadapt_2', 'lenvan.cal24.pl' ); // $dbuser, $dbpassword, $dbname, $dbhost


	// Get Data
	$all_nodes = get_transient( "import_all_users" );

	if ( false === $all_nodes ) {
		$all_nodes    = [];
		$all_db_users = $drupal_DB->get_results("SELECT * FROM users");

		if ( empty( $all_db_users ) ) return;

		foreach ( $all_db_users as $key => $node ) {
			if ( ! empty( $node->uid ) ) {
				$all_nodes[] = (object) [
					'ID'      => ! empty( $node->uid ) ? (int) $node->uid : '',
					'name'    => ! empty( $node->name ) ? $node->name : '',
					'pass'    => ! empty( $node->pass ) ? $node->pass : '',
					'mail'    => ! empty( $node->mail ) ? $node->mail : '',
					'created' => ! empty( $node->created ) ? (int) $node->created : '',
					'status'  => ! empty( $node->status ) ? (int) $node->status : 0,
					'data'    => ! empty( $node->data ) ? maybe_unserialize($node->data) : [],
				];
			}
		}

		set_transient( "import_all_users", $all_nodes, DAY_IN_SECONDS );
	}
	if ( empty( $all_nodes ) ) return;


	// Import
	$loop_start = ! empty( $_GET['item'] ) ? intval( $_GET['item'] ) : 1;
	$loop_end   = isset( $_GET['node'] ) ? count($all_nodes) : ($loop_start + 20);

	for ( $i = $loop_start; $i <= $loop_end; $i++ ) {
		if ( empty( $all_nodes[$i-1] ) ) {
			echo 'END !!!';
			die();
		}


		// Variables
		$logs       = '';
		$error_logs = '';
		$debug      = false;
		$is_new     = true;
		$is_by_slug = false;
		$user_ID    = 0;
		$node       = $all_nodes[$i-1];


		// Temp
		if ( isset( $_GET['node'] ) ) {
			if ( $node->ID !== intval( $_GET['node'] ) ) continue;
		}

		// // URL Alias
		// $url_alias = get_db_user_url_alias( $node->ID );

		// $logs .= '[URL alias]: ';
		// $logs .= ! empty( $url_alias ) ? $url_alias : '----';
		// $logs .= PHP_EOL;

		// if ( ! empty( $url_alias ) ) {
		// 	$url_alias_data = explode( '/', $url_alias );

		// 	$user_name = end($url_alias_data);
		// }
		// else {
		// 	$user_name = sanitize_title( $node->name );
		// }

		// // Nicename may not be longer than 50 characters.
		// if ( mb_strlen( $user_name ) > 50) {
		// 	$user_name = mb_substr( $user_name, 0, 50 );
		// }


		// $userdata = [
		// 	'user_login'      => $user_name,
		// 	'user_nicename'   => $user_name,
		// 	'user_email'      => $node->mail,
		// 	'user_pass'       => $node->pass
		// ];

		// if ( ! empty( $node->created ) ) {
		// 	$userdata['user_registered'] = date( 'Y-m-d H:i:s', $node->created + 60*60 );
		// }

		$isset_user = get_user_by( 'email', $node->mail );



		if ( ! empty( $isset_user->data ) ) {
			$is_new         = false;
			$user_ID        = $isset_user->data->ID;

			$logs .= '[Item]: ' . $i . '/' . count( $all_nodes ) . PHP_EOL;

			$logs .= '[Email]: ' . $node->mail . PHP_EOL;

			// Domain Access
			$domains_access = get_db_domain_access( $node->ID );

			if ( ! $debug && ! empty( $domains_access ) ) {
				foreach ( $domains_access as $site_ID ) {
					if ( ! is_user_member_of_blog( $user_ID, $site_ID ) ) {
						add_user_to_blog( $site_ID, $user_ID, 'contributor' );
					}
				}
			}

			$logs .= '[Domain Access]: ';
			$logs .= ! empty( $domains_access ) ? implode( ', ', $domains_access ) : '----';
			$logs .= PHP_EOL;
		}














		// if ( ! empty( $isset_user->data ) ) {
		// 	$is_new         = false;
		// 	$user_ID        = $isset_user->data->ID;
		// 	$userdata['ID'] = $user_ID;
		// }
		// else {
		// 	$isset_user_nickname = get_user_by( 'slug', $user_name );

		// 	if ( ! empty( $isset_user_nickname->data ) ) {
		// 		$is_new         = false;
		// 		$is_by_slug     = true;
		// 		$user_ID        = $isset_user_nickname->data->ID;
		// 		$userdata['ID'] = $user_ID;
		// 	}
		// }

		// // Logs
		// $logs .= '[Item]: ' . $i . '/' . count( $all_nodes ) . PHP_EOL;
		// $logs .= '[Node ID]: ' . $node->ID . PHP_EOL;
		// $logs .= '[Is New]: ' . ( ( $is_new ) ? 1 : 0 ) . PHP_EOL;
		// $logs .= '[Login]: ' . $user_name . PHP_EOL;
		// $logs .= '[Nicename]: ' . $user_name . PHP_EOL;
		// $logs .= '[Email]: ' . $node->mail . PHP_EOL;
		// $logs .= '[Pass]: ' . $node->pass . PHP_EOL;

		// $logs .= '[Created]: ';

		// if ( ! empty( $node->created ) ) {
		// 	$logs .= date( 'Y-m-d H:i:s', $node->created + 60*60 );
		// }
		// $logs .= PHP_EOL;


		// // First Name
		// $first_name = get_db_value( 'field_first_name', $node->ID );

		// $userdata['first_name'] = $first_name;

		// $logs .= '[First Name]: ' . $first_name . PHP_EOL;


		// // Last Name
		// $last_name = get_db_value( 'field_last_name', $node->ID );

		// $userdata['last_name'] = $last_name;

		// $logs .= '[Last Name]: ' . $last_name . PHP_EOL;


		// // Display Name
		// $display_name_data = [];

		// if ( ! empty( $first_name ) ) {
		// 	$display_name_data[] = $first_name;
		// }
		// if ( ! empty( $last_name ) ) {
		// 	$display_name_data[] = $last_name;
		// }

		// $display_name = implode( ' ', $display_name_data );

		// $userdata['display_name'] = $display_name;

		// $logs .= '[Display Name]: ' . $display_name . PHP_EOL;


		// // Description
		// $about = get_db_value( 'field_about', $node->ID );
		// $about = preg_replace( "/<p[^>]*>(?:\s|&nbsp;)*<\/p>/", '', $about );
		// $about = str_replace( '&nbsp;</', '</', $about );
		// $about = str_replace( '&nbsp;', ' ', $about );
		// $about = wp_kses( $about, [
		// 	'a' => [
		// 		'href'  => true,
		// 		'title' => true,
		// 	],
		// 	'span'   => [],
		// 	'br'     => [],
		// 	'em'     => [],
		// 	'strong' => [],
		// 	'p'      => []
		// ] );
		// $about = trim( $about );

		// $userdata['description'] = $about;

		// $logs .= '[Description]: ' . $about . PHP_EOL;


		// // Insert User
		// if ( ! $is_new ) {
		// 	unset( $userdata['user_login'] );

		// 	$user_ID = wp_update_user( $userdata );

		// 	// Update user_login
		// 	$update_login = $wpdb->update(
		// 		$wpdb->users,
		// 		['user_login' => $user_name],
		// 		['ID' => $user_ID],
		// 		['%s'],
		// 		['%d']
		// 	);

		// 	$logs .= '[Update Login]: ' . ( ( $update_login !== false ) ? 1 : 0 ) . PHP_EOL;

		// 	if ( $is_by_slug ) {

		// 		// Update user_email
		// 		$update_login = $wpdb->update(
		// 			$wpdb->users,
		// 			['user_email' => $node->mail],
		// 			['ID' => $user_ID],
		// 			['%s'],
		// 			['%d']
		// 		);

		// 		$logs .= '[Update Email]: ' . ( ( $update_login !== false ) ? 1 : 0 ) . PHP_EOL;
		// 	}
		// }
		// else {
		// 	$user_ID = wp_insert_user( $userdata );
		// }

		// if ( is_wp_error( $user_ID ) ) {
		// 	$error_logs .= $user_ID->get_error_message();
		// }
		// else {
		// 	$logs .= '[User ID]: ' . $user_ID . PHP_EOL;

		// 	// if ( ! $debug ) {
		// 	// 	update_user_meta( $user_ID, 'is_new_import', 1);
		// 	// }

		// 	$user_url = get_author_posts_url( $user_ID );
		// 	$user_url_alias = rtrim( str_replace( get_home_url() . '/', '', $user_url ), '/' );

		// 	if ( $url_alias !== $user_url_alias ) {
		// 		$url_logs  = $url_alias . ' (Email: ' . $node->mail . '; Node ID: ' . $node->ID . ');' . PHP_EOL;
		// 		$url_logs .= $user_url_alias . ' (Email: ' . $node->mail . '; User ID: ' . $user_ID . ');' . PHP_EOL;
		// 		$url_logs .= PHP_EOL;

		// 		file_put_contents( '../logs/url-alias/user.log', $url_logs, FILE_APPEND );
		// 	}


		// 	// Update hashed pass
		// 	$update_pass = $wpdb->update(
		// 		$wpdb->users,
		// 		['user_pass' => $node->pass],
		// 		['ID' => $user_ID],
		// 		['%s'],
		// 		['%d']
		// 	);

		// 	$logs .= '[Update Pass]: ' . ( ( $update_pass !== false ) ? 1 : 0 ) . PHP_EOL;


		// 	// Status
		// 	$status = ! empty( $node->status ) ? 1 : 0;

		// 	if ( ! $debug ) {
		// 		update_field( 'status', $status, 'user_' . $user_ID );
		// 	}
		// 	$logs .= '[Status]: ' . $status . PHP_EOL;


		// 	// Avatar
		// 	$avatar_ID  = get_db_field( 'image', 'fid', $node->ID, " AND bundle = 'user'" );
		// 	$avatar_url = get_db_file_managed_url( $avatar_ID );

		// 	if ( ! empty( $avatar_url ) ) {
		// 		$avatar_ID = upload_attachment_from_url( $avatar_url, 0, implode( ' ', [$first_name, $last_name ] ), '', $avatar_ID );

		// 		if ( ! $debug ) {
		// 			update_field( 'avatar', $avatar_ID, 'user_' . $user_ID );
		// 		}

		// 		// Logs
		// 		$temp_media_logs = PHP_EOL;
		// 		$temp_media_logs .= 'user_ID: ' . $user_ID . PHP_EOL;
		// 		$temp_media_logs .= 'node_ID: ' . $node->ID . PHP_EOL;
		// 		$temp_media_logs .= 'old src: ' . $avatar_url . PHP_EOL;
		// 		$temp_media_logs .= 'new src: ' . ( ! empty( $avatar_ID ) ? wp_get_attachment_image_url( $avatar_ID, 'full' ) : 'Error !!!' ) . PHP_EOL;

		// 		$media_logs['thumbnail'] = $temp_media_logs;

		// 		if ( empty( $avatar_ID ) ) {
		// 			$error_logs .= PHP_EOL . 'Content Image:' . PHP_EOL;
		// 			$error_logs .= $temp_media_logs;
		// 		}
		// 	}
		// 	else {
		// 		if ( ! $debug ) {
		// 			update_field( 'avatar', 0, 'user_' . $user_ID );
		// 		}
		// 	}

		// 	$logs .= '[Avatar]:' . PHP_EOL;
		// 	$logs .= ! empty( $avatar_ID ) ? '<img src="' . wp_get_attachment_image_url( $avatar_ID, 'full' ) . '">' :'----';
		// 	$logs .= PHP_EOL;


		// 	// Job Title
		// 	$job_title = get_db_value( 'field_position', $node->ID );

		// 	if ( ! $debug ) {
		// 		update_field( 'job_title', $job_title, 'user_' . $user_ID );
		// 	}
		// 	$logs .= '[Job Title]: ';
		// 	$logs .= ! empty( $job_title ) ? $job_title : '----';
		// 	$logs .= PHP_EOL;


		// 	// Subjects of interest
		// 	$interest_IDs  = [];
		// 	$interest_logs = [];
		// 	$interest      = get_db_terms( 'interests', $node->ID );

		// 	if ( ! empty( $interest ) ) {
		// 		foreach ( $interest as $temp_term_name ) {
		// 			if ( ! empty( $temp_term_name ) ) {
		// 				$term_ID = 0;
		// 				$term    = get_term_by( 'name', $temp_term_name, 'interest' );

		// 				if ( empty( $term ) ) {
		// 					if ( ! $debug ) {
		// 						$term    = wp_insert_term( $temp_term_name, 'interest' );
		// 						$term_ID = $term['term_id'];
		// 					}
		// 				}
		// 				elseif ( $term_ID === 0 ) {
		// 					$term_ID = $term->term_id;
		// 				}

		// 				$interest_IDs[]  = $term_ID;
		// 				$interest_logs[] = $temp_term_name . ' (' . $term_ID . ')';
		// 			}
		// 		}

		// 		if ( ! $debug ) {
		// 			update_field( 'interest', $interest_IDs, 'user_' . $user_ID );
		// 		}
		// 	}

		// 	$logs .= '[Subjects of interest]: ';
		// 	$logs .= ! empty( $interest_logs ) ? implode( ', ', $interest_logs ) : '----';
		// 	$logs .= PHP_EOL;


		// 	// Referrer
		// 	$referrer = get_db_value( 'field_referrer', $node->ID );

		// 	if ( ! $debug ) {
		// 		update_field( 'referrer', $referrer, 'user_' . $user_ID );
		// 	}
		// 	$logs .= '[Referrer]: ';
		// 	$logs .= ! empty( $referrer ) ? $referrer : '----';
		// 	$logs .= PHP_EOL;


		// 	// Content Sought
		// 	$content_sought = get_db_value( 'field_content_sought', $node->ID );

		// 	if ( ! $debug ) {
		// 		update_field( 'content_sought', $content_sought, 'user_' . $user_ID );
		// 	}
		// 	$logs .= '[Content Sought]: ';
		// 	$logs .= ! empty( $content_sought ) ? $content_sought : '----';
		// 	$logs .= PHP_EOL;


		// 	// Company
		// 	$company = get_db_value( 'field_company', $node->ID );

		// 	if ( ! $debug ) {
		// 		update_field( 'company', $company, 'user_' . $user_ID );
		// 	}
		// 	$logs .= '[Company]: ';
		// 	$logs .= ! empty( $company ) ? $company : '----';
		// 	$logs .= PHP_EOL;


		// 	// Website URL
		// 	$website_url = get_db_field( 'website', 'url', $node->ID );

		// 	if ( ! $debug ) {
		// 		update_field( 'website_url', $website_url, 'user_' . $user_ID );
		// 	}
		// 	$logs .= '[Website URL]: ';
		// 	$logs .= ! empty( $website_url ) ? $website_url : '----';
		// 	$logs .= PHP_EOL;


		// 	// Linkedin URL
		// 	$linkedin_url = get_db_value( 'field_linkedin_url', $node->ID );

		// 	if ( ! $debug ) {
		// 		update_field( 'linkedin_url', $linkedin_url, 'user_' . $user_ID );
		// 	}
		// 	$logs .= '[Linkedin URL]: ';
		// 	$logs .= ! empty( $linkedin_url ) ? $linkedin_url : '----';
		// 	$logs .= PHP_EOL;



		// 	// User Roles
		// 	// $roles_IDs = [
		// 	// 	81 => XXX, // a@a admin
		// 	// 	56 => XXX, // a@a survey admin
		// 	// 	3  => XXX, // administrator
		// 	// 	// 1  => XXX, // anonymous user
		// 	// 	// 2  => XXX, // authenticated user
		// 	// 	46 => XXX, // awb admin
		// 	// 	// 11 => XXX, // Freelancer
		// 	// 	51 => XXX, // isdb admin
		// 	// 	// 21 => XXX, // ouranos admin
		// 	// 	26 => XXX, // placard admin
		// 	// 	16 => XXX, // pro user
		// 	// 	36 => XXX, // rbd admin
		// 	// 	76 => XXX, // WAC admin
		// 	// 	6  => XXX, // weadapt admin
		// 	// 	31 => XXX, // weadapt admin - no notifications
		// 	// 	// 41 => XXX, // wetransform admin
		// 	// ];

		// 	// old_ID => new_ID, // url | drupal sitename | pdf | comment
		// 	// $domains_IDs = [
		// 	// 	1  => 1, // weadapt.org
		// 	// 	31 => 2, // adaptationataltitude.org | Adaptation At Altitude | Adaptation at Altitude (Zoi Environment Network for SDC)
		// 	// 	11 => 3, // can-adapt.ca | Inspiring Climate Action | Adaptation Learning Network (Royal Roads University) | redirect from https://adaptationlearningnetwork.com/
		// 	// 	21 => 4, // adaptationwithoutborders.org | Adaptation Without Borders | Adaptation Without Borders (ODI, SEI, IDDRI)
		// 	// 	16 => 5, // wetransform.dev | weTRANSFORM | weTRANSFORM (SEI, IRDI, ICoE)
		// 	// 	26 => 6, // cckh.weadapt.org | ISDB | Climate Change Knowledge Hub (Islamic Development Bank)
		// 	// 	6  => 7, // energyadaptation.ouranos.ca | Ouranos | Energy Adaptation Map (Ouranos)
		// 	// 	36 => 8, // communities.adaptationportal.gca.org | Water Adaptation Community | Water Adaptation Community (Global Centre on Adaptation)
		// 	// 	96 => 10, // MAIA
		// 	// ];
		// 	$roles_logs    = [];
		// 	$roles_results = $drupal_DB->get_results("SELECT rid FROM users_roles WHERE uid LIKE '$node->ID'");
		// 	$roles         = array_column( array_map( function( $item ) {
		// 		$rid = intval( $item->rid );

		// 		return ! empty( $rid ) ? [$rid] : [];
		// 	}, $roles_results), 0);


		// 	if ( ! empty( $roles ) ) {
		// 		foreach ( $roles as $role_ID ) {
		// 			$temp_role    = 'administrator';
		// 			$temp_blog_ID = 0;

		// 			switch ( $role_ID ) {
		// 				case 81: // a@a admin
		// 					$temp_blog_ID = 2;
		// 					break;

		// 				case 56: // a@a survey admin
		// 					$temp_blog_ID = 2;
		// 					$temp_role    = 'survey-administrator';
		// 					break;

		// 				case 3: // administrator
		// 				case 6: // weadapt admin
		// 					$temp_blog_ID = 1;
		// 					break;

		// 				case 31: // weadapt admin - no notifications
		// 					$temp_blog_ID = 1;
		// 					$temp_role    = 'no-notifs-administrator';
		// 					break;

		// 				case 46: // awb admin
		// 					$temp_blog_ID = 4;
		// 					break;

		// 				case 51: // isdb admin
		// 					$temp_blog_ID = 6;
		// 					break;

		// 				case 16: // pro user
		// 					$temp_blog_ID = 6;
		// 					$temp_role    = 'pro-user';
		// 					break;

		// 				case 36: // rbd admin
		// 					$temp_blog_ID = 3;
		// 					break;

		// 				case 96: // maia admin
		// 					$temp_blog_ID = 10;
		// 					$temp_role    = 'administrator';
		// 					break;

		// 				// case 26: // placard admin
		// 				// 	$temp_blog_ID = 1;
		// 				// 	$temp_role    = 'administrator';
		// 				// 	break;
		// 			}

		// 			if ( ! empty( $temp_blog_ID ) ) {
		// 				add_user_to_blog( $temp_blog_ID, $user_ID, $temp_role );

		// 				$roles_logs[] = $temp_blog_ID . '|' . $temp_role;
		// 			}
		// 			else {
		// 				$roles_logs[] = $role_ID . '| Error';
		// 			}
		// 		}
		// 	}

		// 	$logs .= '[Roles]: ';
		// 	$logs .= ! empty( $roles_logs ) ? implode( ', ', $roles_logs ) : '----';
		// 	$logs .= PHP_EOL;


		// 	// Taxonomy Roles
		// 	$tax_roles_IDs  = [];
		// 	$tax_roles_logs = [];
		// 	$tax_roles      = get_db_terms( 'role', $node->ID );

		// 	if ( ! empty( $tax_roles ) ) {
		// 		foreach ( $tax_roles as $temp_term_name ) {
		// 			$term_ID = 0;
		// 			$term    = get_term_by( 'name', $temp_term_name, 'role' );

		// 			if ( empty( $term ) ) {
		// 				if ( ! $debug ) {
		// 					$term    = wp_insert_term( $temp_term_name, 'role' );
		// 					$term_ID = $term['term_id'];
		// 				}
		// 			}
		// 			elseif ( $term_ID === 0 ) {
		// 				$term_ID = $term->term_id;
		// 			}

		// 			$tax_roles_IDs[]  = $term_ID;
		// 			$tax_roles_logs[] = $temp_term_name . ' (' . $term_ID . ')';
		// 		}

		// 		if ( ! $debug ) {
		// 			update_field( 'role', $tax_roles_IDs, 'user_' . $user_ID );
		// 		}
		// 	}

		// 	$logs .= '[Taxonomy Roles]: ';
		// 	$logs .= ! empty( $tax_roles_logs ) ? implode( ', ', $tax_roles_logs ) : '----';
		// 	$logs .= PHP_EOL;


		// 	// Organisations
		// 	$organisations = get_post_ids_by_target_ids( 'organisation', $node->ID, 'organisation' );

		// 	if ( ! empty( $organisations ) ) {
		// 		if ( ! $debug ) {
		// 			update_field( 'organisations', array_keys( $organisations ), 'user_' . $user_ID );
		// 		}
		// 	}

		// 	$logs .= '[Organisations]: ';
		// 	$logs .= ! empty( $organisations ) ? implode(', ', array_map(function ($key, $value) {
		// 		return $value . ' (' . $key . ')';
		// 	}, array_keys($organisations), $organisations)) : '----';
		// 	$logs .= PHP_EOL;


		// 	// Address
		// 	$address = [
		// 		'country' => get_db_field( 'address', 'country', $node->ID ),
		// 		'city'    => get_db_field( 'address', 'locality', $node->ID ),
		// 		'county'  => get_db_field( 'address', 'administrative_area', $node->ID )
		// 	];

		// 	if ( ! $debug ) {
		// 		update_field( 'address', $address, 'user_' . $user_ID );
		// 	}

		// 	$logs .= '[Address]:';
		// 	$logs .= ! empty( $address ) ? implode('', array_map(function ($key, $value) {
		// 		return PHP_EOL . '— [' . $key . ']: ' . ( ! empty( $value ) ? $value : '----' );
		// 	}, array_keys($address), $address)) : '----';
		// 	$logs .= PHP_EOL;


		// 	// Personal contact form
		// 	$contact_form = ! empty( $node->data['contact'] ) ? 1 : 0;

		// 	if ( ! $debug ) {
		// 		update_field( 'contact_form', $contact_form, 'user_' . $user_ID );
		// 	}
		// 	$logs .= '[Personal contact form]: ' . $contact_form . PHP_EOL;


		// 	// Plaintext email only
		// 	$mimemail_textonly = ! empty( $node->data['mimemail_textonly'] ) ? 1 : 0;

		// 	if ( ! $debug ) {
		// 		update_field( 'mimemail_textonly', $mimemail_textonly, 'user_' . $user_ID );
		// 	}
		// 	$logs .= '[Plaintext email only]: ' . $mimemail_textonly . PHP_EOL;


		// 	// Newsletter
		// 	// ? 0 : 1 !!!
		// 	$newsletter = ! empty( intval( $drupal_DB->get_var("SELECT deleted FROM field_data_field_subscribe_to_updates WHERE entity_id = $node->ID" ) ) ) ? 0 : 1;

		// 	if ( ! $debug ) {
		// 		update_field( 'newsletter', $newsletter, 'user_' . $user_ID );
		// 	}
		// 	$logs .= '[Newsletter]: ' . $newsletter . PHP_EOL;


		// 	// Agreement
		// 	$agreement = ! empty( get_db_value( 'field_agreement', $node->ID ) ) ? 1 : 0;

		// 	if ( ! $debug ) {
		// 		update_field( 'agreement', $agreement, 'user_' . $user_ID );
		// 	}
		// 	$logs .= '[Agreement]: ' . $agreement . PHP_EOL;



		// 	// Domain Access
		// 	$domains_access = get_db_domain_access( $node->ID );

		// 	if ( ! $debug && ! empty( $domains_access ) ) {
		// 		foreach ( $domains_access as $site_ID ) {
		// 			if ( ! is_user_member_of_blog( $user_ID, $site_ID ) ) {
		// 				add_user_to_blog( $site_ID, $user_ID, 'contributor' );
		// 			}
		// 		}
		// 	}

		// 	$logs .= '[Domain Access]: ';
		// 	$logs .= ! empty( $domains_access ) ? implode( ', ', $domains_access ) : '----';
		// 	$logs .= PHP_EOL;
		// }



		// $error_logs_temp = $logs;

		// // Logs Report
		// if ( ! empty( $logs ) ) {
		// 	$logs .= PHP_EOL . "-------------------------" . PHP_EOL . PHP_EOL;

		// 	file_put_contents( '../logs/user/' . get_logs_file_name( $i, 'logs' ), $logs, FILE_APPEND );
		// }

		// // Logs Error Report
		// if ( ! empty( $error_logs ) ) {
		// 	$error_logs = $error_logs_temp . $error_logs;

		// 	$error_logs .= PHP_EOL . "-------------------------" . PHP_EOL . PHP_EOL;

		// 	file_put_contents( '../logs/errors/user.log', $error_logs, FILE_APPEND );
		// }

		// // Logs Media
		// if ( ! empty( $media_logs ) ) {
		// 	foreach ( $media_logs as $media_log ) {
		// 		$media_log .= PHP_EOL . "-------------------------" . PHP_EOL;

		// 		file_put_contents( '../logs/media/user.log', $media_log, FILE_APPEND );
		// 	}
		// }


		// Logs Output
		?>
		<style>
			img {
				max-width: 300px;
				max-height: 300px;
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
						window.location = '<?php echo get_home_url( null, '?import=users&key=013b0f890d204a522a7e462d1dfa93e5&item=' . $i ); ?>';
					}, 0 );
				};
			</script>
		<?php
	}

	die();
} );






// $pass = '85dCXpavMK!zyZAQ';
// $pass_wp = '$2y$10$XGX.bOaCzjOMCk14toEcY.ybzBi4mkdBygNpwu6hTXNK.hK5XabCq';
// $pass_dp = '$S$Dx3JcyzD8kY6wMW.mFOi5k/ckrxFJ3.dWld0uFIlPDI3/q43xV2e';
// s(wp_hash_password( $pass ));
// s(wp_check_password( $pass, $pass_dp ));
// s(wp_check_password( $pass, $pass_wp ));
// s(user_check_password( $pass, (object) ['pass' => $pass_dp] ));