<?php


/**
 * Get Drupal IDs
 */
function get_db_target_ids( $field = '', $node_ID = 0, $value = '_target_id' ) {
	global $drupal_DB;

	$output      = [];
	$temp_output = [];

	$col_name = 'field_' . $field . $value;
	$db_table = 'field_data_field_' . $field;

	$results = $drupal_DB->get_results("SELECT * FROM $db_table WHERE entity_id LIKE '$node_ID'");

	if ( ! empty( $results ) ) {
		foreach ( $results as $result ) {
			if ( ! empty( $result->$col_name ) ) {
				if ( isset( $result->delta ) ) {
					$delta          = (int) $result->delta;
					$output[$delta] = (int) $result->$col_name;
				}
				else {
					$temp_output[] = (int) $result->$col_name;
				}
			}
		}
	}

	return array_merge( $output, $temp_output );
}


/**
 * Get Drupal Terms
 */
function get_db_terms( $field = '', $node_ID = 0 ) {
	global $drupal_DB;

	$output      = [];
	$temp_output = [];

	$col_name = 'field_' . $field . '_tid';
	$db_table = 'field_data_field_' . $field;

	$results = $drupal_DB->get_results("SELECT * FROM $db_table WHERE entity_id LIKE '$node_ID'");

	if ( ! empty( $results ) ) {
		foreach ( $results as $result ) {
			if ( ! empty( $result->$col_name ) ) {
				$term_name = get_db_term_name( (int) $result->$col_name );

				if ( isset( $result->delta ) ) {
					$delta          = (int) $result->delta;
					$output[$delta] = $term_name;
				}
				else {
					$temp_output[] = $term_name;
				}
			}
		}
	}

	return array_merge( $output, $temp_output );
}


/**
 * Get Drupal Webform Terms
 */
function get_db_webform_terms( $field = '', $data_key = '', $node_ID = 0 ) {
	global $drupal_DB;

	$output       = [];
	$db_data      = [];
	$temp_db_data = [];

	$col_name = 'field_' . $field . '_value';
	$db_table = 'field_data_field_' . $field;

	$results = $drupal_DB->get_results("SELECT * FROM $db_table WHERE entity_id LIKE '$node_ID'");

	if ( ! empty( $results ) ) {
		foreach ( $results as $result ) {
			if ( ! empty( $result->$col_name ) ) {
				if ( isset( $result->delta ) ) {
					$delta          = (int) $result->delta;
					$db_data[$delta] = $result->$col_name;
				}
				else {
					$temp_db_data[] = $result->$col_name;
				}
			}
		}
	}

	$db_names_data = [];
	$db_data       = array_merge( $db_data, $temp_db_data );

	if ( ! empty( $db_data ) ) {
		$names_result = $drupal_DB->get_row("SELECT extra FROM webform_component WHERE form_key LIKE '$data_key'");

		if ( ! empty( $names_result->extra ) ) {
			$title_data = str_replace("\n", "\\n", $names_result->extra );
			$title_data = str_replace("\r", "\\r", $title_data);
			$title_data = str_replace("\t", "\\t", $title_data);
			$title_data = str_replace("\\r\\n", "\\n", $title_data);

			$title_data  = maybe_unserialize( $title_data );
			$title_items = ! empty( $title_data['items'] ) ? explode( "\\n", $title_data['items'] ) : [];

			if ( ! empty( $title_items ) ) {
				foreach ($title_items as $item) {
					$temp_title_data = explode( '|', $item );

					if ( ! empty( $temp_title_data[0] ) && ! empty( $temp_title_data[1] ) ) {
						$temp_title = trim($temp_title_data[1]);

						if ( substr($temp_title, -1) === ',' ) {
							$db_names_data[trim($temp_title_data[0])] = substr($temp_title, 0, -1);
						}
						else {
							$db_names_data[trim($temp_title_data[0])] = $temp_title;
						}
					}
				}
			}
		}

		foreach ( $db_data as $db_item ) {
			if ( ! empty( $db_names_data[$db_item] ) ) {
				$output[] = $db_names_data[$db_item];
			}
		}
	}

	return $output;
}


/**
 * Get Drupal DB Videos
 */
function get_db_videos( $node_ID = 0 ) {
	global $drupal_DB;
	$output      = [];
	$temp_output = [];

	$results = $drupal_DB->get_results("SELECT * FROM field_data_field_video WHERE entity_id LIKE '$node_ID'");

	if ( ! empty( $results ) ) {
		foreach ( $results as $result ) {
			if ( ! empty( $result->field_video_video_url ) ) {
				if ( isset( $result->delta ) ) {
					$delta          = (int) $result->delta;
					$output[$delta] = [
						'url'         => $result->field_video_video_url,
						'description' => fix_wp_excerpt( $result->field_video_description )
					];
				}
				else {
					$temp_output[] = [
						'url'         => $result->field_video_video_url,
						'description' => fix_wp_excerpt( $result->field_video_description )
					];
				}
			}
		}
	}

	return array_merge( $output, $temp_output );
}


/**
 * Get Drupal DB Solution Videos
 */
function get_db_solution_videos( $node_ID = 0 ) {
	global $drupal_DB;
	$output      = [];
	$temp_output = [];

	$solution_results = $drupal_DB->get_results("SELECT * FROM field_data_field_solution_video WHERE entity_id LIKE '$node_ID'");

	if ( ! empty( $solution_results ) ) {
		foreach ( $solution_results as $solution_result ) {
			if ( ! empty( $solution_result->field_solution_video_video_url ) ) {
				if ( isset( $solution_result->delta ) ) {
					$delta          = (int) $solution_result->delta;
					$output[$delta] = [
						'url'         => $solution_result->field_solution_video_video_url,
						'description' => fix_wp_excerpt( $solution_result->field_solution_video_description )
					];
				}
				else {
					$temp_output[] = [
						'url'         => $solution_result->field_solution_video_video_url,
						'description' => fix_wp_excerpt( $solution_result->field_solution_video_description )
					];
				}
			}
		}
	}

	$url_results = $drupal_DB->get_results("SELECT * FROM field_data_field_video_url WHERE entity_id LIKE '$node_ID'");

	if ( ! empty( $url_results ) ) {
		foreach ( $url_results as $url_result ) {
			if ( ! empty( $url_result->field_video_url_url ) ) {
				$temp_output[] = [
					'url'         => $url_result->field_video_url_url,
					'description' => fix_wp_excerpt( $url_result->field_video_url_title )
				];
			}
		}
	}

	return array_merge( $output, $temp_output );
}


/**
 * Get Drupal Meta Value
 */
function get_db_field( $field = '', $name = '', $node_ID = 0, $where = '' ) {
	global $drupal_DB;

	$col_name = 'field_' .  $field . '_' . $name;
	$db_table = 'field_data_field_' .  $field;

	$result = $drupal_DB->get_row("SELECT $col_name FROM $db_table WHERE entity_id LIKE '$node_ID'$where");

	return ! empty( $result->$col_name ) ? $result->$col_name : '';
}


/**
 * Get Drupal Node Value
 */
function get_db_value( $field = '', $node_ID = 0 ) {
	global $drupal_DB;

	$db_name  = $field . '_value';
	$db_table = 'field_data_' .  $field;

	$result = $drupal_DB->get_row("SELECT $db_name FROM $db_table WHERE entity_id LIKE '$node_ID'");

	return ! empty( $result->$db_name ) ? $result->$db_name : '';
}


/**
 * Get Drupal File Managed URL
 */
function get_db_file_managed_url( $file_ID = 0 ) {
	global $drupal_DB;

	$result = $drupal_DB->get_row("SELECT uri FROM file_managed WHERE fid LIKE '" . $file_ID . "'");

	return ! empty( $result->uri ) ? str_replace( 'public://', 'https://www.weadapt.org/sites/weadapt.org/files/', $result->uri ) : '';
}


/**
 * Get Drupal File Alt
 */
function get_db_file_alt( $field = '', $file_ID = 0 ) {
	global $drupal_DB;

	$col_name  = 'field_' .  $field . '_alt';
	$db_table = 'field_data_field_' .  $field;
	$db_where = 'field_' .  $field . '_fid';

	$result = $drupal_DB->get_row("SELECT $col_name FROM $db_table WHERE $db_where LIKE '" . $file_ID . "'");

	return ! empty( $result->$col_name ) ? $result->$col_name : '';
}


/**
 * Get Drupal Image Caption
 */
function get_db_image_caption( $node_ID = 0 ) {
	global $drupal_DB;

	$result = $drupal_DB->get_row("SELECT caption FROM field_image_field_caption WHERE entity_id LIKE '$node_ID'");

	return ! empty( $result->caption ) ? $result->caption : '';
}


/**
 * Get Drupal Node Value
 */
function get_db_node( $field = '', $node_ID = 0 ) {
	global $drupal_DB;

	$result = $drupal_DB->get_row("SELECT $field FROM node WHERE nid LIKE '$node_ID'");

	return ! empty( $result->$field ) ? $result->$field : '';
}


/**
 * Get Drupal Taxonomy Term Name
 */
function get_db_term_name( $tid = 0 ) {
	global $drupal_DB;

	$db_name  = 'name';
	$db_table = 'taxonomy_term_data';

	$result = $drupal_DB->get_row("SELECT name FROM $db_table WHERE tid LIKE '$tid'");

	return ! empty( $result->$db_name ) ? $result->$db_name : '';
}


/**
 * Get Drupal Node Alias
 */
function get_db_node_url_alias( $node_ID = 0, $source = 'node' ) {
	global $drupal_DB;

	$url_alias = '';

	$results = $drupal_DB->get_results("SELECT alias FROM url_alias WHERE source LIKE '$source/$node_ID'");

	if ( ! empty( $results ) ) {
		$results = end($results);

		if ( ! empty( $results->alias ) ) {
			$url_alias = $results->alias;
		}
	}

	return $url_alias;
}


/**
 * Get Drupal User Alias
 */
function get_db_user_url_alias( $user_ID = 0 ) {
	global $drupal_DB;

	$result = $drupal_DB->get_row("SELECT alias FROM url_alias WHERE source LIKE 'user/$user_ID'");

	return ! empty( $result->alias ) ? $result->alias : '';
}


/**
 * Get Drupal Domain Access
 */
function get_db_domain_access( $node_ID = 0 ) {
	global $drupal_DB;

	// old_ID => new_ID, // url | drupal sitename | pdf | comment
	$domains_IDs = [
		1  => 1, // weadapt.org
		31 => 2, // adaptationataltitude.org | Adaptation At Altitude | Adaptation at Altitude (Zoi Environment Network for SDC)
		11 => 3, // can-adapt.ca | Inspiring Climate Action | Adaptation Learning Network (Royal Roads University) | redirect from https://adaptationlearningnetwork.com/
		21 => 4, // adaptationwithoutborders.org | Adaptation Without Borders | Adaptation Without Borders (ODI, SEI, IDDRI)
		16 => 5, // wetransform.dev | weTRANSFORM | weTRANSFORM (SEI, IRDI, ICoE)
		26 => 6, // cckh.weadapt.org | ISDB | Climate Change Knowledge Hub (Islamic Development Bank)
		6  => 7, // energyadaptation.ouranos.ca | Ouranos | Energy Adaptation Map (Ouranos)
		36 => 8, // communities.adaptationportal.gca.org | Water Adaptation Community | Water Adaptation Community (Global Centre on Adaptation)
		41 => 10, // MAIA
	];

	$result = $drupal_DB->get_results("SELECT gid FROM domain_access WHERE nid LIKE '$node_ID'");
	$output = array_column( array_map( function( $item ) use ( $domains_IDs ) {
		$gid = intval( $item->gid );

		return ! empty( $domains_IDs[$gid] ) ? [$domains_IDs[$gid]] : [];
	}, $result), 0);

	return $output;
}


/**
 * Get Logs File Name
 */
function get_logs_file_name( $i, $name = 'import' ) {
	$i = strval($i);

	switch( iconv_strlen ( $i ) ) {
		case 3: $add_slug = $i[0]; break;
		case 4: $add_slug = $i[0] . $i[1]; break;
		default: $add_slug = '0';
	}

	return $name . '-' . $add_slug . '.log';
}


/**
 * Upload Attachment
 */
function upload_attachment_from_url( $image_url = '', $post_ID = 0, $alt = '', $caption = '', $node_ID = 0 ) {
	global $debug;
	global $media_debug;

	$attach_ID    = 0;
	$attach_name  = wp_basename( strtok( $image_url, '?' ) );

	// From DB ID
	if ( ! empty( $node_ID ) ) {
		$attach_query_by_meta = new WP_Query( [
			'post_type'              => 'attachment',
			'post_status'            => 'all',
			'posts_per_page'         => 1,
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'orderby'                => 'post_date ID',
			'order'                  => 'ASC',
			'meta_query'             => [
				[
					'key'     => 'old_id',
					'value'   => $node_ID,
					'compare' => '=',
				],
			],
		] );

		if ( ! empty( $attach_query_by_meta->post->ID ) ) {
			$attach_ID = $attach_query_by_meta->post->ID;
		}
	}

	// From Content
	else {
		$attach_query_by_title = new WP_Query( [
			'post_type'              => 'attachment',
			'title'                  => $attach_name,
			'post_status'            => 'all',
			'posts_per_page'         => 1,
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'orderby'                => 'post_date ID',
			'order'                  => 'ASC',
			'meta_query'             => [
				[
					'key'     => 'old_id',
					'compare' => 'NOT EXISTS',
				],
			],
		] );

		if ( ! empty( $attach_query_by_title->post->ID ) ) {
			$attach_ID = $attach_query_by_title->post->ID;
		}
	}

	if ( empty( $attach_ID ) ) {
		if ( $debug || $media_debug ) return $attach_ID;

		$upload_dir = wp_upload_dir();
		$filename   = wp_unique_filename( $upload_dir['path'], $attach_name );

		if ( wp_mkdir_p( $upload_dir['path'] ) ) {
			$upload_file = $upload_dir['path'] . '/' . $filename;
		} else {
			$upload_file = $upload_dir['basedir'] . '/' . $filename;
		}

		$image_data = file_get_contents( strtok( $image_url, '?' ), false, stream_context_create( [ 'http' => [ 'timeout' => 1, 'ignore_errors' => true ] ] ) );

		if ( strpos( $image_data, '404 Not Found' ) !== false ) {
			return $attach_ID;
		}

		if ( $image_data !== false ) {
			file_put_contents( $upload_file, $image_data );

			$wp_filetype = wp_check_filetype( $upload_file, null );
			$attachment  = array(
				'guid'           => $upload_file,
				'post_mime_type' => $wp_filetype['type'],
				'post_title'     => $attach_name,
				'post_excerpt'   => $caption,
				'post_status'    => 'inherit'
			);

			$attach_ID = wp_insert_attachment( $attachment, $upload_file, $post_ID );

			require_once( ABSPATH . 'wp-admin/includes/image.php' );

			$attach_data = wp_generate_attachment_metadata( $attach_ID, $upload_file );

			wp_update_attachment_metadata( $attach_ID, $attach_data );

			// Alt
			if ( ! empty( $alt ) && wp_attachment_is_image( $attach_ID ) ) {
				update_post_meta( $attach_ID, '_wp_attachment_image_alt', $alt );
			}

			// Old ID
			if ( ! empty( $node_ID ) ) {
				update_post_meta( $attach_ID, 'old_id', $node_ID );
			}
		}
	}

	return $attach_ID;
}


/**
 * Fix WordPress Excerpt
 */
function fix_wp_excerpt( $content = '' ) {
	$content = preg_replace( "/<p[^>]*>(?:\s|&nbsp;)*<\/p>/", '', $content );
	$content = str_replace( '</p><p>', PHP_EOL . PHP_EOL, $content );
	$content = str_replace( ['<p>', '</p>'], '', $content );
	$content = str_replace( '&nbsp;</', '</', $content );
	$content = str_replace( '&nbsp;', ' ', $content );
	$content = wp_kses( $content, [
		'a' => [
			'href'  => true,
			'title' => true,
		],
		'br'     => [],
		'em'     => [],
		'strong' => [],
	] );
	$content = trim( $content );

	return $content;
}


/**
 * Fix WordPress Excerpt Textarea (ACF)
 */
function fix_wp_textarea( $content = '' ) {
	$content = preg_replace( "/<p[^>]*>(?:\s|&nbsp;)*<\/p>/", '', $content );
	$content = str_replace( '</p><p>', PHP_EOL . PHP_EOL, $content );
	$content = str_replace( ['<p>', '</p>'], '', $content );
	$content = str_replace( '&nbsp;</', '</', $content );
	$content = str_replace( '&nbsp;', ' ', $content );
	$content = wp_kses( $content, [
		'a' => [
			'href'  => true,
			'title' => true,
		],
		'br'         => [],
		'em'         => [],
		'strong'     => [],
		'h1'         => [],
		'h2'         => [],
		'h3'         => [],
		'h4'         => [],
		'h5'         => [],
		'h6'         => [],
		'blockquote' => [],
		'ul'         => [],
		'ol'         => [],
		'li'         => [],
		'p'          => [],
	] );
	$content = trim( $content );

	return $content;
}


/**
 * Is URL File
 */
function is_weadapt_file($url = '') {
	$is_file = false;
	if ( strpos( $url, 'weadapt.org' ) !== false ) {
		if ( substr( $url, 0, 7 ) === '/sites/' ) {
			$url = str_replace('/sites/', 'https://www.weadapt.org/sites/', $url);
		}

		$headers = get_headers($url);

		if ( ! empty( $headers )) {
			foreach ($headers as $header) {
				if (
					(strpos($header, 'Content-Type: application/pdf') !== false) ||
					(strpos($header, 'Content-Type: image/jpeg') !== false) ||
					(strpos($header, 'Content-Type: image/jpg') !== false) ||
					(strpos($header, 'Content-Type: image/png') !== false) ||
					(strpos($header, 'Content-Type: image/gif') !== false) ||
					(strpos($header, 'Content-Type: image/bmp') !== false) ||
					(strpos($header, 'Content-Type: image/tiff') !== false) ||
					(strpos($header, 'Content-Type: image/webp') !== false) ||
					(strpos($header, 'Content-Type: application/msword') !== false) ||
					(strpos($header, 'Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document') !== false) ||
					(strpos($header, 'Content-Type: application/vnd.ms-excel') !== false) ||
					(strpos($header, 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') !== false) ||
					(strpos($header, 'Content-Type: application/vnd.ms-powerpoint') !== false) ||
					(strpos($header, 'Content-Type: application/vnd.openxmlformats-officedocument.presentationml.presentation') !== false)
				) {
					$is_file = true;
					break;
				}
			}
		}

		return $is_file;
	}
}


/**
 * Fix WordPress Content Media Only
 */
function fix_wp_content_media_only( $content = '', $post_ID = 0, $node_ID = 0 ) {
	global $media_logs;
	global $error_logs;

	$html = str_get_dom( $content );

	// div
	for ( $i = 0; $i < 5; $i++ ) {
		foreach ( $html('div') as $element ) {
			$element->setOuterText( $element->getInnerText() );
		}
	}

	// img
	foreach ( $html('img') as $element ) {
		$temp_media_logs = PHP_EOL;

		$temp_img_src = $element->src;

		if ( substr( $temp_img_src, 0, 7 ) === '/sites/' ) {
			$temp_img_src = str_replace('/sites/', 'https://www.weadapt.org/sites/', $element->src);
		}

		s($temp_img_src);

		$parts   = parse_url( $temp_img_src );
		$img_src = $parts['scheme'] . '://' . $parts['host'] . $parts['path'];

		if ( is_weadapt_file( $img_src ) ) {
			$image_alt = ! empty( $element->alt ) ? $element->alt : '';
			$image_ID  = upload_attachment_from_url( $img_src, $post_ID, $image_alt );

			// Logs
			$temp_media_logs .= 'post_ID: ' . $post_ID . PHP_EOL;
			$temp_media_logs .= 'node_ID: ' . $node_ID . PHP_EOL;
			$temp_media_logs .= 'old src: ' . $img_src . PHP_EOL;
			$temp_media_logs .= 'new src: ' . ( ! empty( $image_ID ) ? wp_get_attachment_image_url( $image_ID, 'full' ) : 'Error !!!' ) . PHP_EOL;

			$media_logs[] = $temp_media_logs;

			if ( empty( $image_ID ) ) {
				$error_logs .= PHP_EOL . 'Content Textarea Image:' . PHP_EOL;
				$error_logs .= $temp_media_logs;

				$element->setOuterText( '' );
			}
			else {
				$element->setOuterText(
					PHP_EOL . '<img src="' . wp_get_attachment_image_url( $image_ID, 'full' ) . '" alt="" class="wp-image-' . $image_ID . '"/>'
				);
			}
		}
	}

	$content = $html->toString();

	// fix empties
	$content = str_replace( PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL, PHP_EOL . PHP_EOL, $content );
	$content = str_replace( PHP_EOL . PHP_EOL . PHP_EOL, PHP_EOL . PHP_EOL, $content );
	$content = str_replace( PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL, PHP_EOL . PHP_EOL, $content );
	$content = str_replace( PHP_EOL . PHP_EOL . PHP_EOL, PHP_EOL . PHP_EOL, $content );


	$content = str_replace( '<~root~>', '', $content );
	$content = str_replace( '</~root~>', '', $content );

	return $content;
}


/**
 * Fix WordPress Content
 */
function fix_wp_content( $content = '', $post_ID = 0, $node_ID = 0 ) {
	global $media_logs;
	global $error_logs;

	$content = preg_replace('/<h1>.{0,4}<\/h1>/','', $content);
	$content = str_replace( '<div>&nbsp;</div>', '', $content );
	$content = str_replace( '<p>&nbsp;</p>', '', $content );
	$content = str_replace( '<p></p>', '', $content );
	$content = str_replace( '&nbsp;</', '</', $content );
	$content = str_replace( '&nbsp;', ' ', $content );
	$content = str_replace( ['<br>', '<br/>', '<br />'], '', $content );

	$html = str_get_dom( $content );

	// div
	for ( $i = 0; $i < 5; $i++ ) {
		foreach ( $html('div') as $element ) {
			$element->setOuterText( $element->getInnerText() );
		}
	}

	// span
	for ( $i = 0; $i < 5; $i++ ) {
		foreach ( $html('span') as $element ) {
			$element->setOuterText( $element->getInnerText() );
		}
	}

	// p
	foreach ( $html('p') as $element ) {
		$element->setOuterText( '<p>' . $element->getInnerText() . '</p>' );
	}

	// fix p a img
	foreach ( $html('p') as $element ) {
		$has_a_img = false;

		foreach ( $element('a') as $child_a_element ) {
			foreach ( $child_a_element('img') as $child_img_element ) {
				$has_a_img = true;
			}
		}


		if ( $has_a_img ) {
			$element->setOuterText( $element->getInnerText() );
		}
	}
	$content = $html->toString();
	$content = str_replace( '<~root~>', '', $content );
	$content = str_replace( '</~root~>', '', $content );

	$html = str_get_dom( $content );

	// fix p img
	foreach ( $html('p') as $element ) {
		foreach ( $element('img') as $child_element ) {
			$child_element->setOuterText( $child_element->htmlUTF8() . 'TEMP_REPLACE_PARAGRAPH' );
		}
	}
	$content = $html->toString();
	$content = str_replace( '<~root~>', '', $content );
	$content = str_replace( '</~root~>', '', $content );

	$html = str_get_dom( $content );

	// fix figcaption
	foreach ( $html('figure') as $element ) {
		$has_img    = false;
		$figcaption = '';

		foreach ( $element('figcaption') as $figcaption_element ) {
			$figcaption = $figcaption_element->getInnerText();

			$figcaption_element->setOuterText( '' );
		}

		foreach ( $element('img') as $child_element ) {
			if ( ! empty( $figcaption ) ) {
				$child_element->setAttribute( 'TEMP_FIGCAPTION', $figcaption );
			}

			$element->setOuterText( $element->getInnerText() );
		}
	}

	// fix a | a img
	foreach ( $html('a') as $element ) {
		if ( ! empty( trim( $element->href ) ) ) {
			if ( is_weadapt_file( $element->href ) ) {
				$link_href = $element->href;

				if ( substr( $link_href, 0, 7 ) === '/sites/' ) {
					$link_href = str_replace('/sites/', 'https://www.weadapt.org/sites/', $element->src);
				}

				$file_ID  = upload_attachment_from_url( $link_href, $post_ID );

				// Logs
				$temp_media_logs  = 'post_ID: ' . $post_ID . PHP_EOL;
				$temp_media_logs .= 'node_ID: ' . $node_ID . PHP_EOL;
				$temp_media_logs .= 'old src: ' . $element->href . PHP_EOL;
				$temp_media_logs .= 'new src: ' . ( ! empty( $file_ID ) ? wp_get_attachment_url( $file_ID ) : 'Error !!!' ) . PHP_EOL;

				$media_logs[] = $temp_media_logs;

				if ( empty( $file_ID ) ) {
					$error_logs .= PHP_EOL . 'Content File:' . PHP_EOL;
					$error_logs .= $temp_media_logs;
				}
				else {
					$element->setAttribute( 'href', wp_get_attachment_url( $file_ID ) );
				}
			}
			else {
				$link_href = $element->href;
			}
		}

		foreach ( $element('img') as $child_element ) {
			if ( ! empty( $file_ID ) ) {
				$child_element->setAttribute( 'TEMP_PARENT_URL', wp_get_attachment_url( $file_ID ) );
				$element->setOuterText( $element->getInnerText() );
			}
			else if ( ! empty( $link_href ) ) {
				$child_element->setAttribute( 'TEMP_PARENT_URL', $link_href );
				$element->setOuterText( $element->getInnerText() );
			}
		}
	}
	$content = $html->toString();
	$content = str_replace( '<~root~>', '', $content );
	$content = str_replace( '</~root~>', '', $content );

	$html = str_get_dom( $content );

	// img
	foreach ( $html('img') as $element ) {
		$temp_media_logs = PHP_EOL;

		$temp_img_src = $element->src;

		if ( substr( $temp_img_src, 0, 7 ) === '/sites/' ) {
			$temp_img_src = str_replace('/sites/', 'https://www.weadapt.org/sites/', $element->src);
		}

		$parts   = parse_url( $temp_img_src );
		$img_src = $parts['scheme'] . '://' . $parts['host'] . $parts['path'];

		if ( is_weadapt_file( $img_src ) ) {
			$image_alt = ! empty( $element->alt ) ? $element->alt : '';
			$image_ID  = upload_attachment_from_url( $img_src, $post_ID, $image_alt );

			// Logs
			$temp_media_logs .= 'post_ID: ' . $post_ID . PHP_EOL;
			$temp_media_logs .= 'node_ID: ' . $node_ID . PHP_EOL;
			$temp_media_logs .= 'old src: ' . $img_src . PHP_EOL;
			$temp_media_logs .= 'new src: ' . ( ! empty( $image_ID ) ? wp_get_attachment_image_url( $image_ID, 'full' ) : 'Error !!!' ) . PHP_EOL;

			$media_logs[] = $temp_media_logs;

			if ( empty( $image_ID ) ) {
				$error_logs .= PHP_EOL . 'Content Image:' . PHP_EOL;
				$error_logs .= $temp_media_logs;

				$element->setOuterText( '' );
			}
			else {
				$figcaption_html = '';

				if ( ! empty( $element->TEMP_FIGCAPTION ) ) {
					$figcaption_html = '<figcaption class="wp-element-caption">' . $element->TEMP_FIGCAPTION . '</figcaption>'. PHP_EOL;
				}

				if ( ! empty( $element->TEMP_PARENT_URL ) ) {
					$element->setOuterText(
						'<!-- wp:image {"id":' . $image_ID . ',"sizeSlug":"content-thumbnail","linkDestination":"custom"} -->' . PHP_EOL .
						'<figure class="wp-block-image size-content-thumbnail">' . PHP_EOL .
						'<a href="' . esc_url( $element->TEMP_PARENT_URL ) . '">' . PHP_EOL .
						'<img src="' . wp_get_attachment_image_url( $image_ID, 'content-thumbnail' ) . '" alt="" class="wp-image-' . $image_ID . '"/>' . PHP_EOL .
						'</a>' . PHP_EOL .
						$figcaption_html .
						'</figure>' . PHP_EOL .
						'<!-- /wp:image -->' . PHP_EOL . PHP_EOL
					);
				}
				else {
					$element->setOuterText(
						'<!-- wp:image {"id":' . $image_ID . ',"sizeSlug":"content-thumbnail","linkDestination":"media"} -->' . PHP_EOL .
						'<figure class="wp-block-image size-content-thumbnail">' . PHP_EOL .
						'<a href="' . wp_get_attachment_image_url( $image_ID, 'full' ) . '">' . PHP_EOL .
						'<img src="' . wp_get_attachment_image_url( $image_ID, 'content-thumbnail' ) . '" alt="" class="wp-image-' . $image_ID . '"/>' . PHP_EOL .
						'</a>' . PHP_EOL .
						$figcaption_html .
						'</figure>' . PHP_EOL .
						'<!-- /wp:image -->' . PHP_EOL . PHP_EOL
					);
				}
			}
		}
	}

	// h1-h6
	foreach( range(1, 6) as $i ) {
		foreach ( $html("h$i") as $element ) {
			$element->setOuterText( '<!-- wp:heading {"level":' . $i . '} -->' . PHP_EOL . "<h$i>" . $element->getInnerText() . "</h$i>" . PHP_EOL . '<!-- /wp:heading -->' . PHP_EOL . PHP_EOL );
		}
	}

	// p
	foreach ( $html('p') as $element ) {
		if ( ! empty( $element->getInnerText() ) ) {
			$element->setOuterText( '<!-- wp:paragraph -->' . PHP_EOL . $element->htmlUTF8() . PHP_EOL . '<!-- /wp:paragraph -->' . PHP_EOL . PHP_EOL );
		}
		else {
			$element->setOuterText( '' );
		}
	}

	// ul
	foreach ( $html('ul') as $element ) {
		$element->setOuterText( '<!-- wp:list -->' . PHP_EOL . $element->htmlUTF8() . PHP_EOL . '<!-- /wp:list -->' . PHP_EOL . PHP_EOL );
	}

	// ol
	foreach ( $html('ol') as $element ) {
		$element->setOuterText( '<!-- wp:list {"ordered":true} -->' . PHP_EOL . $element->htmlUTF8() . PHP_EOL . '<!-- /wp:list -->' . PHP_EOL . PHP_EOL );
	}

	// li
	foreach ( $html('li') as $element ) {
		$element->setOuterText( '<!-- wp:list-item -->' . PHP_EOL . $element->htmlUTF8() . PHP_EOL . '<!-- /wp:list-item -->' );
	}

	// iframe
	foreach ( $html('iframe') as $element ) {
		if ( strpos( $element->src, 'youtube' ) !== false ) {
			$temp_iframe_src = $element->src;

			if ( substr( $temp_iframe_src, 0, 2 ) === '//' ) {
				$temp_iframe_src = 'https:' . $temp_iframe_src;
			}

			$parts = parse_url( $temp_iframe_src );

			$iframe_src	= $parts['scheme'] . '://' . $parts['host'] . $parts['path'];

			$element->setOuterText(
				'<!-- wp:embed {"url":"' . $iframe_src . '","type":"rich","providerNameSlug":"embed-handler","responsive":true,"className":"wp-embed-aspect-16-9 wp-has-aspect-ratio"} -->' . PHP_EOL .
				'<figure class="wp-block-embed is-type-rich is-provider-embed-handler wp-block-embed-embed-handler wp-embed-aspect-16-9 wp-has-aspect-ratio"><div class="wp-block-embed__wrapper">' . PHP_EOL .
				$iframe_src . PHP_EOL .
				'</div></figure>' . PHP_EOL .
				'<!-- /wp:embed -->' . PHP_EOL . PHP_EOL
			);
		}
	}

	$content = $html->toString();

	// fix p > img
	$content = str_replace( '<!-- wp:paragraph -->' .  PHP_EOL . '<p><!-- wp:image', '<!-- wp:image', $content );
	$content = str_replace( '<!-- /wp:image -->' .  PHP_EOL . PHP_EOL . '</p>' .  PHP_EOL . '<!-- /wp:paragraph -->', '<!-- /wp:image -->', $content );

	// fix p > embed
	$content = str_replace( '<!-- wp:paragraph -->' .  PHP_EOL . '<p><!-- wp:embed', '<!-- wp:embed', $content );
	$content = str_replace( '<!-- /wp:embed -->' .  PHP_EOL . PHP_EOL . '</p>' .  PHP_EOL . '<!-- /wp:paragraph -->', '<!-- /wp:embed -->', $content );

	// fix tab
	$content = str_replace( '	<!-- wp:', '<!-- wp:', $content );

	// fix p img
	$content = str_replace( 'TEMP_REPLACE_PARAGRAPH</p>' . PHP_EOL . '<!-- /wp:paragraph -->', '', $content );
	$content = str_replace( 'TEMP_REPLACE_PARAGRAPH', '<!-- wp:paragraph -->' . PHP_EOL . '<p>', $content );

	// fix empty p
	$content = str_replace( '<!-- wp:paragraph -->' . PHP_EOL . '<p> </p>' . PHP_EOL . '<!-- /wp:paragraph -->', '', $content );

	// fix empties
	$content = str_replace( PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL, PHP_EOL . PHP_EOL, $content );
	$content = str_replace( PHP_EOL . PHP_EOL . PHP_EOL, PHP_EOL . PHP_EOL, $content );
	$content = str_replace( PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL, PHP_EOL . PHP_EOL, $content );
	$content = str_replace( PHP_EOL . PHP_EOL . PHP_EOL, PHP_EOL . PHP_EOL, $content );


	$content = str_replace( '<~root~>', '', $content );
	$content = str_replace( '</~root~>', '', $content );

	return $content;
}


/**
 * Get Post Type IDs by Target IDs
 */
function get_post_ids_by_target_ids( $field = '', $node_ID = 0, $post_type = ['post'] ) {
	global $debug;

	$output_data = [];
	$target_ids  = get_db_target_ids( $field, $node_ID );

	if ( ! empty( $target_ids ) ) {
		foreach ( $target_ids as $target_id ) {
			$posts_query = new WP_Query( [
				'post_type'              => $post_type,
				'post_status'            => 'all',
				'posts_per_page'         => 1,
				'no_found_rows'          => true,
				'ignore_sticky_posts'    => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'orderby'                => 'post_date ID',
				'order'                  => 'ASC',
				'meta_query'             => [
					[
						'key'     => 'old_id',
						'value'   => $target_id,
						'compare' => '=',
					],
				],
			] );

			if ( ! empty( $posts_query->post->ID ) ) {
				$output_data[$posts_query->post->ID] = $posts_query->post->post_title;
			}
		}
	}

	return $output_data;
}


/**
 * Get Post Type ID by Node ID
 */
function get_post_id_by_node_id( $node_ID = 0, $post_type = ['post'] ) {
	$output_data = [];

	$posts_query = new WP_Query( [
		'post_type'              => $post_type,
		'post_status'            => 'all',
		'posts_per_page'         => 1,
		'no_found_rows'          => true,
		'ignore_sticky_posts'    => true,
		'update_post_term_cache' => false,
		'update_post_meta_cache' => false,
		'orderby'                => 'post_date ID',
		'order'                  => 'ASC',
		'meta_query'             => [
			[
				'key'     => 'old_id',
				'value'   => $node_ID,
				'compare' => '=',
			],
		],
	] );

	if ( ! empty( $posts_query->post->ID ) ) {
		$output_data[$posts_query->post->ID] = $posts_query->post->post_title;
	}

	return $output_data;
}


/**
 * Get User IDs by Target IDs
 */
function get_user_ids_by_target_ids( $field = '', $node_ID = 0 ) {
	global $drupal_DB;

	$output_data = [];
	$target_ids  = get_db_target_ids( $field, $node_ID );

	if ( ! empty( $target_ids ) ) {
		foreach ( $target_ids as $target_id ) {
			$user_node = $drupal_DB->get_row("SELECT * FROM users WHERE uid LIKE '$target_id'");
			$temp_data = [
				'old_id' => $target_id,
				'email'  => '',
				'wp_id'  => 0,
			];

			if ( ! empty( $user_node->mail ) ) {
				$temp_data['email'] = $user_node->mail;

				$isset_user = get_user_by( 'email', esc_attr( $user_node->mail ) );

				if ( ! empty( $isset_user->data ) ) {
					$temp_data['wp_id'] = $isset_user->data->ID;
				}
			}

			$output_data[] = $temp_data;
		}
	}

	return $output_data;
}


/**
 * Sort Nodes by Parent
 */
function sort_nodes_data_by_parent( array &$elements, $parent_id = 0 ) {
	$branch = [];
	foreach ( $elements as $element ) {
		if ( $element->parent == $parent_id ) {
			$children = sort_nodes_data_by_parent( $elements, $element->ID );
			if ( $children ) {
				$element->children = $children;
			}
			$branch[] = $element;

			unset($element);
		}
	}
	return $branch;
}


/**
 * Reverse Nodes by Parent
 */
function reverse_nodes_data_by_parent( $elements ) {
	$output = [];

	foreach ($elements as $item) {
		$output[] = [
			'ID'          => $item->ID,
			'vid'         => $item->vid,
			'name'        => $item->name,
			'description' => $item->description,
			'weight'      => $item->weight,
			'parent'      => $item->parent,
		];

		if ( isset( $item->children ) && is_array( $item->children ) && ! empty( $item->children ) ) {
			$output = array_merge( $output, reverse_nodes_data_by_parent( $item->children ) );
		}
	}
	return $output;
}