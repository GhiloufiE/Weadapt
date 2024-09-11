<?php

add_filter( 'body_class', function( $classes ) {
	return array_merge( $classes, array( 'blog-' . get_current_blog_id() ) );
} );