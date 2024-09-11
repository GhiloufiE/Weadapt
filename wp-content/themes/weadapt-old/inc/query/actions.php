<?php

/**
 * Selected Categories
 */

add_action( 'selected_taxonomies_filter',  function( $query_url, $args_for_query ) {
	$scales  	  = ! empty( $args_for_query['scales'] ) ? $args_for_query['scales'] : [];
	$ecosystems	  = ! empty( $args_for_query['ecosystems'] ) ? $args_for_query['ecosystems'] : [];
	$types	  	  = ! empty( $args_for_query['types'] ) ? $args_for_query['types'] : [];
	$sectors  	  = ! empty( $args_for_query['sectors'] ) ? $args_for_query['sectors'] : [];
	$impacts      = ! empty( $args_for_query['impacts'] ) ? $args_for_query['impacts'] : [];
	$sort_by      = ! empty( $args_for_query['sort_by'] ) ? $args_for_query['sort_by'] : '';
	$post_type    = ! empty( $args_for_query['post_type'] ) ? $args_for_query['post_type'] : [];
	$status 	  = ! empty( $args_for_query['status'] ) ? $args_for_query['status'] : [];
	$search_query = ! empty( $args_for_query['search_query'] ) ? $args_for_query['search_query'] : '';

	$post_type    = ! empty( $args_for_query['post_type'] ) ? $args_for_query['post_type'] : [];
	$temp_query_args = [];

	if ( ! empty( $scales ) ) {
		$temp_query_args['solution-scale'] = implode( ',', $scales );
	}
	if ( ! empty( $ecosystems ) ) {
		$temp_query_args['solution-ecosystem-type'] = implode( ',', $ecosystems );
	}
	if ( ! empty( $types ) ) {
		$temp_query_args['solution-type'] = implode( ',', $types );
	}
	if ( ! empty( $sectors ) ) {
		$temp_query_args['solution-sector'] = implode( ',', $sectors );
	}
	if ( ! empty( $impacts ) ) {
		$temp_query_args['solution-climate-impact'] = implode( ',', $impacts );
	}
	if ( ! empty( $search_query ) ) {
		$temp_query_args['search'] = esc_attr( $search_query );
	}
	if ( ! empty( $sort_by ) ) {
		$temp_query_args['sort_by'] = $sort_by;
	}
	if ( ! empty( $post_type ) ) {
		$temp_query_args['post_type'] = $post_type;
	}
	if ( ! empty( $status ) ) {
		$temp_query_args['status'] = implode( ',', $status );
	}

	if ( ! empty( $scales ) ) {
		foreach ( $scales as $term_id ) :
			$temp_query_args_2 = $temp_query_args;
			$term = get_term( $term_id );
			$url = $query_url;
			if ( ! empty( $term ) ) :
				$temp_scales = array_diff( $scales, [ $term_id ] );
				if ( ! empty( $temp_scales ) ) {
					$temp_query_args_2['solution-scale'] = implode( ',', $temp_scales );
				} else {
					unset( $temp_query_args_2['solution-scale']);
				}
				if ( ! empty( $temp_query_args ) ) {
					$url = add_query_arg( $temp_query_args_2, $query_url );
				}
			?>
			<li class="cpt-filters__term">
				<a href="<?php echo esc_url( $url ); ?>" class="cpt-filters__active">
					<span class="cpt-filters__active-text"><?php echo $term->name; ?></span>
					<span class="cpt-filters__active-icon"><?php echo get_img( 'icon-x' ); ?></span>
				</a>
			</li>
		<?php
			endif;
			endforeach;
	}
	if ( ! empty( $ecosystems ) ) {
		foreach ( $ecosystems as $term_id ) :
			$temp_query_args_2 = $temp_query_args;
			$term = get_term( $term_id );
			$url = $query_url;
			if ( ! empty( $term ) ) :
				$temp_ecosystems = array_diff( $ecosystems, [ $term_id ] );

				if ( ! empty( $temp_ecosystems ) ) {
					$temp_query_args_2['solution-ecosystem-type'] = implode( ',', $temp_ecosystems );
				} else {
					unset( $temp_query_args_2['solution-ecosystem-type']);
				}
				if ( ! empty( $temp_query_args ) ) {
					$url = add_query_arg( $temp_query_args_2, $query_url );
				}

		?>
			<li class="cpt-filters__term">
				<a href="<?php echo esc_url( $url ); ?>" class="cpt-filters__active">
					<span class="cpt-filters__active-text"><?php echo $term->name; ?></span>
					<span class="cpt-filters__active-icon"><?php echo get_img( 'icon-x' ); ?></span>
				</a>
			</li>
		<?php
			endif;
			endforeach;
	}
	if ( ! empty( $types ) ) {
		foreach ( $types as $term_id ) :
			$temp_query_args_2 = $temp_query_args;
			$term = get_term( $term_id );
			$url = $query_url;
			if ( ! empty( $term ) ) :
				$temp_types = array_diff( $types, [ $term_id ] );

				if ( ! empty( $temp_types ) ) {
					$temp_query_args_2['solution-type'] = implode( ',', $temp_types );
				} else {
					unset( $temp_query_args_2['solution-type']);
				}
				if ( ! empty( $temp_query_args ) ) {
					$url = add_query_arg( $temp_query_args_2, $query_url );
				}

		?>
			<li class="cpt-filters__term">
				<a href="<?php echo esc_url( $url ); ?>" class="cpt-filters__active">
					<span class="cpt-filters__active-text"><?php echo $term->name; ?></span>
					<span class="cpt-filters__active-icon"><?php echo get_img( 'icon-x' ); ?></span>
				</a>
			</li>
		<?php
			endif;
			endforeach;
	}
	if ( ! empty( $sectors ) ) {
		foreach ( $sectors as $term_id ) :
			$temp_query_args_2 = $temp_query_args;
			$term = get_term( $term_id );
			$url = $query_url;
			if ( ! empty( $term ) ) :
				$temp_sectors = array_diff( $sectors, [ $term_id ] );

				if ( ! empty( $temp_sectors ) ) {
					$temp_query_args_2['solution-sector'] = implode( ',', $temp_sectors );
				} else {
					unset( $temp_query_args_2['solution-sector']);
				}
				if ( ! empty( $temp_query_args ) ) {
					$url = add_query_arg( $temp_query_args_2, $query_url );
				}

		?>
			<li class="cpt-filters__term">
				<a href="<?php echo esc_url( $url ); ?>" class="cpt-filters__active">
					<span class="cpt-filters__active-text"><?php echo $term->name; ?></span>
					<span class="cpt-filters__active-icon"><?php echo get_img( 'icon-x' ); ?></span>
				</a>
			</li>
		<?php
			endif;
			endforeach;
	}
	if ( ! empty( $impacts ) ) {
		foreach ( $impacts as $term_id ) :
			$temp_query_args_2 = $temp_query_args;
			$term = get_term( $term_id );
			$url = $query_url;
			if ( ! empty( $term ) ) :
				$temp_impacts = array_diff( $impacts, [ $term_id ] );

				if ( ! empty( $temp_impacts ) ) {
					$temp_query_args_2['solution-climate-impact'] = implode( ',', $temp_impacts );
				} else {
					unset( $temp_query_args_2['solution-climate-impact']);
				}
				if ( ! empty( $temp_query_args ) ) {
					$url = add_query_arg( $temp_query_args_2, $query_url );
				}
		?>
			<li class="cpt-filters__term">
				<a href="<?php echo esc_url( $url ); ?>" class="cpt-filters__active">
					<span class="cpt-filters__active-text"><?php echo $term->name; ?></span>
					<span class="cpt-filters__active-icon"><?php echo get_img( 'icon-x' ); ?></span>
				</a>
			</li>
		<?php
			endif;
			endforeach;
	}
}, 10, 2 );

add_action( 'selected_categories_filter', function( $query_url, $args_for_query ) {
	$url = $query_url;

	$categories   = ! empty( $args_for_query['categories'] ) ? $args_for_query['categories'] : [];
	$sort_by      = ! empty( $args_for_query['sort_by'] ) ? $args_for_query['sort_by'] : '';
	$search_query = ! empty( $args_for_query['search_query'] ) ? $args_for_query['search_query'] : '';
	$post_type    = ! empty( $args_for_query['post_type'] ) ? $args_for_query['post_type'] : [];

	if ( ! empty( $categories ) ) {
		foreach ( $categories as $term_id ) :
			$temp_query_args = [];
			$term = get_term( $term_id );

			if ( ! empty( $term ) ) :
				$temp_categories = array_diff( $categories, [ $term_id ] );

				if ( ! empty( $search_query ) ) {
					$temp_query_args['s'] = esc_attr( $search_query );
				}

				if ( ! empty( $temp_categories ) ) {
					$temp_query_args['categories'] = implode( ',', $temp_categories );
				}

				if ( ! empty( $sort_by ) ) {
					$temp_query_args['sort_by'] = $sort_by;
				}

				if ( ! empty( $post_type ) ) {
					$temp_query_args['post_type'] = $post_type;
				}

				if ( ! empty( $temp_query_args ) ) {
					$url = add_query_arg( $temp_query_args, $query_url );
				}
		?>
			<li class="cpt-filters__term">
				<a href="<?php echo esc_url( $url ); ?>" class="cpt-filters__active">
					<span class="cpt-filters__active-text"><?php echo $term->name; ?></span>
					<span class="cpt-filters__active-icon"><?php echo get_img( 'icon-x' ); ?></span>
				</a>
			</li>
		<?php
			endif;
			endforeach;
	}
}, 10, 2 );

/**
 * Selected Categories Group Filters
 */
add_action( 'selected_categories_groups_filter', function( $query_url, $args_for_query ) {
	$url = $query_url;

	$categories   = ! empty( $args_for_query['categories'] ) ? $args_for_query['categories'] : [];
	$sort_by      = ! empty( $args_for_query['sort_by'] ) ? $args_for_query['sort_by'] : '';
	$search_query = ! empty( $args_for_query['search_query'] ) ? $args_for_query['search_query'] : '';
	$post_type    = ! empty( $args_for_query['post_type'] ) ? $args_for_query['post_type'] : [];
	$tab          = ! empty( $args_for_query['tab'] ) ? $args_for_query['tab'] : [];

	if ( ! empty( $categories ) ) {
		foreach ( $categories as $term_id ) :
			$temp_query_args = [];
			$term = get_term( $term_id );

			if ( ! empty( $term ) ) :
				$temp_categories = array_diff( $categories, [ $term_id ] );

				if ( ! empty( $search_query ) ) {
					$temp_query_args['search'] = esc_attr( $search_query );
				}

				if ( ! empty( $temp_categories ) ) {
					$temp_query_args['categories'] = implode( ',', $temp_categories );
				}

				if ( ! empty( $sort_by ) ) {
					$temp_query_args['sort_by'] = $sort_by;
				}

				if ( ! empty( $post_type ) ) {
					$temp_query_args['post_type'] = $post_type;
				}

				if ( ! empty( $tab ) ) {
                    $temp_query_args['tab'] = $tab;
                }

                if ( ! empty( $temp_query_args ) ) {
                    $url = add_query_arg( $temp_query_args, $query_url );
                }

		?>
			<li class="cpt-filters__term">
				<a href="<?php echo esc_url( $url ); ?>" class="cpt-filters__active">
					<span class="cpt-filters__active-text"><?php echo $term->name; ?></span>
					<span class="cpt-filters__active-icon"><?php echo get_img( 'icon-x' ); ?></span>
				</a>
			</li>
		<?php
			endif;
			endforeach;
	}
}, 10, 2 );


/**
 * Selected Post Types Group Filters
 */
add_action( 'selected_post_types_groups_filter', function( $query_url, $args_for_query ) {
	$url = $query_url;

	$post_types   = get_allowed_post_types( explode( ',', $args_for_query['post_types'] ) );
	$sort_by      = ! empty( $args_for_query['sort_by'] ) ? $args_for_query['sort_by'] : '';
	$search_query = ! empty( $args_for_query['search_query'] ) ? $args_for_query['search_query'] : '';
	$post_type    = ! empty( $args_for_query['post_type'] ) ? $args_for_query['post_type'] : [];
	$categories   = ! empty( $args_for_query['categories'] ) ? $args_for_query['categories'] : [];
	$tab          = ! empty( $args_for_query['tab'] ) ? $args_for_query['tab'] : [];

	$selected_posts = ! empty( $args_for_query['selected_posts'] ) ? $args_for_query['selected_posts'] : '';

	 if( $selected_posts != '' ) {

	    $selected_posts_array = explode( ',', $selected_posts );

        foreach ( $selected_posts_array as $type ) :
        		$temp_query_args = [];
        		$temp_post_types = array_diff( $selected_posts_array, [ $type ] );


        		if ( ! empty( $search_query ) ) {
        			$temp_query_args['search'] = esc_attr( $search_query );
        		}

        		if ( ! empty( $temp_post_types ) ) {
        			$temp_query_args['post_types'] = implode( ',', $temp_post_types );
        		}

        		if ( ! empty( $categories ) ) {
        			$temp_query_args['categories'] = implode( ',', $categories );
        		}

        		if ( ! empty( $sort_by ) ) {
        			$temp_query_args['sort_by'] = $sort_by;
        		}

        		if ( ! empty( $post_type ) ) {
        			$temp_query_args['post_type'] = $post_type;
        		}

                if ( ! empty( $tab ) ) {
                    $temp_query_args['tab'] = $tab;
                }

                if ( ! empty( $temp_query_args ) ) {
                    $url = add_query_arg( $temp_query_args, $query_url );
                }
        	?>
        		<li class="cpt-filters__term">
        			<a href="<?php echo esc_url( $url ); ?>" class="cpt-filters__active">
        				<?php
        					$post_type_name = $type;

        					switch ($type) {
        						case 'forums': $post_type_name = 'forum'; break;
        						case 'forum':  $post_type_name = 'discussion'; break;
        					}
        				?>
        				<span class="cpt-filters__active-text"><?php echo str_replace( '-', ' ', $post_type_name ); ?></span>
        				<span class="cpt-filters__active-icon"><?php echo get_img( 'icon-x' ); ?></span>
        			</a>
        		</li>
        	<?php endforeach;
    }
}, 10, 2 );

/**
 * Selected Post Types
 */
add_action( 'selected_post_types_filter', function( $query_url, $args_for_query ) {
	$url = $query_url;

	$post_types   = get_allowed_post_types( explode( ',', $args_for_query['post_types'] ) );
	$sort_by      = ! empty( $args_for_query['sort_by'] ) ? $args_for_query['sort_by'] : '';
	$search_query = ! empty( $args_for_query['search_query'] ) ? $args_for_query['search_query'] : '';
	$post_type    = ! empty( $args_for_query['post_type'] ) ? $args_for_query['post_type'] : [];
	$categories   = ! empty( $args_for_query['categories'] ) ? $args_for_query['categories'] : [];

	foreach ( $post_types as $type ) :
		$temp_query_args = [];
		$temp_post_types = array_diff( $post_types, [ $type ] );

		if ( ! empty( $search_query ) ) {
			$temp_query_args['s'] = esc_attr( $search_query );
		}

		if ( ! empty( $temp_post_types ) ) {
			$temp_query_args['post_types'] = implode( ',', $temp_post_types );
		}

		if ( ! empty( $categories ) ) {
			$temp_query_args['categories'] = implode( ',', $categories );
		}

		if ( ! empty( $sort_by ) ) {
			$temp_query_args['sort_by'] = $sort_by;
		}

		if ( ! empty( $post_type ) ) {
			$temp_query_args['post_type'] = $post_type;
		}

		if ( ! empty( $temp_query_args ) ) {
			$url = add_query_arg( $temp_query_args, $query_url );
		}
	?>
		<li class="cpt-filters__term">
			<a href="<?php echo esc_url( $url ); ?>" class="cpt-filters__active">
				<?php
					$post_type_name = $type;

					switch ($type) {
						case 'forums': $post_type_name = 'forum'; break;
						case 'forum':  $post_type_name = 'discussion'; break;
					}
				?>
				<span class="cpt-filters__active-text"><?php echo str_replace( '-', ' ', $post_type_name ); ?></span>
				<span class="cpt-filters__active-icon"><?php echo get_img( 'icon-x' ); ?></span>
			</a>
		</li>
	<?php endforeach;
}, 10, 2 );


/**
 * Filtered Query
 */
add_action( 'pre_get_posts', function( $query ) {

	// Filter Admin Query
	if ( is_admin() && $query->is_main_query() ) {
		$meta_query = [];

		// Forums
		if (
			isset( $query->query_vars['post_type'] ) &&
			$query->query_vars['post_type'] === 'forums'
		) {
			$query->set( 'orderby', 'meta_value' );
			$query->set( 'meta_key', 'weight' );
		}

		// Publish To
		if (
			! is_super_admin() &&
			! current_user_can( 'administrator' )
		) {
			$meta_query[] = array(
				'key'     => 'publish_to',
				'value'   => sprintf(':"%s"', get_current_blog_id()),
				'compare' => 'LIKE'
			);
		}
		if ( ! empty( $_GET['blog_top'] ) ) {
			$meta_query[] = array(
				'key'     => 'publish_to',
				'value'   => sprintf(':"%s"', intval( $_GET['blog_top'] )),
				'compare' => 'LIKE'
			);
		}

		// Main Theme/Network Filter
		if ( ! empty( $_GET['theme_network_top'] ) ) {
			$meta_query[] = array(
				'key'     => 'relevant_main_theme_network',
				'value'   => intval( $_GET['theme_network_top'] )
			);
		}

		if ( ! empty( $meta_query ) ) {
			$query->set( 'meta_query', array( $meta_query ) );
		}
	}

	// Filter Search Query
	if ( $query->is_search && $query->is_main_query() && ! is_admin() ) {
		$query->set( 'meta_query', array(
			'RELATION' => 'AND',
			array(
				'key'     => 'publish_to',
				'value'   => sprintf(':"%s"', get_current_blog_id()),
				'compare' => 'LIKE'
			)
		) );

		if ( ! isset( $_GET['post_type'] ) || empty( $_GET['post_type'] ) ) {
			$post_types = get_post_types( array( 'publicly_queryable' => 1 ) );

			unset( $post_types['attachment'] );

			if ( is_array( $post_types ) && count( $post_types ) > 0 ) {
				$query->set( 'post_type', array_keys( $post_types ) );
			}
		}
		else {
			$query->set( 'post_type', get_allowed_post_types( [ esc_attr( $_GET['post_type'] ) ] ) );
		}
	}

	// Filter Theme Query
	if ( ! isset( $query->query_vars['theme_query'] ) ) return;

	if ( ! empty( $query->query_vars['meta_query'] ) ) {
		$query->set( 'meta_query', array(
			'RELATION' => 'AND',
			array(
				'key'     => 'publish_to',
				'value'   => sprintf(':"%s"', get_current_blog_id()),
				'compare' => 'LIKE'
			),
			$query->query_vars['meta_query'] )
		);
	}
	else {
		$query->set( 'meta_query', array( array(
			'key'     => 'publish_to',
			'value'   => sprintf(':"%s"', get_current_blog_id()),
			'compare' => 'LIKE'
		) ) );
	}

	return $query;
} );
