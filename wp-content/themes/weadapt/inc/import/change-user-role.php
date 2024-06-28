<?php

/*

Users:
http://weadapt/web/?import=change-user-roles&key=013b0f890d204a522a7e462d1dfa93e5


mike-dev
85dCXpavMK!zyZAQ

*/

if (
	is_admin() ||
	! isset( $_GET['import'] ) ||
	$_GET['import'] != 'change-user-roles' ||
	! isset( $_GET['key'] ) ||
	$_GET['key'] != '013b0f890d204a522a7e462d1dfa93e5'
) return;


add_action( 'init', function() {
	$args = array(
		'role'    => 'subscriber',
		'fields'  => 'ID'
	);
	$users = get_users( $args );

	if ( ! empty( $users ) ) {
		foreach ( $users as $user_ID ) {
			$user = new WP_User( $user_ID );

			// Remove subscriber
			$user->remove_role( 'subscriber' );

			// Add contributor
			$user->add_role( 'contributor' );
		}
	}

	die();
} );