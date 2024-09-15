<?php

/**
 * Rewrite Rules
 */
add_action('init', function() {
	global $wp_rewrite;

	// Author Base Url
	$wp_rewrite->author_base = 'member';

	// Add 'knowledge-base/post_name' Rewrite Rules
	foreach ( get_allowed_post_types( [
		'theme',
		'network',
		'blog',
		'article',
		'course',
		'event'
	] ) as $post_type ) {
		$wp_rewrite->add_rewrite_tag("%$post_type%", '([^/]+)', "$post_type=");
		$wp_rewrite->add_permastruct($post_type, "knowledge-base/%$post_type%");
	}

	// Add 'knowledge-base/main_theme_network/post_name' Rewrite Rules
	$wp_rewrite->add_rewrite_tag("%main_theme_network%", '([^/]+)', "main_theme_network=");

	foreach ( get_allowed_post_types( [
		'blog',
		'article',
		'course',
		'event'
	] ) as $post_type ) {
		$wp_rewrite->add_permastruct( $post_type, "knowledge-base/%main_theme_network%/%$post_type%" );
	}

	// Add main_theme_network Query Var
	add_filter( 'query_vars', function( $vars ){
		$vars[] = 'main_theme_network';
		$vars[] = 'cip_station_id';

		return $vars;
	} );

	// Conditionally add rewrite rules for posts without a main_theme_network
	add_rewrite_rule_for_non_main_theme_network_posts();
});

/**
 * Conditionally add rewrite rules for posts that don't have a %main_theme_network%.
 */
function add_rewrite_rule_for_non_main_theme_network_posts() {
    $args = array(
        'post_type'      => array( 'article', 'event' ),
        'posts_per_page' => -1,
        'meta_query'     => array(
            'relation' => 'OR',
            array(
                'key'     => 'relevant_main_theme_network',
                'compare' => 'NOT EXISTS', // No main_theme_network field
            ),
            array(
                'key'     => 'relevant_main_theme_network',
                'value'   => '', // Empty value for main_theme_network
                'compare' => '='
            )
        )
    );

    $posts_without_main_theme_network = get_posts( $args );

    // Only add rewrite rules if there are posts without a main_theme_network
    if ( ! empty( $posts_without_main_theme_network ) ) {
        foreach ($posts_without_main_theme_network as $post) {
            // Dynamically add rules for each post without a main_theme_network
            add_rewrite_rule(
                '^knowledge-base/' . $post->post_name . '/?$',
                'index.php?post_type=' . $post->post_type . '&name=' . $post->post_name,
                'top'
            );
        }
    }
}

/**
 * Fix Custom Permalinks (knowledge-base) 404 Error
 */
add_filter( 'request', function( $request ) {
	$post_types = [
		'theme',
		'network',
		'blog',
		'article',
		'course',
		'event',
		'case-study',
	];

	if (
		! is_admin() &&
		! empty( $request['post_type'] ) &&
		in_array( $request['post_type'], $post_types )
	) {
		$request['post_type'] = $post_types;
	}

	return $request;
} );


/**
 * Fix Attachment Post Type with 'knowledge-base/main_theme_network/post_name' Permalink
 */
add_filter('rewrite_rules_array', function($rules) {
	if ( isset( $rules['knowledge-base/[^/]+/([^/]+)/?$'] ) ) {
		unset( $rules['knowledge-base/[^/]+/([^/]+)/?$'] );
	}

	return $rules;
});


/**
 * Custom Permalinks
 */
add_filter('post_type_link', function($post_link, $post, $leavename, $sample) {
    if ($post->post_type == 'article' || $post->post_type == 'event') {
        // Check if post has a 'relevant_main_theme_network'
        $main_theme_networks = get_field('relevant_main_theme_network', $post->ID);

        // If main theme networks exist and is an array
        if (!empty($main_theme_networks) && is_array($main_theme_networks)) {
            foreach ($main_theme_networks as $main_theme_network_ID) {
                if (!empty($main_theme_network_ID)) {
                    $main_theme_network = get_post($main_theme_network_ID);
                    if ($main_theme_network) {
                        // Replace %main_theme_network% with the actual post name of the main theme network
                        $post_link = str_replace('%main_theme_network%', $main_theme_network->post_name, $post_link);
                        return $post_link;
                    }
                }
            }
        }

        // If there is no main theme network, remove the placeholder
        $post_link = str_replace('/%main_theme_network%', '', $post_link);
    }

    return $post_link;
}, 10, 4);

