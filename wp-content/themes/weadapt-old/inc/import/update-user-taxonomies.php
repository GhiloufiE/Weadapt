<?php

/*

Users:
http://weadapt/web/?import=update-user-taxonomies&key=013b0f890d204a522a7e462d1dfa93e5


mike-dev
85dCXpavMK!zyZAQ

*/

if (
	is_admin() ||
	! isset( $_GET['import'] ) ||
	$_GET['import'] != 'update-user-taxonomies' ||
	! isset( $_GET['key'] ) ||
	$_GET['key'] != '013b0f890d204a522a7e462d1dfa93e5'
) return;


add_action( 'init', function() {
	$args = array(
		'fields'  => 'ID'
	);
	$users = get_users( $args );

	if ( ! empty( $users ) ) {
		foreach ( $users as $user_ID ) {
			foreach ( [
				'interest' => 'field_6437bc05e1834',
				'role'     => 'field_642fdf6638172'
			] as $taxonomy => $field_key ) {
				$terms = get_field( $taxonomy, 'user_' . $user_ID) ? get_field( $taxonomy, 'user_' . $user_ID) : [];

				update_field( $field_key, array_map('strval', $terms), 'user_' . $user_ID );
			}
		}
	}

	die();
} );