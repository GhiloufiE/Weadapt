<?php

/**
 * Set 'draft' tag status
 */
add_filter( 'saved_tags', function( $term_id, $tt_id, $updated, $args ) {
	if ( ! current_user_can( 'manage_categories' ) ) {
		update_field( 'status', false, "term_$term_id" );
	}

	return $term_id;
}, 10, 4 );


/**
 * Add 'status' tag column
 */
add_filter( 'manage_edit-tags_columns', function( $columns ) {
	$columns['status'] = __( 'Status', 'weadapt' );

	return $columns;
});


/**
 * Add 'status' tag column content
 */
add_filter('manage_tags_custom_column', function( $string, $column_name, $term_id ) {
	$status = get_field( 'status', "term_$term_id" );
	$string = $status === false ? __( 'Draft', 'weadapt' ) : __( 'Publish', 'weadapt' );

	return $string;
}, 10, 3 );


/**
 * Add 'status' tag column sortable
 */
add_filter( 'manage_edit-tags_sortable_columns', function ( $sortable_columns ){
	$sortable_columns['status'] = [ 'status_status', false ];

	return $sortable_columns;
} );


/**
 * Filter 'status' sortable args
 */
add_filter( 'get_terms_args', function( $args, $taxonomies ) {
	if (
		! empty( $args['orderby'] ) &&
		$args['orderby'] === 'status_status' &&
		in_array( 'tags', $taxonomies )
	) {
		if ( ! empty( $args['order'] ) && $args['order'] === 'asc' ) {
			$args['meta_query'] = [ [
				'key'   => 'status',
				'value' => 0,
			] ];
		}
		else {
			$args['meta_query'] = [
				'relation' => 'OR',
				[
					'key'   => 'status',
					'value' => 1,
				],
				[
					'key'     => 'status',
					'compare' => 'NOT EXISTS',
				],
			];
		}
	}

	return $args;
}, 10, 2 );



/**
 * Filter 'status' term meta globaly
 */
add_filter( 'pre_get_terms', function( $query ) {
	if (
		isset( $query->query_vars['theme_query'] ) &&
		! empty( $query->query_vars['taxonomy'] ) &&
		in_array( 'tags', $query->query_vars['taxonomy'] )
	) {
		$query->meta_query->queries = array_merge( $query->meta_query->queries, [
			'relation' => 'OR',
			[
				'key'   => 'status',
				'value' => 1,
			],
			[
				'key'     => 'status',
				'compare' => 'NOT EXISTS',
			],
		] );
	}

	return $query;
}, 10, 2 );