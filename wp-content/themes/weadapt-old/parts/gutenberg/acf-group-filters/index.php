<?php

/**
 * Group Filter Block
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */

    $block_object = new Block( $block );

    $name = $block_object->name();
    $attr = $block_object->attr( 'background-' . get_field( 'background_color' ) );

    $query_type     = 'wp_query';
    $show_filters    = true;
    $show_search     = true;
    $is_upcoming	 = false;

    $show_categories = get_field('show_categories');
    $show_post_types = get_field('show_post_types');

    $base_url        = get_current_clean_url();

    $groups = get_field('groups');
    $per_page    = get_field( 'posts_per_page' ) ? get_field( 'posts_per_page' ) : 6;

    $query_args = [
        'post_status'         => 'publish',
        'posts_per_page'      => $per_page,
        'orderby'             => 'date',
        'order'               => 'DESC',
        'ignore_sticky_posts' => true,
        'theme_query'         => true, // multisite fix
    ];

?>

<section <?php echo $attr; ?>>

    <style>
        <?php
            load_inline_styles_shared( 'archive' );
            load_inline_styles_shared( 'cpt-list-item' );
            load_inline_styles( __DIR__, $name );
    	?>
    </style>
	<?php load_inline_dependencies( '/parts/gutenberg/core-heading/', 'core-heading'); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-paragraph/', 'core-paragraph'); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-button/', 'core-button'); ?>

    <div class="container">
       <?php

          $all_post_types = [];
          $posts_type = [];
          $all_categories = [];
		  $categories = [];
		  $tabs = [];

          foreach( $groups as $group ) {
          		  if( ! empty($group['post_type']) and ! empty($all_post_types)) {
					$all_post_types = array_merge( $all_post_types, $group['post_type'] );
          		  } elseif( empty( $categories)  ) {
                    $all_post_types = $group['post_type'];
                  }
                  $posts_type[] = $group['post_type'];
                  if( ! empty( $group['tab'] ) ) {
                       $tabs[] = $group['tab'];
                  }
          }
          $all_post_types = array_unique($all_post_types);
          $tabs = array_unique($tabs);
       ?>

        <?php if ( count($tabs) > 1 ) : ?>
           <div class="tabs-container">
                <ul class="tabs">
                    <?php foreach ( $tabs as $tab ) : ?>
                        <?php if( ! empty( $tab ) ) : ?>
                            <li><?php echo $tab; ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
           </div>
        <?php endif; ?>

       <?php
          foreach( $groups as $group ) {

                $query_args['post_type'] = get_allowed_post_types( $group['post_type'] );
                $query_args['query_post_types'] = $group['post_type'];

                $terms_query = new WP_Query( array_merge( $query_args, [
                    'posts_per_page' => -1,
                    'fields'         => 'ids',
                ] ) );

                $group_categories = [];
                if ( ! empty( $terms_query->posts ) ) {
                    $group_categories = wp_get_object_terms( $terms_query->posts, 'category', array('fields' => 'ids') );
                    $categories[] = array_map( function( $item ) {
                        return $item . "";
                    } , $group_categories );
                }
				if( ! empty( $all_categories ) and ! empty( $categories ) ) {
                	$all_categories = array_merge( $all_categories, end($categories) );
				} elseif( ! empty( $categories)  ) {
                    $all_categories = end($categories);
				}
         }
         $all_categories = array_unique($all_categories);

         $filterData = $show_categories ? $all_categories : $all_post_types ;

         foreach( $groups as $index => $group ) {

            $meta_query = [
               'relation' => 'AND'
           ];
           $tax_query = [
           	'relation' => 'AND'
           ];

		   $query_args['meta_query'] = $meta_query;
		   $query_args['all_post_types'] = $filterData;
		   $query_args['tax_query'] = $tax_query;
           $query_args['post_type'] = get_allowed_post_types( $group['post_type'] );
           $query_args['query_post_types'] = $group['post_type'];

           // Events Meta
           if ( count( $group['post_type'] ) == 1 && in_array( 'event', $group['post_type'] ) ) {
               $event_type = $group['event_type'] ? $group['event_type'] : 'all';
               $types      = $group['types'] ? $group['types'] : 'none';

               if ( $event_type !== 'all' ) {
                   $query_args['orderby']   = 'meta_value';
                   $query_args['meta_key']  = 'start_date';
                   $query_args['meta_type'] = 'DATETIME';
               }

               if ( $event_type === 'upcoming' ) {
	               $query_args['order'] = 'ASC';
				   $is_upcoming = true;

                   $meta_query[] = [
                       'relation'          => 'AND',
                       array(
                           'key'       => 'start_date',
                           'compare'   => 'EXISTS'
                       ),
                       array(
                           'key'       => 'start_date',
                           'value'     => date('Y-m-d H:i:s'),
                           'compare'   => '>',
                           'type'      => 'DATETIME'
                       )
                   ];
               }
               else if ( $event_type === 'past' ) {
                   $meta_query[] = [
                       'key'           => 'start_date',
                       'compare'       => '<=',
                       'value'         => date('Y-m-d H:i:s'),
                       'type'          => 'DATETIME',
                   ];
               }

               if ( ! empty( $types ) && $types !== 'none' ) {
                   $meta_query[] = [
                       'key'   => 'type',
                       'value' => $types
                   ];
               }
           }

           // Categories
           if ( ! empty( $include_categories = $group['categories'] ) ) {
           	$query_args['category__in'] = wp_parse_id_list( $include_categories );
           }
           if ( ! empty( $exclude_categories = $group['exclude_categories'] ) ) {
           	$query_args['category__not_in'] = wp_parse_id_list( $exclude_categories );
           }

           // Tags
           if ( ! empty( $include_tags =  $group['tags'] ) ) {
           	$tax_query[] = [
           		'taxonomy' => 'tags',
           		'terms'    => wp_parse_id_list( $include_tags ),
           		'operator' => 'AND'
           	];
           }
           if ( ! empty( $exclude_tags = $group['exclude_tags'] ) ) {
           	$tax_query[] = [
           		'taxonomy' => 'tags',
           		'terms'    => wp_parse_id_list( $exclude_tags ),
           		'operator' => 'NOT IN'
           	];
           }

          // Meta Query
          if ( ! empty( $meta_query ) ) {
              $query_args['meta_query'] = $meta_query;
          }

          // Tax Query
          if ( ! empty( $tax_query ) ) {
            $query_args['tax_query'] = $tax_query;
          }

           get_part( 'components/cpt-group-filters-query/index', [
                'number' => $index,
                'title' => $group['title'],
                'query_args' => $query_args,
                'filters_args' => $index === 0 ? $filterData : [],
                'categories' => $index === 0 ? $all_categories : [],
                'show_filters' => $index === 0 ? true : false,
                'show_search' => $index === 0 ? true : false,
                'show_post_types' => $show_post_types,
                'show_categories' => $show_categories,
                'search_term' => ! empty( $_GET['search'] ) ? $_GET['search'] : '',
                'tab' => $group['tab'],
                'is_upcoming' => $is_upcoming
           ]);
           $query_args = [
			   'post_status'         => 'publish',
			   'posts_per_page'      => $per_page,
			   'orderby'             => 'date',
			   'order'               => 'DESC',
			   'ignore_sticky_posts' => true,
			   'theme_query'         => true, // multisite fix
		   ];
         }

         wp_localize_script('weadapt/group-filters', 'groupsData', $show_categories ? [] : $posts_type );
         wp_localize_script('weadapt/group-filters', 'groupsDataCategories', $categories);
       ?>
    </div>

</section>
