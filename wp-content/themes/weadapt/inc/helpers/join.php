<?php

/**
 * Global Variables
 */
global $wpdb;
global $join_table;

$join_table = $wpdb->base_prefix . 'wa_join';


/**
 * Create wa_join Table
 */
if ( ! $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $join_table ) ) ) ) {
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$charset_collate = $wpdb->get_charset_collate();

	dbDelta("CREATE TABLE {$join_table} (
		`id`      BIGINT(20) unsigned NOT NULL auto_increment,
		`user_id` int NOT NULL,
		`join_id` int NOT NULL,
		`type`    VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
		`created` int NOT NULL default 0,
		PRIMARY KEY (id)
	) $charset_collate");
}


/**
 * The event handler Post Like
 *
 * for debuging use:
 * ob_start();
 * s($variable);
 * wp_die( json_encode( array( 'status' => 'error', 'message' => ob_get_clean() ) ) );
 */
function ajax_join() {
	if (
		empty( $_POST['nonce'] ) ||
		empty( $_POST['post_id'] ) ||
		empty( $_POST['type'] ) ||
		! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'join' )
	) {
		wp_die( json_encode( array(
			'status' => 'error',
			'message' => __( 'Busted!', 'weadapt' )
		) ) );
	}

	global $wpdb;
	global $join_table;

	$status  = 'error';
	$message = '';

	$post_ID   = intval( sanitize_text_field($_POST['post_id']));
	$user_ID   = get_current_user_id();
	$type      = sanitize_text_field($_POST['type']);
	$is_joined = wp_validate_boolean($_POST['is_joined']);

	// Processing
	if ( ! $is_joined ) {
		$exist_rows = $wpdb->get_results( $wpdb->prepare( "SELECT id FROM $join_table WHERE user_id = %d and join_id = %d and type = %s",
			$user_ID,
			$post_ID,
			$type
		) );

		if ( empty( $exist_rows ) ) {
			$wpdb->insert( $join_table, [
				'user_id' => $user_ID,
				'join_id' => $post_ID,
				'type'    => $type,
				'created' => time()
			] );
		}

		$exists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $join_table WHERE user_id = %d and join_id = %d and type = %s",
			$user_ID,
			$post_ID,
			$type
		) );

		if ( '1' === $exists ) {
			$status  = 'success';

			switch ( $type ) {
				case 'theme':
				case 'network':
					$message = __( 'Thanks for subscribing.', 'weadapt' );
					break;

				case 'user':
				case 'forums':
				case 'forum':
					$message = __( 'Thanks for following.', 'weadapt' );
					break;

				default:
					$message = __( 'Thanks for bookmarking.', 'weadapt' );
					break;
			}
		}
		else {
			$message = __( 'Oops! Something went wrong.', 'weadapt' );
		}
	}
	else {
		$exist_rows = $wpdb->get_results( $wpdb->prepare( "SELECT id FROM $join_table WHERE user_id = %d and join_id = %d and type = %s",
			$user_ID,
			$post_ID,
			$type
		) );

		if ( ! empty( $exist_rows ) ) {
			foreach ( $exist_rows as $row ) {
				$wpdb->delete( $join_table, [ 'id' => $row->id ] );
			}
		}

		$exists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $join_table WHERE user_id = %d and join_id = %d and type = %s",
			$user_ID,
			$post_ID,
			$type
		) );

		if ( '0' === $exists ) {
			$status  = 'success';

			switch ( $type ) {
				case 'theme':
				case 'network':
					$message = __( 'Unsubscribed successfully.', 'weadapt' );
					break;

				case 'user':
				case 'forums':
				case 'forum':
					$message = __( 'Unfollowed successfully.', 'weadapt' );
					break;

				default:
					$message = __( 'Unbookmarked successfully.', 'weadapt' );
					break;
			}
		}
		else {
			$message = __( 'Oops! Something went wrong.', 'weadapt' );
		}
	}

	$count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $join_table WHERE join_id = %d and type = %s",
		$post_ID,
		$type
	) );

	if ( 'user' === $type ) {
		$count_html = sprintf( _n( '%s Follower', '%s Followers', $count, 'weadapt' ), $count );
	}
	else {
		$count_html = sprintf( _n( '%s Member', '%s Members', $count, 'weadapt' ), $count );
	}

	do_action( 'theme_join', $user_ID, $post_ID, $type );

	wp_die( json_encode( array(
		'status'     => $status,
		'message'    => $message,
		'count_html' => $count_html
	) ) );
}
add_action( 'wp_ajax_ajax_join', 'ajax_join' );


if ( ! function_exists( 'is_user_joined' ) ) :

	/**
	 * Is user Joined
	 */
	function is_user_joined( $post_ID = 0, $type = '' ) {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		global $wpdb;
		global $join_table;

		if ( empty( $post_ID ) ) {
			global $post;

			$post_ID = $post->ID;
		}

		$type    = ! empty( $type ) ? $type : get_post_type( $post_ID );
		$user_ID = get_current_user_id();

		$exists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $join_table WHERE user_id = %d and join_id = %d and type = %s",
			$user_ID,
			$post_ID,
			$type
		) );

		return ( '0' === $exists ) ? false : true;
	}
endif;


if ( ! function_exists( 'get_members_count' ) ) :

	/**
	 * Get members count
	 */
	function get_members_count( $post_ID = 0, $type = '', $hide_if_empty = false ) {
		global $wpdb;
		global $join_table;

		if ( empty( $post_ID ) ) {
			global $post;

			$post_ID = $post->ID;
		}

		$type  = ! empty( $type ) ? $type : get_post_type( $post_ID );

		if ( 'organisation' === $type ) {
			$count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( $wpdb->users.ID ) as count
				FROM $wpdb->users
				LEFT JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id
				WHERE $wpdb->usermeta.meta_key = 'organisations' AND $wpdb->usermeta.meta_value LIKE '%:\"%d\"%'", $post_ID )
			);
		}
		else {
			$count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $join_table WHERE join_id = %d and type = %s",
				$post_ID,
				$type
			) );
		}

		if ( $count === 0 && $hide_if_empty ) {
			return '';
		}
		global $post;
		$post_id = $post->ID;
		if ( 'forums' === $type ) {
			$theme_id = get_field('relevant_main_theme_network', $post_id);
			$count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $join_table WHERE join_id = %d ", $theme_id ) );
			$count_html = $count; 
		}

		if ( 'user' === $type ) {
			$count_html = sprintf( _n( '%s Follower', '%s Followers', $count, 'weadapt' ), $count );
		}
		else {
			$count_html = sprintf( _n( '%s Member', '%s Members', $count, 'weadapt' ), $count );
		}

		return sprintf(
			'<span class="join-count" data-id="%d" data-type="%s">%s</span>',
			$post_ID,
			$type,
			$count_html 
		);
	}
endif;


if ( ! function_exists( 'get_followed_posts' ) ) :

	/**
	 * Get members count
	 */
	function get_followed_posts( $post_types, $user_ID = 0 ) {
		$ids = [];

		if ( is_user_logged_in() && ! empty( $post_types ) ) {
			if ( empty( $user_ID ) ) {
				$user_ID = get_current_user_id();
			}

			if ( is_array( $post_types ) ) {
				$cache_key = 'followed_' . implode( '_', $post_types ) . '_' . $user_ID;
			}
			else {
				$cache_key = 'followed_' . $post_types . '_' . $user_ID;
			}

			$ids = wp_cache_get( $cache_key, 'site-options' );

			if ( ! isset( $ids ) || false === $ids ) {
				global $wpdb;
				global $join_table;

				if ( is_array( $post_types ) ) {
					$conditions = [];

					foreach ( $post_types as $post_type ) {
						$conditions[] = $wpdb->prepare( "(user_id = %d AND type = %s)", $user_ID, $post_type );
					}

					$conditions_sql = implode( ' OR ', $conditions );

					$results = $wpdb->get_results("SELECT join_id FROM $join_table WHERE $conditions_sql ORDER BY created DESC");
				}
				else {
					$results = $wpdb->get_results( $wpdb->prepare( "SELECT join_id FROM $join_table WHERE user_id = %d and type = %s ORDER BY created DESC",
						$user_ID,
						$post_types
					) );
				}

				$ids = array_map( 'intval', wp_list_pluck( $results, 'join_id' ) );

				wp_cache_set( $cache_key, $ids, 'site-options' );
			}
		}

		return $ids;
	}
endif;


if ( ! function_exists( 'get_followed_users' ) ) :

	/**
	 * Get members count
	 */
	function get_followed_users( $join_ID = 0, $post_type = '' ) {
		$ids = [];

		if ( ! empty( $join_ID ) && ! empty( $post_type ) ) {
			$cache_key = 'followed_users_' . $post_type . '_' . $join_ID;
			$ids       = wp_cache_get( $cache_key, 'site-options' );

			if ( ! isset( $ids ) || false === $ids ) {
				global $wpdb;
				global $join_table;

				$results = $wpdb->get_results( $wpdb->prepare( "SELECT user_id FROM $join_table WHERE join_id = %d and type = %s ORDER BY created DESC",
					$join_ID,
					$post_type
				) );

				$ids = array_map( 'intval', wp_list_pluck( $results, 'user_id' ) );

				wp_cache_set( $cache_key, $ids, 'site-options' );
			}
		}

		return $ids;
	}
endif;