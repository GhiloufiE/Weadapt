<?php

/**
 * Restricting access to the administration panel
 */
add_filter( 'wpcf7_map_meta_cap', function( $meta_caps ) {
	$meta_caps['wpcf7_edit_contact_form']    = 'manage_options';
	$meta_caps['wpcf7_edit_contact_forms']   = 'manage_options';
	$meta_caps['wpcf7_read_contact_form']    = 'manage_options';
	$meta_caps['wpcf7_read_contact_forms']   = 'manage_options';
	$meta_caps['wpcf7_delete_contact_form']  = 'manage_options';
	$meta_caps['wpcf7_delete_contact_forms'] = 'manage_options';

	return $meta_caps;
} );


/**
 * Add required html attr to required fields
 */
add_filter( 'wpcf7_form_elements', function( $content ) {
	$name = 'weadapt-required';

	$content = str_replace( $name . '"', $name . '" required="required"', $content );

	return $content;
} );


/**
 * Disable autop filter
 */
add_filter( 'wpcf7_autop_or_not', '__return_false' );


/**
 * Disable validate configuration
 */
add_filter( 'wpcf7_validate_configuration', '__return_false' );