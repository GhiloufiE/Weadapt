<?php

/**
 * Output Post Excerpt HTML
 */
function the_post_excerpt_html( $post_ID = 0 ) {
	if ( has_excerpt( $post_ID ) ) {
		$excerpt = get_the_excerpt( $post_ID );

		if ( get_query_var( 'theme_short_excerpt' ) ) {
			$excerpt = wp_trim_words( $excerpt, 18, null );
		}

		?><div class="cpt-list-item__excerpt"><?php
			echo wp_kses_post( $excerpt );
		?></div><?php
	}
}