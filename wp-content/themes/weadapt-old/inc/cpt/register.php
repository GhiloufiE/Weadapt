<?php

/**
 * Register Custom Post Type
 */
function register_cpt() {
	$all_post_types = apply_filters( 'theme_cpt', [] );

	if ( ! empty( $all_post_types ) ) {
		foreach ( $all_post_types as $args ) {
			$default_settings = [
				'labels'             => [
					'name'                  => __( $args['multiple_name'] ),
					'singular_name'         => __( $args['singular_name'] ),
					'menu_name'             => __( $args['multiple_name'] ),
					'name_admin_bar'        => __( $args['singular_name'] ),
					'add_new_item'          => sprintf( __( 'Add New %s' ), __( $args['singular_name'] ) ),
					'new_item'              => sprintf( __( 'New %s' ), __( $args['singular_name'] ) ),
					'edit_item'             => sprintf( __( 'Edit %s' ), __( $args['singular_name'] ) ),
					'view_item'             => sprintf( __( 'View %s' ), __( $args['singular_name'] ) ),
					'all_items'             => sprintf( __( 'All %s' ), __( $args['multiple_name'] ) ),
					'search_items'          => sprintf( __( 'Search %s' ), __( $args['multiple_name'] ) ),
					'parent_item_colon'     => sprintf( __( 'Parent %s:' ), __( $args['multiple_name'] ) ),
					'not_found'             => sprintf( __( 'No %s found.' ), __( $args['multiple_name'] ) ),
					'not_found_in_trash'    => sprintf( __( 'No %s found in Trash.' ), __( $args['multiple_name'] ) ),
					'featured_image'        => sprintf( __( '%s Cover Image' ), __( $args['singular_name'] ) ),
					'archives'              => sprintf( __( '%s archives' ), __( $args['singular_name'] ) ),
					'insert_into_item'      => sprintf( __( 'Insert into %s' ), __( $args['singular_name'] ) ),
					'uploaded_to_this_item' => sprintf( __( 'Uploaded to this %s' ), __( $args['singular_name'] ) ),
					'filter_items_list'     => sprintf( __( 'Filter %s list' ), __( $args['multiple_name'] ) ),
					'items_list_navigation' => sprintf( __( '%s list navigation' ), __( $args['multiple_name'] ) ),
					'items_list'            => sprintf( __( '%s list' ), __( $args['multiple_name'] ) ),
				],
				'public'             => false, // will be enable depending on network settings
				'publicly_queryable' => true,
				// 'exclude_from_search'=> false,
				// 'show_ui'            => true,
				// 'show_in_nav_menus'  => true,
				'query_var'          => true,
				'show_in_menu'       => false, // will be enable depending on network settings
				// 'show_in_nav_menus'  => true,
				'show_in_rest'       => true,
				'has_archive'        => true,
				'hierarchical'       => false,
				'rewrite'            => [ 'with_front' => false ],
				'menu_position'      => 5,
				'menu_icon'          => 'dashicons-admin-post',
				'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ]
			];

			$default_settings = apply_filters( 'register_cpt_default_args', $default_settings );

			$post_type_args   = array_merge( $default_settings, $args );

			unset( $post_type_args['post_type'] );

			// Enable Post Type per Site
			$network_post_types = get_theme_network_post_types();

			if ( ! empty( $network_post_types ) && in_array( $args['post_type'], $network_post_types ) ) {
				$post_type_args['public']       = true;
				$post_type_args['show_in_menu'] = true;
			}

			register_post_type( $args['post_type'], $post_type_args );
		}
	}

	// s($GLOBALS['wp_post_types']);
}

add_action( 'init', 'register_cpt' );


/**
 * Register Custom Taxonomies
 */
function register_taxonomies() {
	$all_taxonomies = apply_filters( 'theme_tax', [] );

	if ( ! empty( $all_taxonomies ) ) {
		foreach ( $all_taxonomies as $args ) {
			$default_settings = [
				'labels'             => [
					'name'              => __( $args['multiple_name'] ),
					'singular_name'     => __( $args['singular_name'] ),
					'menu_name'         => ! empty( $args['menu_name'] ) ? __( $args['menu_name'] ) : __( $args['multiple_name'] ),
					'search_items'      => sprintf( __( 'Search %s' ), __( $args['multiple_name'] ) ),
					'all_items'         => sprintf( __( 'All %s' ), __( $args['multiple_name'] ) ),
					'view_item '        => sprintf( __( 'View %s' ), __( $args['singular_name'] ) ),
					'parent_item'       => sprintf( __( 'Parent %s' ), __( $args['singular_name'] ) ),
					'parent_item_colon' => sprintf( __( 'Parent %s' ), __( $args['singular_name'] ) ),
					'edit_item'         => sprintf( __( 'Edit %s' ), __( $args['singular_name'] ) ),
					'update_item'       => sprintf( __( 'Update %s' ), __( $args['singular_name'] ) ),
					'add_new_item'      => sprintf( __( 'Add New %s' ), __( $args['singular_name'] ) ),
					'new_item_name'     => sprintf( __( 'New %s Name' ), __( $args['singular_name'] ) ),
					'back_to_items'     => sprintf( __( '← Back to  %s' ), __( $args['singular_name'] ) ),
				],
				'public'             => false, // will be enable depending on network settings
				// 'publicly_queryable' => true,
				// 'show_ui'            => true,
				// 'show_in_nav_menus'  => true,
				'show_in_rest'       => false, // will be enable depending on network settings
				'query_var'          => false, // will be enable depending on network settings
				'show_admin_column'  => false, // will be enable depending on network settings
				'rewrite'            => false, // will be enable depending on network settings
				'hierarchical'       => true,
			];

			$tax_args = array_merge( $default_settings, $args );

			// Enable Taxonomy per Site
			$has_taxonomy       = ! empty( $args['public'] ) ? $args['public'] : false;
			$network_post_types = get_theme_network_post_types();

			if ( ! empty( $network_post_types ) ) {
				foreach ( $network_post_types as $post_type ) {
					if ( in_array( $post_type, $args['for_types'] ) ) {
						$has_taxonomy = true;

						break;
					}
				}
			}

			if ( $has_taxonomy ) {
				$tax_args['public']            = true;
				$tax_args['show_in_rest']      = isset( $args['show_in_rest'] ) ? wp_validate_boolean( $args['show_in_rest'] ) : true;
				$tax_args['query_var']         = isset( $args['query_var'] ) ? wp_validate_boolean( $args['query_var'] ) : true;
				$tax_args['show_admin_column'] = isset( $args['show_admin_column'] ) ? wp_validate_boolean( $args['show_admin_column'] ) : true;
				$tax_args['rewrite']           = isset( $args['rewrite'] ) ? $args['rewrite'] : true;
			}

			register_taxonomy( $args['tax_slug'], $args['for_types'], $tax_args );
		}
	}
}

add_action( 'init', 'register_taxonomies' );


/**
 * Custom Taxonomies Filter
 */
add_filter( 'theme_tax', function( $taxonomies ) {

	// Tags
	$taxonomies[] = [
		'tax_slug'      => 'tags',
		'for_types'     => [ 'theme', 'network', 'article', 'course', 'event', 'blog', 'organisation', 'case-study' ],
		'singular_name' => 'Tag',
		'multiple_name' => 'Tags',
		'hierarchical'  => false,
		'rest_base'     => 'tag_list' // small rest_base fix
	];

	// Classification
	$taxonomies[] = [
		'tax_slug'      => 'classification',
		'for_types'     => [ 'organisation' ],
		'singular_name' => 'Classification',
		'multiple_name' => 'Services classification',
		'hierarchical'  => false,
	];

	// Solution | Scale
	$taxonomies[] = [
		'tax_slug'          => 'solution-scale',
		'for_types'         => [ 'solutions-portal' ],
		'singular_name'     => 'Scale',
		'multiple_name'     => 'Scales',
		'hierarchical'      => false,
		'show_admin_column' => false,
		'show_in_rest'      => false
	];

	// Solution | Ecosystem type
	$taxonomies[] = [
		'tax_slug'          => 'solution-ecosystem-type',
		'for_types'         => [ 'solutions-portal' ],
		'singular_name'     => 'Ecosystem type',
		'multiple_name'     => 'Ecosystem types',
		'hierarchical'      => false,
		'show_admin_column' => false,
		'show_in_rest'      => false
	];

	// Solution | Scale
	$taxonomies[] = [
		'tax_slug'          => 'solution-type',
		'for_types'         => [ 'solutions-portal' ],
		'singular_name'     => 'Type',
		'multiple_name'     => 'Types',
		'hierarchical'      => false,
		'show_admin_column' => false,
		'show_in_rest'      => false
	];

	// Solution | Sector
	$taxonomies[] = [
		'tax_slug'          => 'solution-sector',
		'for_types'         => [ 'solutions-portal' ],
		'singular_name'     => 'Sector',
		'multiple_name'     => 'Sectors',
		'hierarchical'      => false,
		'show_admin_column' => false,
		'show_in_rest'      => false
	];

	// Solution | Climate impact time-scale
	$taxonomies[] = [
		'tax_slug'          => 'solution-climate-impact',
		'for_types'         => [ 'solutions-portal' ],
		'singular_name'     => 'Climate impact',
		'multiple_name'     => 'Climate impacts',
		'hierarchical'      => false,
		'show_admin_column' => false,
		'show_in_rest'      => false
	];

	// Solution | Climate time-scale
	$taxonomies[] = [
		'tax_slug'          => 'solution-climate-timescale',
		'for_types'         => [ 'solutions-portal' ],
		'singular_name'     => 'Climate time-scale',
		'multiple_name'     => 'Climate time-scales',
		'hierarchical'      => false,
		'show_admin_column' => false,
		'show_in_rest'      => false
	];

	// Solution | Benefit
	$taxonomies[] = [
		'tax_slug'          => 'solution-benefit',
		'for_types'         => [ 'solutions-portal' ],
		'singular_name'     => 'Benefit',
		'multiple_name'     => 'Benefits',
		'hierarchical'      => false,
		'show_admin_column' => false,
		'show_in_rest'      => false
	];

	// Solution | Co-Benefit
	$taxonomies[] = [
		'tax_slug'          => 'solution-co-benefit',
		'for_types'         => [ 'solutions-portal' ],
		'singular_name'     => 'Co-Benefit',
		'multiple_name'     => 'Co-Benefits',
		'hierarchical'      => false,
		'show_admin_column' => false,
		'show_in_rest'      => false
	];

	// Solution | Addressed target
	$taxonomies[] = [
		'tax_slug'          => 'solution-addressed-target',
		'for_types'         => [ 'solutions-portal' ],
		'singular_name'     => 'Addressed target',
		'multiple_name'     => 'Addressed targets',
		'hierarchical'      => false,
		'show_admin_column' => false,
		'show_in_rest'      => false
	];

	// Solution | Addressed sdg
	$taxonomies[] = [
		'tax_slug'          => 'solution-addressed-sdg',
		'for_types'         => [ 'solutions-portal' ],
		'singular_name'     => 'Addressed sdg',
		'multiple_name'     => 'Addressed sdgs',
		'hierarchical'      => false,
		'show_admin_column' => false,
		'show_in_rest'      => false
	];

	return $taxonomies;
} );


/**
 * Custom Post Type Filter
 */
add_filter( 'theme_cpt', function( $post_types ) {

	// Themes
	$post_types[] = [
		'post_type'     => 'theme',
		'singular_name' => 'Theme',
		'multiple_name' => 'Themes',
		'taxonomies'    => [ 'category', 'tags' ],
		'menu_position' => 1,
		'rewrite'       => false,
		'capabilities'  => [
			'edit_post'    => 'edit_posts',
			'create_posts' => 'administrator'
		]
	];

	// Networks
	$post_types[] = [
		'post_type'     => 'network',
		'singular_name' => 'Network',
		'multiple_name' => 'Networks',
		'taxonomies'    => [ 'category', 'tags' ],
		'menu_position' => 3,
		'rewrite'       => false,
		'capabilities'  => [
			'edit_post'    => 'edit_posts',
			'create_posts' => 'administrator'
		]
	];

	// Blog Posts
	$post_types[] = [
		'post_type'     => 'blog',
		'singular_name' => 'Blog Post',
		'multiple_name' => 'Blog Posts',
		'taxonomies'    => [ 'category', 'tags' ],
		'rewrite'       => false,
		'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'comments' ],
	];

	// Articles
	$post_types[] = [
		'post_type'     => 'article',
		'singular_name' => 'Article',
		'multiple_name' => 'Articles',
		'taxonomies'    => [ 'category', 'tags' ],
		'rewrite'       => false,
		'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'comments' ],
	];

	// Courses
	$post_types[] = [
		'post_type'     => 'course',
		'singular_name' => 'Course',
		'multiple_name' => 'Courses',
		'taxonomies'    => [ 'category', 'tags' ],
		'rewrite'       => false,
		'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'comments' ],
	];

	// Events
	$post_types[] = [
		'post_type'     => 'event',
		'singular_name' => 'Event',
		'multiple_name' => 'Events',
		'taxonomies'    => [ 'category', 'tags' ],
		'rewrite'       => false,
		'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'comments' ],
	];

	// Case Studies
	$post_types[] = [
		'post_type'     => 'case-study',
		'singular_name' => 'Case Study',
		'multiple_name' => 'Case Studies',
		'menu_icon'     => 'dashicons-location',
		'taxonomies'    => [ 'category', 'tags' ],
		'rewrite'       => false,
		'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'comments' ],
	];

	// Organisations
	$post_types[] = [
		'post_type'       => 'organisation',
		'singular_name'   => 'Organisation',
		'multiple_name'   => 'Organisations',
		'taxonomies'      => [ 'tags', 'classification' ],
		'menu_icon'       => 'dashicons-id-alt',
	];

	// Solution Portal
	$post_types[] = [
		'post_type'     => 'solutions-portal',
		'singular_name' => 'Solution',
		'multiple_name' => 'Solutions',
		'taxonomies'    => [ 'tags' ],
		'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'comments' ],
	];
	
	// Stakeholders
	$post_types[] = [
		'post_type'     => 'stakeholders',
		'singular_name' => 'Stakeholders',
		'multiple_name' => 'Stakeholders',
		'menu_icon'     => 'dashicons-buddicons-buddypress-logo',
		'taxonomies'    => [ 'category', 'tags' ],
		'rewrite'       => false,
		'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'comments' ],
	];

	// Members
	$post_types[] = [
		'post_type'     => 'members',
		'singular_name' => 'Members',
		'multiple_name' => 'Members',
		'menu_icon'     => 'dashicons-money',
		'taxonomies'    => [ 'category', 'tags' ],
		'rewrite'       => false,
		'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'comments' ],
	];

	// Forums
	$post_types[] = [
		'post_type'     => 'forums',
		'singular_name' => 'Forum',
		'multiple_name' => 'Forums',
		'menu_icon'     => 'dashicons-buddicons-forums',
		'taxonomies'    => [],
		'supports'      => [ 'title', 'editor', 'excerpt', 'revisions' ]
	];

	// Forum
	$post_types[] = [
		'post_type'     => 'forum',
		'singular_name' => 'Forum Topic',
		'multiple_name' => 'Forum Topics',
		'menu_icon'     => 'dashicons-buddicons-topics',
		'taxonomies'    => [ 'tags' ],
		'supports'      => [ 'title', 'editor', 'excerpt', 'revisions', 'comments' ]
	];

	return $post_types;
} );


/**
 * Remove "Posts" post type
 */
add_filter( 'register_post_type_args', function( $args, $post_type ){
	if ( $post_type === 'post' ) {
		$args['public']              = false;
		$args['show_ui']             = false;
		$args['show_in_menu']        = false;
		$args['show_in_admin_bar']   = false;
		$args['show_in_nav_menus']   = false;
		$args['can_export']          = false;
		$args['has_archive']         = false;
		$args['exclude_from_search'] = true;
		$args['publicly_queryable']  = false;
		$args['show_in_rest']        = false;
	}

	return $args;
}, 0, 2);


/**
 * Remove "Categories" from gutenberg, we have ACF
 */
add_filter( 'register_taxonomy_args', function( $args, $name, $object_type ) {
	if ( 'category' === $name ) {
		$args['show_in_rest'] = false;
	}

	return $args;
}, 10, 3 );