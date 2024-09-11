<?php

/**
 * Custom Taxonomies Filter
 */
add_filter( 'theme_tax', function( $taxonomies ) {

	// Roles
	$taxonomies[] = [
		'tax_slug'           => 'role',
		'for_types'          => [],
		'singular_name'      => 'Role',
		'multiple_name'      => 'Roles',
		'hierarchical'       => false,
		'public'             => true,
		'publicly_queryable' => false,
	];

	// Interests
	$taxonomies[] = [
		'tax_slug'           => 'interest',
		'for_types'          => [],
		'singular_name'      => 'Interest',
		'multiple_name'      => 'Interests',
		'hierarchical'       => false,
		'public'             => true,
		'publicly_queryable' => false,
	];

	// Badges
	$taxonomies[] = [
		'tax_slug'           => 'badge',
		'for_types'          => [],
		'singular_name'      => 'Badge',
		'multiple_name'      => 'Badges',
		'hierarchical'       => false,
		'public'             => true,
		'publicly_queryable' => false,
	];

	return $taxonomies;
} );


/**
 * Admin page for user taxonomies
 */
add_action( 'admin_menu', function() {
	foreach ( [
		'role',
		'interest',
		'badge'
	] as $taxonomy_name ) {
		$taxonomy = get_taxonomy( $taxonomy_name );

		add_users_page(
			esc_attr( $taxonomy->labels->menu_name ),
			esc_attr( $taxonomy->labels->menu_name ),
			$taxonomy->cap->manage_terms,
			'edit-tags.php?taxonomy=' . $taxonomy->name
		);
	}

} );


/**
 * Unsets the 'posts' column
 */
foreach ( [
	'role',
	'interest',
	'badge'
] as $taxonomy_name ) {
	add_filter( "manage_edit-{$taxonomy_name}_columns", function( $columns ) {
		unset( $columns['posts'] );

		return $columns;
	} );
}