<?php

/**
 * Admin Bar Menu
 */
add_action( 'add_admin_bar_menus', function() {

	// WordPress menu
	remove_action( 'admin_bar_menu', 'wp_admin_bar_wp_menu', 10 );

	if ( ! current_user_can( 'administrator' ) ) {

		// User-related, aligned right.
		remove_action( 'admin_bar_menu', 'wp_admin_bar_search_menu', 4 );
		remove_action( 'admin_bar_menu', 'wp_admin_bar_my_account_menu', 0 );
		remove_action( 'admin_bar_menu', 'wp_admin_bar_my_account_item', 7 );

		add_action( 'admin_bar_menu', function( $wp_admin_bar ) {
			$user_id      = get_current_user_id();
			$current_user = wp_get_current_user();

			if ( ! $user_id ) {
				return;
			}

			$profile_ID = get_page_id_by_template( 'profile' );

			if ( $profile_ID ) {
				$profile_url = get_permalink( $profile_ID );
			}
			else {
				$profile_url = false;
			}

			$avatar = get_avatar( $user_id, 26 );
			$howdy = sprintf( __( 'Hello, %s' ), '<span class="display-name">' . $current_user->display_name . '</span>' );
			$class = empty( $avatar ) ? '' : 'with-avatar';

			$wp_admin_bar->add_node(
				array(
					'id'     => 'my-account',
					'parent' => 'top-secondary',
					'title'  => $howdy . $avatar,
					'href'   => $profile_url,
					'meta'   => array(
						'class' => $class,
					),
				)
			);
		}, 7 );

		// Site-related.
		remove_action( 'admin_bar_menu', 'wp_admin_bar_my_sites_menu', 20 );
		remove_action( 'admin_bar_menu', 'wp_admin_bar_customize_menu', 40 );

		// Content-related.
		if ( ! is_network_admin() && ! is_user_admin() ) {
			remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
		}
	}

	if ( ! current_user_can( 'publish_posts' ) ) {

		// New Media Link
		add_action( 'admin_bar_menu', function( $wp_admin_bar ) {
			$wp_admin_bar->remove_node( 'new-media' );
		}, 201 );
	}
} );


/**
 * Admin Menu
 */
add_action( 'admin_menu', function() {
	if ( ! current_user_can( 'publish_posts' ) ) {

		// My Sites
		global $submenu;

		if ( isset( $submenu['index.php'][5][2] ) && $submenu['index.php'][5][2] === 'my-sites.php' ) {
			unset( $submenu['index.php'][5] );
		}

		// Media
		remove_menu_page( 'upload.php' );

		// Organizations
		remove_menu_page( 'edit.php?post_type=organisation' );
	}

	if ( ! current_user_can( 'administrator' ) ) {

		// Users
		remove_menu_page( 'users.php' );
		remove_menu_page( 'profile.php' );

		// Comments
		remove_menu_page( 'edit-comments.php' );

		// Tools
		remove_menu_page( 'tools.php' );

		// Front End PM PRO
		remove_menu_page( 'fep-all-messages' );
	}
}, 99 );


/**
 * Dashboard
 */
add_action( 'wp_dashboard_setup', function() {

	if ( ! current_user_can( 'administrator' ) ) {

		// Side
		foreach ( [
			// 'dashboard_quick_press',
			'dashboard_recent_drafts',
			'dashboard_primary',
			'dashboard_secondary',
		] as $meta_box_key ) {
			if ( isset( $GLOBALS['wp_meta_boxes']['dashboard']['side']['core'][$meta_box_key] ) ) {
				unset( $GLOBALS['wp_meta_boxes']['dashboard']['side']['core'][$meta_box_key] );
			}
		}

		// Normal
		foreach ( [
			'dashboard_incoming_links',
			'dashboard_right_now',
			'dashboard_recent_comments',
			'dashboard_plugins',
			'dashboard_activity',
			'dashboard_site_health',
		] as $meta_box_key ) {
			if ( isset( $GLOBALS['wp_meta_boxes']['dashboard']['normal']['core'][$meta_box_key] ) ) {
				unset( $GLOBALS['wp_meta_boxes']['dashboard']['normal']['core'][$meta_box_key] );
			}
		}
	}


	// Welcome Panel
	remove_action( 'welcome_panel', 'wp_welcome_panel' );
} );


// Add admin body class / Hide some ACF fields with CSS
add_filter( 'admin_body_class', function( $classes ) {
	$current_user = wp_get_current_user();

	if ( ! empty( $current_user->roles ) ) {
		foreach ( $current_user->roles as $role ) {
			$classes .= " user-has-role-$role";
		}
	}

	$classes .= ' blog-' . get_current_blog_id();

	return $classes;
} );



/**
 * Add Registered Column
*/
add_filter( 'manage_users_columns', function ( $columns ) {
	$columns['registered'] = __( 'Registered', 'weadapt' );

	return $columns;
} );

add_filter( 'manage_users_sortable_columns', function ( $sortable_columns ) {
	$sortable_columns['registered'] = 'registered';

	return $sortable_columns;
} );

add_action( 'pre_get_users', function ( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}

	$orderby = $query->get( 'orderby' );

	if ( 'registered' === $orderby ) {
		$query->set( 'orderby', 'idate' );
	}
} );

add_filter( 'manage_users_custom_column', function ( $val, $column_name, $user_id ) {
	switch ( $column_name ) {
		case 'registered':
			return get_the_author_meta( 'user_registered', $user_id );
		default:
			return $val;
	}
}, 10, 3 );
