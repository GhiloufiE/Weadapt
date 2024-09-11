<?php

if ( 1 === get_current_blog_id() ) {
	add_filter( 'grid_template_post_meta', function( $post_ID ) {
		return [
			['icon-calendar', get_the_date( 'd/m/Y - H:i', $post_ID )],
		];
	} );
}