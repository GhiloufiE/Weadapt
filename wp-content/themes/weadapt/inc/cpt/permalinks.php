<?php

/**
 * Rewrite Rules
 */
add_action('init', function() {
	global $wp_rewrite;

	// Author Base Url
	$wp_rewrite->author_base = 'member';

	// Allowed post types for knowledge-base structure
	$post_types = [
		'theme',
		'network',
		'blog',
		'article',
		'course',
		'event'
	];

	// Add 'knowledge-base/post_name' and 'knowledge-base/main_theme_network/post_name' Rewrite Rules
	foreach ( get_allowed_post_types( $post_types ) as $post_type ) {
		$wp_rewrite->add_rewrite_tag("%$post_type%", '([^/]+)', "$post_type=");
		$wp_rewrite->add_permastruct($post_type, "knowledge-base/%$post_type%");
	}

	// Separate handling for post types with 'main_theme_network'
	$networked_post_types = ['blog', 'article', 'course', 'event'];
	foreach ( get_allowed_post_types( $networked_post_types ) as $post_type ) {
		$wp_rewrite->add_permastruct($post_type, "knowledge-base/%main_theme_network%/%$post_type%");
	}

	// Add main_theme_network Query Var
	add_filter( 'query_vars', function( $vars ) {
		array_push($vars, 'main_theme_network', 'cip_station_id');
		return $vars;
	});

	// Flushing rules only when necessary (best during plugin/theme activation)
	// $wp_rewrite->flush_rules();
});

/**
 * Conditionally add rewrite rules for posts that don't have a %main_theme_network%.
 */
function add_generic_rewrite_rule_for_non_main_theme_network_posts() {
	// Single call to add_rewrite_rule for 'article' and 'event' post types
	add_rewrite_rule(
		'^knowledge-base/([^/]+)/?$',
		'index.php?post_type=article&name=$matches[1]',
		'top'
	);
	add_rewrite_rule(
		'^knowledge-base/([^/]+)/?$',
		'index.php?post_type=event&name=$matches[1]',
		'top'
	);
}
add_action('init', 'add_generic_rewrite_rule_for_non_main_theme_network_posts');

/**
 * Fix Custom Permalinks (knowledge-base) 404 Error
 */
add_filter('request', function( $request ) {
	$post_types = [
		'theme',
		'network',
		'blog',
		'article',
		'course',
		'event',
		'case-study'
	];

	if (
		! is_admin() &&
		isset( $request['post_type'] ) &&
		in_array( $request['post_type'], $post_types )
	) {
		$request['post_type'] = $post_types;
	}

	return $request;
});

/**
 * Fix Attachment Post Type with 'knowledge-base/main_theme_network/post_name' Permalink
 */
add_filter('rewrite_rules_array', function( $rules ) {
	// Remove problematic attachment rule if it exists
	if ( isset( $rules['knowledge-base/[^/]+/([^/]+)/?$'] ) ) {
		unset( $rules['knowledge-base/[^/]+/([^/]+)/?$'] );
	}

	return $rules;
});

/**
 * Custom Permalinks
 */
add_filter('post_type_link', function( $post_link, $post, $leavename, $sample ) {
	// Handle main_theme_network in the permalink structure
	if ( strpos( $post_link, '/%main_theme_network%/' ) !== false ) {
		$main_theme_networks = get_field( 'relevant_main_theme_network', $post->ID );

		// Replace placeholder with first valid main_theme_network slug
		if ( ! empty( $main_theme_networks ) && is_array( $main_theme_networks ) ) {
			foreach ( $main_theme_networks as $main_theme_network_ID ) {
				if ( ! empty( $main_theme_network_ID ) ) {
					$main_theme_network = get_post( $main_theme_network_ID );
					if ( $main_theme_network ) {
						$post_link = str_replace( '%main_theme_network%', $main_theme_network->post_name, $post_link );
						break;
					}
				}
			}
		}

		// If no valid main_theme_network found, remove the placeholder
		$post_link = str_replace( '/%main_theme_network%', '', $post_link );
	}

	return $post_link;
}, 10, 4);
