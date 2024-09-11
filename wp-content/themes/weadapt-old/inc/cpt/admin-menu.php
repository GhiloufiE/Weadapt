<?php

/**
 * Admin Menu
 */
add_action( 'admin_menu', function() {
	global $menu;

	ksort( $menu );

	// Change Dashboard Position
	if (
		! empty( $menu[1] ) &&
		! empty( $menu[2][1] ) &&
		$menu[2][1] == 'read' )
	{
		$read_menu = $menu[2];

		$menu[2] = $menu[1];
		$menu[1] = $read_menu;
	}

	// Change Media Position
	if (
		empty( $menu[19] ) &&
		! empty( $menu[10][1] ) &&
		$menu[10][1] == 'upload_files' )
	{
		$menu[19] = $menu[10];
		unset( $menu[10] );
	}

	// Hide ACF & Network Setting
	if ( get_current_blog_id() != 1 ) {
		foreach ( $menu as $key => $item ) {
			if (
				! empty( $item[2] ) &&
				in_array( $item[2], [
					'network-general-settings',
					'edit.php?post_type=acf-field-group'
				] )
			) {
				unset( $menu[$key] );
			}
		}
	}
}, 100 );