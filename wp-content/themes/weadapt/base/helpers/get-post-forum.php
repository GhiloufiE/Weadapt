<?php

/**
 * Get Post Forum
 */
if ( ! function_exists( 'get_post_forum' ) ) :

	function get_post_forum( $post_ID = 0 ) {
		$cache_key = "post_forum_$post_ID";
		$forum_ID  = wp_cache_get( $cache_key, 'site-options' );

		if ( ! isset( $forum_ID ) || false === $forum_ID ) {
			$theme_network_forum = get_page_by_path( 'themes-networks-and-projects', OBJECT, 'forums' );
			$parent_ID           = ! empty( $theme_network_forum->ID ) ? $theme_network_forum->ID : 0;

			$related_forum = get_posts( array(
				'numberposts'	=> -1,
				'post_type'		=> 'forums',
				'post_status'   => 'any',
				'meta_query'    => [ [
					'relation' => 'AND',
					[
						'key'   => 'parent',
						'value' => $parent_ID,
					],
					[
						'key'   => 'relevant_main_theme_network',
						'value' => $post_ID,
					]
				] ],
			) );

			$forum_ID = ! empty( $related_forum[0]->ID ) ? $related_forum[0]->ID : 0;

			wp_cache_set( $cache_key, $forum_ID, 'site-options' );
		}

		return $forum_ID;
	}

endif;