<?php

add_filter( 'args_for_solutions_query', function( $args, $params, $query_type = 'wp_query') {
	$scales   		= ! empty( $params['solution-scale'] ) ? wp_parse_id_list( $params['solution-scale'] ) : [];
	$ecosystems   	= ! empty( $params['solution-ecosystem-type'] ) ? wp_parse_id_list( $params['solution-ecosystem-type'] ) : [];
	$types   		= ! empty( $params['solution-type'] ) ? wp_parse_id_list( $params['solution-type'] ) : [];
	$sectors   		= ! empty( $params['solution-sector'] ) ? wp_parse_id_list( $params['solution-sector'] ) : [];
	$impacts   		= ! empty( $params['solution-climate-impact'] ) ? wp_parse_id_list( $params['solution-climate-impact'] ) : [];
	$status			= ! empty( $params['status'] ) ? wp_parse_list($params['status']) : [];

	$sort_by      	= ! empty( $params['sort_by'] ) ? esc_attr( $params['sort_by'] ) : '';
	$page         	= ! empty( $params['paged'] ) ? intval( $params['paged'] ) : 1;
	$post_type    	= ! empty( $params['post_type'] ) ? $params['post_type'] : '';
	$search_query 	= ! empty( $params['search'] ) ? esc_attr( $params['search'] ) : '';

	$existed_scales 	= [];
	$existed_ecosystems = [];
	$existed_types 		= [];
	$existed_sectors 	= [];
	$existed_impacts 	= [];
	$existed_status 	= [];

	if ( ! empty( $post_type ) ) {
		$temp_post_types = [];

		if ( ! empty( $post_type) ) {
			$temp_post_types = [ $post_type ];
		}
		if ( ! empty( $temp_post_types ) ) {
			$args['post_type'] = get_allowed_post_types( $temp_post_types );
		}
	}

	if ( ! empty( $search_query ) ) {
		$args['s'] = $search_query;
	}

	if ( ! empty( $sort_by ) ) {
    	switch ( $sort_by ) {
			case 'Oldest':
				$args['orderby'] = 'date';
				$args['order'] = 'ASC';

				break;

			case 'A-Z':
				$args['orderby'] = 'title';
				$args['order'] = 'ASC';

				break;

			case 'Z-A':
				$args['orderby'] = 'title';
				$args['order'] = 'DESC';

				break;

			default:
				$args['orderby'] = 'date';
				$args['order'] = 'DESC';

				$sort_by = 'Newest';
		}
    }

    if ( ! empty( $scales ) ) {
		foreach ( $scales as $term_ID ) {
			$term = term_exists( $term_ID, 'solution-scale' );

			if ( ! empty( $term ) && $term_ID !== 1 ) {
				$existed_scales[] = $term_ID;
			}
		}
	}
	if ( ! empty( $ecosystems ) ) {
		foreach ( $ecosystems as $term_ID ) {
			$term = term_exists( $term_ID, 'solution-ecosystem-type' );

			if ( ! empty( $term ) && $term_ID !== 1 ) {
				$existed_ecosystems[] = $term_ID;
			}
		}
	}
	if ( ! empty( $types ) ) {
		foreach ( $types as $term_ID ) {
			$term = term_exists( $term_ID, 'solution-type' );

			if ( ! empty( $term ) && $term_ID !== 1 ) {
				$existed_types[] = $term_ID;
			}
		}
	}
	if ( ! empty( $sectors ) ) {
		foreach ( $sectors as $term_ID ) {
			$term = term_exists( $term_ID, 'solution-sector' );

			if ( ! empty( $term ) && $term_ID !== 1 ) {
				$existed_sectors[] = $term_ID;
			}
		}
	}
	if ( ! empty( $impacts ) ) {
		foreach ( $impacts as $term_ID ) {
			$term = term_exists( $term_ID, 'solution-climate-impact' );

			if ( ! empty( $term ) && $term_ID !== 1 ) {
				$existed_impacts[] = $term_ID;
			}
		}
	}

	if( ! empty( $status ) ) {
		$existed_status = $status;
	}

    if ( $page > 1 ) {
		$args['paged'] = $page;
	}

	return [
		'args'         	=> $args,
		'post_type'    	=> $post_type,
		'scales'   		=> $existed_scales,
		'ecosystems'   	=> $existed_ecosystems,
		'types'   		=> $existed_types,
		'sectors'   	=> $existed_sectors,
		'impacts'   	=> $existed_impacts,
		'status'		=> $existed_status,
		'sort_by'      	=> $sort_by,
		'page'         	=> $page,
		'search_query' 	=> $search_query
	];

}, 10, 3 );

add_filter( 'args_for_groups_filter', function( $args, $params, $query_type = 'wp_query' ) {
	$categories   = ! empty( $params['categories'] ) ? wp_parse_id_list( $params['categories'] ) : [];
	$sort_by      = ! empty( $params['sort_by'] ) ? esc_attr( $params['sort_by'] ) : '';
	$page         = ! empty( $params['paged'] ) ? intval( $params['paged'] ) : 1;
	$post_types   = ! empty( $params['post_types'] ) ? $params['post_types'] : [];
	$post_type    = ! empty( $params['post_type'] ) ? $params['post_type'] : '';
	$search_query = ! empty( $params['search'] ) ? esc_attr( $params['search'] ) : '';

	$existed_categories = [];

	if ( ! empty( $post_types ) || ! empty( $post_type ) ) {
		$temp_post_types = [];

		if ( is_string( $post_types ) ) {
			$temp_post_types = explode( ',', esc_attr( $post_types ) );
		}
		else if ( is_array( $post_types ) ) {
			$temp_post_types = $post_types;
		}
		if ( ! empty( $post_type) ) {
			$temp_post_types = [ $post_type ];
		}
		if ( ! empty( $temp_post_types ) ) {
			$args['post_type'] = get_allowed_post_types( $temp_post_types );
		}
	}

	if ( ! empty( $search_query ) ) {
		$args['s'] = $search_query;
	}

	if ( ! empty( $categories ) ) {
		if ( $query_type === 'user_query' ) {
			$args['meta_query'] = [];

			foreach ( $categories as $term_ID ) {
				$term = term_exists( $term_ID, 'interest' );

				if ( ! empty( $term ) && $term_ID !== 1 ) {
					$args['meta_query'][] = [
						'key'     => 'interest',
						'value'    => sprintf( ':"%d";', $term_ID ),
						'compare'  => 'LIKE'
					];

					$existed_categories[] = $term_ID;
				}
			}
		}
		else {
			foreach ( $categories as $term_ID ) {
				$term = term_exists( $term_ID, 'category' );

				if ( ! empty( $term ) && $term_ID !== 1 ) {
					$existed_categories[] = $term_ID;
				}
			}

			$args['category__in'] = $existed_categories;
		}
	}

	if ( ! empty( $sort_by ) ) {
		if ( $query_type === 'user_query' ) {
			switch ( $sort_by ) {
				case 'Oldest':
					$args['orderby'] = 'user_registered';
					$args['order'] = 'ASC';

					break;

				case 'A-Z':
					$args['orderby'] = 'user_name';
					$args['order'] = 'ASC';

					break;

				case 'Z-A':
					$args['orderby'] = 'user_name';
					$args['order'] = 'DESC';

					break;

				default:
					$args['orderby'] = 'user_registered';
					$args['order'] = 'DESC';

					$sort_by = 'Newest';
			}
		}
		else {
			switch ( $sort_by ) {
				case 'Oldest':
					$args['order']   = 'ASC';
					$args['orderby'] = ( isset( $args['orderby'] ) && $args['orderby'] === 'meta_value' ) ? 'meta_value' : 'date';

					break;

				case 'A-Z':
					$args['orderby'] = 'title';
					$args['order'] = 'ASC';

					break;

				case 'Z-A':
					$args['orderby'] = 'title';
					$args['order'] = 'DESC';

					break;

				default:
					$args['order']   = 'DESC';
					$args['orderby'] = ( isset( $args['orderby'] ) && $args['orderby'] === 'meta_value' ) ? 'meta_value' : 'date';

					$sort_by = 'Newest';
			}
		}
	}

	if ( $page > 1 ) {
		$args['paged'] = $page;
	}

	return [
		'args'         => $args,
		'post_types'   => $post_types,
		'post_type'    => $post_type,
		'categories'   => $existed_categories,
		'sort_by'      => $sort_by,
		'page'         => $page,
		'search_query' => $search_query
	];
}, 10, 3 );

add_filter( 'args_for_query', function( $args, $params, $query_type = 'wp_query' ) {
	$categories   = ! empty( $params['categories'] ) ? wp_parse_id_list( $params['categories'] ) : [];
	$sort_by      = ! empty( $params['sort_by'] ) ? esc_attr( $params['sort_by'] ) : '';
	$page         = ! empty( $params['paged'] ) ? intval( $params['paged'] ) : 1;
	$post_types   = ! empty( $params['post_types'] ) ? $params['post_types'] : [];
	$post_type    = ! empty( $params['post_type'] ) ? $params['post_type'] : '';
	$search_query = ! empty( $params['s'] ) ? esc_attr( $params['s'] ) : '';
	$is_draft     = ! empty( $params['p'] ) && ! empty( $params['post_type'] ) ? true : false;

	$existed_categories = [];

	if ( ( ! empty( $post_types ) || ! empty( $post_type) ) && !$is_draft ) {
		$temp_post_types = [];

		if ( is_string( $post_types ) ) {
			$temp_post_types = explode( ',', esc_attr( $post_types ) );
		}
		else if ( is_array( $post_types ) ) {
			$temp_post_types = $post_types;
		}
		if ( ! empty( $post_type) ) {
			$temp_post_types = [ $post_type ];
		}
		if ( ! empty( $temp_post_types ) ) {
			$args['post_type'] = get_allowed_post_types( $temp_post_types );
		}
	}

	if ( ! empty( $search_query ) ) {
		$args['s'] = $search_query;
	}

	if ( ! empty( $categories ) ) {
		if ( $query_type === 'user_query' ) {
			$args['meta_query'] = [];

			foreach ( $categories as $term_ID ) {
				$term = term_exists( $term_ID, 'interest' );

				if ( ! empty( $term ) && $term_ID !== 1 ) {
					$args['meta_query'][] = [
						'key'     => 'interest',
						'value'    => sprintf( ':"%d";', $term_ID ),
						'compare'  => 'LIKE'
					];

					$existed_categories[] = $term_ID;
				}
			}
		}
		else {
			foreach ( $categories as $term_ID ) {
				$term = term_exists( $term_ID, 'category' );

				if ( ! empty( $term ) && $term_ID !== 1 ) {
					$existed_categories[] = $term_ID;
				}
			}

			$args['category__in'] = $existed_categories;
		}
	}

	if ( ! empty( $sort_by ) ) {
		if ( $query_type === 'user_query' ) {
			switch ( $sort_by ) {
				case 'Oldest':
					$args['orderby'] = 'user_registered';
					$args['order'] = 'ASC';

					break;

				case 'A-Z':
					$args['orderby'] = 'user_name';
					$args['order'] = 'ASC';

					break;

				case 'Z-A':
					$args['orderby'] = 'user_name';
					$args['order'] = 'DESC';

					break;

				default:
					$args['orderby'] = 'user_registered';
					$args['order'] = 'DESC';

					$sort_by = 'Newest';
			}
		}
		else {
			switch ( $sort_by ) {
				case 'Oldest':
					$args['order']   = 'ASC';
					$args['orderby'] = ( isset( $args['orderby'] ) && $args['orderby'] === 'meta_value' ) ? 'meta_value' : 'date';

					break;

				case 'A-Z':
					$args['orderby'] = 'title';
					$args['order'] = 'ASC';

					break;

				case 'Z-A':
					$args['orderby'] = 'title';
					$args['order'] = 'DESC';

					break;

				default:
					$args['order']   = 'DESC';
					$args['orderby'] = ( isset( $args['orderby'] ) && $args['orderby'] === 'meta_value' ) ? 'meta_value' : 'date';

					$sort_by = 'Newest';
			}
		}
	}

	if ( $page > 1 ) {
		$args['paged'] = $page;
	}

	return [
		'args'         => $args,
		'post_types'   => $post_types,
		'post_type'    => $post_type,
		'categories'   => $existed_categories,
		'sort_by'      => $sort_by,
		'page'         => $page,
		'search_query' => $search_query
	];
}, 10, 3 );


/**
 * Filter "wp_count_posts" by "publish_to" field
 */
add_filter( 'wp_count_posts', function( $counts, $type, $perm ) {
	if ( is_super_admin() ) {
		return $counts;
	}

	$cache_key    = _count_posts_cache_key( 'theme_' . $type, $perm );
	$cache_counts = wp_cache_get( $cache_key, 'counts' );

	if ( false !== $cache_counts ) {
		return $cache_counts;
	}

	global $wpdb;

	$query = "SELECT post_status, COUNT($wpdb->posts.ID) as post_count
		FROM $wpdb->posts
		LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id
		WHERE $wpdb->posts.post_type = %s
		AND ($wpdb->postmeta.meta_key = 'publish_to' AND $wpdb->postmeta.meta_value LIKE '%:\"%d\"%')
		GROUP BY post_status";

	$results = $wpdb->get_results( $wpdb->prepare( $query, $type, get_current_blog_id() ) );
	$counts  = (array) $counts;

	if ( ! empty( $results ) ) {
		foreach ($results as $result) {
			$counts[$result->post_status] = $result->post_count;
		}
	}
	else {
		foreach ($counts as $count_key => $count_value ) {
			$counts[$count_key] = 0;
		}
	}

	$counts = (object) $counts;

	wp_cache_set( $cache_key, $counts, 'counts' );

	return $counts;
}, 10, 3 );


/**
 * Filter "Mine wp_count_posts" by "publish_to" field
 */
function theme_edit__list_table_views( $views ) {
	if ( isset( $views['mine'] ) ) {
		global $wpdb;

		$query = "SELECT COUNT($wpdb->posts.ID) as post_count
			FROM $wpdb->posts
			LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id
			WHERE $wpdb->posts.post_type = %s
			AND $wpdb->posts.post_author = %s
			AND ($wpdb->postmeta.meta_key = 'publish_to' AND $wpdb->postmeta.meta_value LIKE '%:\"%d\"%')";

		$mine_posts = $wpdb->get_var( $wpdb->prepare( $query, get_current_screen()->post_type, get_current_user_id(), get_current_blog_id() ) );

		if ( ! empty( $mine_posts ) ) {
			$views['mine'] = preg_replace('/<span class="count">\(\d+\)<\/span>/', '<span class="count">(' . $mine_posts . ')</span>', $views['mine'] );
		}
		else {
			unset( $views['mine'] );
		}
	}

	return $views;
}

$theme_network_post_types = get_theme_network_post_types();

if ( ! empty( $theme_network_post_types ) ) {
	foreach ( $theme_network_post_types as $post_type ) {
		add_filter( "views_edit-{$post_type}", 'theme_edit__list_table_views' );
	}
}
