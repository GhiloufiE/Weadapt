<?php

/**
 * Get Badge ID
 */
if ( ! function_exists( 'get_badge_id' ) ) :

	function get_badge_id( $slug ) {
		$term = get_term_by( 'slug', $slug, 'badge' );

		return ! empty( $term->term_id ) ? intval( $term->term_id ) : 0;
	}

endif;


/**
 * Set Badge ID
 */
if ( ! function_exists( 'set_badge_id' ) ) :

	function set_badge_id( $user_ID, $badge_ID, $force_update = false ) {
		$badges = get_field( 'badges', 'user_' . $user_ID );

		if ( empty( $badges ) ) {
			$badges = [];
		}

		$badges = array_unique( array_merge( $badges, [$badge_ID] ) );

		if ( $force_update && ! empty( $badges ) ) {
			$temp_badges = [];

			foreach ( $badges as $temp_ID ) {
				$term = get_term_by( 'ID', $temp_ID, 'badge' );

				if ( ! empty( $term ) ) {
					$temp_badges[] = $term->term_id;
				}
			}

			$badges = $temp_badges;
		}

		update_field( 'badges', $badges, 'user_' . $user_ID );
	}

endif;


/**
 * Delete Badge ID
 */
if ( ! function_exists( 'delete_badge_id' ) ) :

	function delete_badge_id( $user_ID, $badge_ID, $force_update = false ) {
		$badges = get_field( 'badges', 'user_' . $user_ID );

		if ( empty( $badges ) ) {
			$badges = [];
		}

		if ( in_array( $badge_ID, $badges ) ) {
			$badges = array_diff( $badges, [$badge_ID]);
		}

		if ( $force_update && ! empty( $badges ) ) {
			$temp_badges = [];

			foreach ( $badges as $temp_ID ) {
				$term = get_term_by( 'ID', $temp_ID, 'badge' );

				if ( ! empty( $term ) ) {
					$temp_badges[] = $term->term_id;
				}
			}

			$badges = $temp_badges;
		}

		update_field( 'badges', $badges, 'user_' . $user_ID );
	}

endif;


/**
 * Get User Badges
 */
if ( ! function_exists( 'get_user_badges' ) ) :

	function get_user_badges( $user_ID ) {
		$badges = get_field( 'badges', 'user_' . $user_ID );
		$count  = ( is_array( $badges ) ) ? count( $badges ) : 0;

		if ( $count > 0 ) {
			return sprintf( _n( '%s badge', '%s badges', $count, 'weadapt' ), $count );
		}
		else {
			return false;
		}
	}

endif;