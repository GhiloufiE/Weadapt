<?php

if ( ! is_multisite() )
	return;


/**
 * Select | Forum
 */
add_filter( 'acf/load_field/name=forum', function( $field ) {
	if ( isset( $_GET['selected-forum'] ) && ! empty( intval( $_GET['selected-forum'] ) ) ) {
		$field['value'] = intval( $_GET['selected-forum'] );
	}

	return $field;
} );