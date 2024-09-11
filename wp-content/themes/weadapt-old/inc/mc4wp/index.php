<?php

/**
 * Multisite fix | Replace Lists maichimp form subscribes to
 */
add_filter( 'mc4wp_form_lists', function( $lists ) {
	switch ( get_current_blog_id() ) {
		case 1:
			$lists = ['be94db1743']; // weAdapt
			break;

		case 2:
			$lists = ['a5040a96dc']; // Adaptation at Altitude
			break;

		case 2:
			$lists = ['6493584bbb']; // Adaptation Without Borders
			break;


	}

	return $lists;
} );