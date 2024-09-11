<?php


/**
 * Set Multisite Global Users Query
 */
add_action( 'pre_get_users', function( $user_query ) {
	if ( ! empty( $user_query->query_vars['theme_query'] ) ) {
		return $user_query;
	}

	if (
		is_admin() &&
		function_exists( 'get_current_screen' ) &&
		isset( get_current_screen()->base ) &&
		get_current_screen()->base === 'users'
	) {
		if ( empty( $_GET['role'] ) ) {
			$user_query->set( 'blog_id', 0 );
		}
	}
	else {
		$user_query->set( 'blog_id', 0 );
	}

	return $user_query;
} );


/**
 * Filters the list of available list table views
 */
add_filter( 'views_users', function( $views ) {
	$total_users = get_user_count();
	$all_class   = empty( $_GET['role'] ) ? ' class="current"' : '';

	$views['all'] = sprintf( '<a%s href="%s">%s</a>', $all_class, esc_url( add_query_arg( 'list', 'all_network', 'users.php' ) ), sprintf( _nx(
		'All <span class="count">(%s)</span>',
		'All <span class="count">(%s)</span>',
		$total_users,
		'users',
		'weadapt'
	), number_format_i18n( $total_users ) ) );

	return $views;
} );