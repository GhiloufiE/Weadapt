<?php
/**
 * Archive Theme Aside
 *
 * @package WeAdapt
 */
?>

<?php

get_part( 'components/tags/index', ['title' => __( 'Trending tags', 'weadapt' )] );

// Popular themes
$themes = new WP_Query( [
	'post_status'         => 'publish',
	'post_type'           => get_allowed_post_types( [ 'theme' ] ),
	'fields'              => 'ids',
	'meta_key'            => '_views_count',
	'orderby'             => 'meta_value_num',
	'posts_per_page'      => 3,
	'ignore_sticky_posts' => true,
	'theme_query'         => true, // multisite fix
] );

$cpt_widget_args = [
	'title' => __( 'Popular themes', 'weadapt' ),
	'cpt_IDs' => $themes->posts,
	'buttons' => [ 'join', 'share' ]
];
if ( ! empty( $themes_template_ID = get_page_id_by_template( 'theme' ) ) ) {
	$cpt_widget_args['more_link'] = [get_permalink( $themes_template_ID ), __( 'View all themes', 'weadapt' )];
}
get_part('components/cpt-widget/index', $cpt_widget_args);

if ( is_user_logged_in() ) {
	get_part('components/members-widget/index', [
		'title'     => __( 'Users you follow', 'weadapt' ),
		'more_link' => [get_page_id_by_template( 'people' ), __( 'View all users', 'weadapt' )],
		'members_IDs'   => get_followed_users( get_current_user_id(), 'user' ),
	]);
}

get_part('components/cta-widget/index', [
	'template' => 'need_help'
]);