<?php

/**
 * Admin Publish To Column
 */
function publish_to_posts_column( $columns ) {
	$num = 2;

	$new_columns = array(
		'publish_to' => __( 'Publish To', 'weadapt' ),
	);

	return array_slice( $columns, 0, $num ) + $new_columns + array_slice( $columns, $num );
}


/**
 * Admin Theme Column
 */
function theme_posts_column( $columns ) {
	$num = 3;

	$new_columns = array(
		'theme' => __( 'Theme/Network', 'weadapt' ),
	);

	return array_slice( $columns, 0, $num ) + $new_columns + array_slice( $columns, $num );
}


/**
 * Admin Users Column
 */
function users_posts_column( $columns ) {
	$num = 3;

	$new_columns = array(
		'users' => __( 'Users', 'weadapt' ),
	);

	return array_slice( $columns, 0, $num ) + $new_columns + array_slice( $columns, $num );
}





/**
 * Admin Columns Content
 */
function theme_posts_column_content( $colname, $post_ID ) {
	switch ( $colname ) {
		case 'publish_to':
			$publish_to = get_field( 'publish_to', $post_ID );

			if ( empty( $publish_to ) ) {
				echo '—';
			}
			else {
				$blog_names = [];

				foreach ( $publish_to as $blog_ID ) {
					$blog_details = get_blog_details( $blog_ID );

					if ( isset( $blog_details->blogname ) ) {
						$blog_names[] = get_blog_details( $blog_ID )->blogname;
					}
				}

				echo implode( ', ', $blog_names );
			}
			break;

		case 'theme':
			$main_theme_network = get_field( 'relevant_main_theme_network', $post_ID );

			if ( empty( $main_theme_network ) ) {
				echo '—';
			}
			else {
				echo get_the_title( $main_theme_network );
			}
			break;

		case 'users':
			$user_query = new WP_User_Query( [
				'meta_query' => [ [
					'key'      => 'organisations',
					'value'    => sprintf( ':"%d";', $post_ID ),
					'compare'  => 'LIKE'
				] ],
				'number' => -1,
			] );

			if ( empty( $user_query->results ) ) {
				echo '—';
			}
			else {
				$users = [];

				foreach ( $user_query->results as $user ) {
					$users[] = sprintf( '<a href="%s" target="_blank">%s</a>',
						get_edit_user_link( $user->ID ),
						esc_html( $user->data->display_name )
					);
				}

				echo implode( ', ', $users );
			}
			break;

		case 'topics':
			echo get_post_meta_count( $post_ID, ['forum'], '', '' );
			break;
	}
}


$theme_network_post_types = get_theme_network_post_types();
$theme_network_post_types[] = 'page';

if ( ! empty( $theme_network_post_types ) ) {
	foreach ( $theme_network_post_types as $post_type ) {
		add_filter( "manage_{$post_type}_posts_columns", 'publish_to_posts_column' );
		add_action( "manage_{$post_type}_posts_custom_column", 'theme_posts_column_content', 10, 2 );
	}
}

foreach ( [ 'blog', 'article', 'course', 'event', 'case-study' ] as $post_type ) {
	add_filter( "manage_{$post_type}_posts_columns", 'theme_posts_column' );
}

add_filter( "manage_organisation_posts_columns", 'users_posts_column' );