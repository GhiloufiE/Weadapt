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
		$wp_rewrite->add_permastruct($post_type, "knowledge-base/%$post_type%" );
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


	// Add 'placemarks/maps/view/post_id' Rewrite Rule
	foreach ( get_allowed_post_types( [
		'case-study'
	] ) as $post_type ) {
		$wp_rewrite->add_rewrite_tag("%$post_type%", '([^/]+)', "$post_type=");
		$wp_rewrite->add_permastruct($post_type, "placemarks/maps/view/%$post_type%" );

		$map_page_ID = url_to_postid( get_home_url( null, '/placemarks/maps/' ) );

		if ( $map_page_ID ) {
			add_rewrite_rule('placemarks/maps/weather-station/([0-9]+)/?$', 'index.php?page_id=' . $map_page_ID . '&cip_station_id=$matches[1]', 'top');
		}
	}


	// Add main_theme_network Query Var
	add_filter( 'query_vars', function( $vars ){
		$vars[] = 'main_theme_network';
		$vars[] = 'cip_station_id';

		return $vars;
	} );
} );


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
add_filter('post_type_link', function( $post_link, $post, $leavename, $sample ) {
    
    // Blog, Articles, Courses, Events
    if ( false !== strpos( $post_link, '/%main_theme_network%/' ) ) {
        $main_theme_networks = get_field( 'relevant_main_theme_network', $post->ID );

        if ( ! empty( $main_theme_networks ) && is_array( $main_theme_networks ) ) {
            // Get the first non-empty main theme network
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
        
        // If no valid main theme network was found, remove the placeholder
        if ( false !== strpos( $post_link, '/%main_theme_network%' ) ) {
            $post_link = str_replace( '/%main_theme_network%', '', $post_link );
        }
    }

    return $post_link;
}, 10, 4);