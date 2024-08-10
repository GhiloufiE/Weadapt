<?php
/**
 * Get views count
 *
 * @param  int        $post_ID
 * @param  boolean    $abridged
 * @param  boolean    $with_title
 * @return int|string Count views
 */

function get_post_views( $post_ID = false, $abridged = true, $with_title = true ) {
    global $post;

    if ( ! $post_ID && is_object( $post ) ) {
        $post_ID = $post->ID;
    }

    if ( ! $post_ID ) {
        return 0;
    }

    // Get view count
    $view_count = intval( get_post_meta( $post_ID, '_views_count', true ) );

    // Abriged count
    $output = $abridged ? abridged_number( $view_count ) : $view_count;

    // With Title count
    if ( $with_title ) {
        $output = sprintf( _n( '%s View', '%s Views', $view_count, 'weadapt' ), $output );
    }

    return $output;
}



/**
 * Set views count
 *
 * @param  int  $post_ID
 * @return bool Result of operation
 */
function set_post_views( $post_ID ) {
    global $post;

    if ( ! $post_ID && is_object( $post ) ) {
        $post_ID = $post->ID;
    }

    if ( ! $post_ID ) {
        return;
    }

    $user_ip = get_user_ip();
    $view_ip = get_post_meta( $post_ID, '_view_ip', true );

    if ( ! is_array( $view_ip ) ) {
        $view_ip = array(); // Default to an empty array if not an array
    }

    if ( ! array_key_exists( $user_ip, $view_ip ) ) {
        $view_ip[$user_ip] = time();
        
        // Increment views count
        $current_view_count = intval( get_post_meta( $post_ID, '_views_count', true ) );
        $new_view_count = $current_view_count + 1;
        update_post_meta( $post_ID, '_views_count', $new_view_count );
        
        // Update view IPs
        update_post_meta( $post_ID, '_view_ip', $view_ip );
    }
}

/**
 * template_redirect | process_post_views()
 *
 * Process post views
 */
function process_post_views() {
    global $post;

    $options = apply_filters( 'weadapt_views_options', array(
        'admin_views' => true,
        'cookie'      => true
    ) );

    $current_user = wp_get_current_user();

    if ( is_object( $post ) ) {
        $post_ID = $post->ID;
    }

    // Check if is post
    if ( empty( $post_ID ) ) {
        return;
    }

    if ( ! wp_is_post_revision( $post_ID ) ) {
        if ( is_singular( ['organisation', 'theme', 'forum'] ) ) {

            // Admin views
            if ( $options['admin_views'] === false ) {
                if ( $current_user->has_cap( 'administrator' ) ) {
                    return;
                }
            }

            // Cookie Defense
            if ( $options['cookie'] == true ) {
                if ( ! empty( $_COOKIE[ USER_COOKIE . '_views' ] ) ) {
                    $views_cookie = $_COOKIE[ USER_COOKIE . '_views' ];
                }

                if ( ! empty( $views_cookie ) ) {
                    $viewed = array_map( 'intval', explode( ',', $views_cookie ) );
                }
                else {
                    $viewed = array();
                }

                if ( !empty( $views_cookie ) && in_array( $post->ID, $viewed ) ) {
                    return;
                }

                $viewed[] = $post_ID;

                setcookie(
                    USER_COOKIE . '_views',
                    implode( ',', $viewed ),
                    [
                        'expires'  => strtotime( "+1 month" ),
                        'path'     => COOKIEPATH,
                        'domain'   => COOKIE_DOMAIN,
                        'secure'   => is_ssl(),
                        'httponly' => true,
                        'samesite' => 'Strict'
                    ]
                );
            }

            // Set views
            set_post_views( $post_ID );
        }
    }
}
add_action( 'template_redirect', 'process_post_views', 200 );
remove_action( 'init', 'adjacent_posts_rel_link_wp_head', 10, 0 ); // To keep the count accurate, lets get rid of prefetching
