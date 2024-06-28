<?php
/**
 * Archive Blog Aside
 *
 * @package WeAdapt
 */

// Latest organisations
$post_status   = current_user_can( 'administrator' ) ? ['publish', 'draft'] : ['publish'];
$organisations = new WP_Query( [
	'post_status'         => $post_status,
	'post_type'           => get_allowed_post_types( [ 'organisation' ] ),
	'fields'              => 'ids',
	'posts_per_page'      => 2,
	'ignore_sticky_posts' => true,
	'theme_query'         => true, // multisite fix
] );

$cpt_widget_args = [
	'title' => __( 'Latest organisations', 'weadapt' ),
	'cpt_IDs' => $organisations->posts,
	'buttons' => [ 'website', 'share' ]
];
if ( ! empty( $organisation_template_ID = get_page_id_by_template( 'organisation' ) ) ) {
	$cpt_widget_args['more_link'] = [get_permalink( $organisation_template_ID ), __( 'View all Organisations', 'weadapt' )];
}
get_part('components/cpt-widget/index', $cpt_widget_args);


// Latest contributors
$members_IDs = [];
$last_posts  = new WP_Query( [
	'post_status'         => 'publish',
	'post_type'           => get_allowed_post_types( [ 'blog', 'article', 'course', 'event', 'case-study' ] ),
	'fields'              => 'ids',
	'ignore_sticky_posts' => true,
	'theme_query'         => true, // multisite fix
] );

if ( ! empty( $last_posts->posts ) ) {
	foreach ( $last_posts->posts as $post_ID ) {
		$members_IDs = array_merge( $members_IDs, get_field( 'people_contributors', $post_ID ) );
	}
}

if ( ! empty( $members_IDs ) ) {
	foreach ( $members_IDs as $key => $user_ID ) {
		if ( empty( get_field( 'avatar', 'user_' . $user_ID ) ) ) {
			unset( $members_IDs[$key] );
		}
	}

	$members_IDs = array_unique( $members_IDs );
}

if ( count( $members_IDs ) > 4 ) {
	$members_IDs = array_slice( $members_IDs, 0, 4 );
}

get_part('components/members-widget/index', [
	'title'       => __( 'Latest contributors', 'weadapt' ),
	'members_IDs' => $members_IDs,
]);