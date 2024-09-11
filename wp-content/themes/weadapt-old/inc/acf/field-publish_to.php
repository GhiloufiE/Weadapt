<?php

if ( ! is_multisite() )
	return;


/**
 * Checkbox | Publish to
 */
add_filter( 'acf/load_field/name=publish_to', function( $field ) {
	$choices = [];

	foreach ( get_sites() as $key => $site ) {
		$choices[$site->blog_id] = get_blog_details( $site->blog_id )->blogname;
	}

	if ( ! empty( $choices ) ) {
		$field['choices'] = $choices;
	}

	return $field;
} );