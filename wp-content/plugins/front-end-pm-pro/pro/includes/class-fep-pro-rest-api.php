<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Fep_Pro_REST_API {

	private static $instance;

	public static function init() {
		if ( ! self::$instance instanceof self ) {
			self::$instance = new self();
		}
			return self::$instance;
	}

	function actions_filters() {
			add_filter( 'fep_filter_rest_users_response_group', array( $this, 'group_members' ), 10, 4 );
	}

	function group_members( $response, $args, $q, $x ) {
		$args  = apply_filters( 'fep_group_members_suggestion_arguments', $args );
		$roles = wp_roles()->roles;

		if ( false !== strpos( $q, '{role}-' ) ) {
			$search_role = str_replace( '{role}-', '', $q );

			foreach ( $roles as $key => $role ) {
				if ( ! $search_role ) {
					$response[] = array(
						'id'   => '{role}-' . $key,
						'name' => '{role}-' . esc_js( $role['name'] ),
					);
				} elseif ( false !== strpos( $key, $search_role ) || false !== strpos( $role['name'], $search_role ) ) {
					$response[] = array(
						'id'   => '{role}-' . $key,
						'name' => '{role}-' . esc_js( $role['name'] ),
					);
				}
			}
		} elseif ( false !== strpos( $q, '{role-unparsed}-' ) ) {
			$search_role = str_replace( '{role-unparsed}-', '', $q );

			foreach ( $roles as $key => $role ) {
				if ( ! $search_role ) {
					$response[] = array(
						'id'   => '{role-unparsed}-' . $key,
						'name' => '{role-unparsed}-' . esc_js( $role['name'] ),
					);
				} elseif ( false !== strpos( $key, $search_role ) || false !== strpos( $role['name'], $search_role ) ) {
					$response[] = array(
						'id'   => '{role-unparsed}-' . $key,
						'name' => '{role-unparsed}-' . esc_js( $role['name'] ),
					);
				}
			}
		} else {
			// The Query
			$users = get_users( $args );

			foreach ( $users as $user ) {
				$response[] = array(
					'id'   => $user->ID,
					'name' => fep_user_name( $user->ID ),
				);
			}
		}
		return $response;
	}
} //END CLASS

add_action( 'init', array( Fep_Pro_REST_API::init(), 'actions_filters' ) );

