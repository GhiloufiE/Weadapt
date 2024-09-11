<?php


// Temp Code | Add Editor (Microsite Editor)
function add_microsite_editor_role() {
	global $wp_roles;

	// remove_role( 'editor' );
	add_role( 'editor', 'Editor' );

	foreach ([
		// WordPress 2.0
		// 'moderate_comments',
		'manage_categories',
		'manage_links',
		'upload_files',
		'unfiltered_html',
		'edit_posts',
		'edit_others_posts',
		'edit_published_posts',
		'publish_posts',
		// 'edit_pages',
		'read',
		'level_7',
		'level_6',
		'level_5',
		'level_4',
		'level_3',
		'level_2',
		'level_1',
		'level_0',

		// WordPress 2.1
		// 'edit_others_pages',
		// 'edit_published_pages',
		// 'publish_pages',
		// 'delete_pages',
		// 'delete_others_pages',
		// 'delete_published_pages',
		'delete_posts',
		'delete_others_posts',
		'delete_published_posts',
		'delete_private_posts',
		'edit_private_posts',
		'read_private_posts',
		// 'delete_private_pages',
		// 'edit_private_pages',
		// 'read_private_pages',
	] as $cap ) {
		$wp_roles->add_cap( 'editor', $cap );
	}
}


/**
 * Create custom User Roles
 */
add_action( 'after_switch_theme', function() {
	$administrator = get_role( 'administrator' );

	add_role( 'pro', __( 'Pro', 'weadapt' ), $administrator->capabilities );
	add_role( 'survey-administrator', __( 'Survey Administrator', 'weadapt' ), $administrator->capabilities );
	add_role( 'no-notifs-administrator', __( 'No Notifs Administrator', 'weadapt' ), $administrator->capabilities );


	// Change Caps
	global $wp_roles;

	// Editor (Microsite Editor)
	foreach ([
		'moderate_comments',
		'edit_pages',
		'edit_others_pages',
		'edit_published_pages',
		'publish_pages',
		'delete_pages',
		'delete_others_pages',
		'delete_published_pages',
		'delete_private_pages',
		'edit_private_pages',
		'read_private_pages',
	] as $cap ) {
		$wp_roles->remove_cap( 'editor', $cap );
	}

	// Contributor
	$wp_roles->remove_cap( 'contributor', 'delete_posts' );

	// Author (Editor)
	$wp_roles->remove_cap( 'author', 'delete_posts' );
	$wp_roles->remove_cap( 'author', 'delete_others_posts' );

	$wp_roles->add_cap( 'author', 'manage_categories' );
} );


/**
 * Rename Roles
 */
add_action( 'wp_roles_init', static function ( \WP_Roles $roles ) {
	// Editor (Microsite Editor)
	$roles->roles['editor']['name'] = __( 'Microsite Editor', 'weadapt' );
	$roles->role_names['editor'] = __( 'Microsite Editor', 'weadapt' );

	// Temp Code | Add Microsite Editor
	if ( empty( $roles->roles['editor']['capabilities'] ) ) {
		add_microsite_editor_role();
	}

	// Author (Editor)
	$roles->roles['author']['name'] = __( 'Editor', 'weadapt' );
	$roles->role_names['author'] = __( 'Editor', 'weadapt' );
} );


/**
 * Add User to blog if not is_user_member_of_blog()
 */
add_action( 'init', function() {
	if ( ! is_user_logged_in() ) {
		return;
	}

	$current_user = wp_get_current_user();
	$default_role = get_option( 'default_role' );

	// Add User To Blog
	if ( ! is_user_member_of_blog() && $current_user->exists() && empty( $current_user->roles ) ) {
		add_user_to_blog( get_current_blog_id(), $current_user->ID, $default_role );
	}

	// Add User upload_files Cap
	if ( ! current_user_can( 'upload_files' ) && in_array( $default_role, $current_user->roles ) ) {
		$current_user->add_cap( 'upload_files' );
	}
}, 0 );


/**
 * Allow Theme/Network Author editing posts
 */
add_action( 'init', function() {
	if ( ! is_user_logged_in() ) {
		return;
	}

	$current_user = wp_get_current_user();

	// Contributor
	if ( in_array( 'contributor', $current_user->roles ) ) {
		add_filter( 'map_meta_cap', function( $caps, $cap, $user_id, $args ) {

			if (
				// $args[0] holds post ID.
				! empty( $args[0] ) &&

				// Only edit_post cap
				'edit_post' === $cap
			) {
				$people = get_field( 'people', $args[0] );

				foreach ( [
					'creator',
					'publisher',
					'contributors',
					'editors'
				] as $role ) {
					if ( ! empty( $people[$role] ) && in_array( $user_id, $people[$role] ) ) {
						$caps = ['edit_posts'];
					}
				}
			}

			return $caps;
		}, 10, 4 );
	}

	// Author (Editor)
	if ( in_array( 'author', $current_user->roles ) ) {
		add_filter( 'map_meta_cap', function( $caps, $cap, $user_id, $args ) {
	
			if (
				// $args[0] holds post ID.
				! empty( $args[0] ) &&
	
				// Only edit_post cap
				'edit_others_posts' === $cap
			) {
				$caps = ['do_not_allow'];
			}
	
	
			if (
				// $args[0] holds post ID.
				! empty( $args[0] ) &&
	
				// Only edit_post cap
				'edit_post' === $cap
			) {
				$main_theme_networks = get_field( 'relevant_main_theme_network', $args[0] );
	
				if ( ! empty( $main_theme_networks ) && is_array( $main_theme_networks ) ) {
					foreach ( $main_theme_networks as $main_theme_network ) {
						$main_theme_network_editors = get_field( 'people_editors', $main_theme_network );
	
						if (
							! empty( $main_theme_network_editors ) &&
							in_array( $user_id, $main_theme_network_editors )
						) {
							$caps = [ 'edit_posts' ];
							break;
						}
					}
				}
	
				$people = get_field( 'people', $args[0] );
	
				foreach ( [
					'creator',
					'publisher',
					'contributors',
					'editors'
				] as $role ) {
					if ( ! empty( $people[$role] ) && in_array( $user_id, $people[$role] ) ) {
						$caps = ['edit_posts'];
					}
				}
	
				// Allow Microsite Editors edit Organization
				if ( get_post_type($args[0]) === 'organisation' ) {
					$caps = ['edit_posts'];
				}
			}
	
			return $caps;
		}, 10, 4 );
	}
	

	// Administrator / Editor (Microsite Editor)
	if (
		( in_array( 'administrator', $current_user->roles ) && ! is_super_admin() ) ||
		in_array( 'editor', $current_user->roles )
	) {
		add_filter( 'map_meta_cap', function( $caps, $cap, $user_id, $args ) {
			if (
				// $args[0] holds post ID.
				! empty( $args[0] ) &&

				// Only delete_post, edit_post cap
				in_array( $cap, array('delete_post', 'edit_post' ) )
			) {
				$publish_to = get_field( 'publish_to', $args[0] );

				if ( ! empty( $publish_to ) && ! in_array( get_current_blog_id(), $publish_to ) ) {
					$caps = [ 'do_not_allow' ];
				}
			}

			return $caps;
		}, 10, 4 );
	}
}, 0 );