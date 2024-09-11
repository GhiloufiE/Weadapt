<?php

/**
 * Check if post type allow send mails
 */
function is_mailed_post_type( $post_type ) {
	$is_mailed = false;

	if ( in_array( $post_type, [
		'theme',
		'network',
		'blog',
		'forum',
		'article',
		'course',
		'event',
		'case-study',
		'organisation',
		'solutions-portal'
	] ) ) {
		$is_mailed = true;
	}

	return $is_mailed;
}
