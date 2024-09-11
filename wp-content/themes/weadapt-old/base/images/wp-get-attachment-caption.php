<?php

/**
 * Fix spaces before links
 */
add_filter( 'wp_get_attachment_caption', function( $caption, $post_ID ) {
	$pattern = '/<a(.*?)>(.*?)<\/a>/i';

	$сallback = function ($matches) {
		return ' <a' . $matches[1] . '>' . $matches[2] . '</a> ';
	};

	$caption = preg_replace_callback($pattern, $сallback, $caption);
	$caption = trim( str_replace( '  ', ' ', $caption ) );

	return $caption;
}, 10, 2 );