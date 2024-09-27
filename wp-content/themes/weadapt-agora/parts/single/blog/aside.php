<?php
/**
 * Single Blog Aside
 *
 * @package WeAdapt
 */

$relevant         = get_field( 'relevant' );
$relevant_post_ID = get_field( 'relevant_main_theme_network' ) ? get_field( 'relevant_main_theme_network' ) : 0;

$organisations    = ! empty( $relevant['organizations'] ) ? $relevant['organizations'] : [];
$theme_network    = ! empty( $relevant['themes_networks'] ) ? $relevant['themes_networks'] : [];

$people = get_field( 'people' );

$contributors_IDs = ! empty( $people['contributors'] ) ? $people['contributors'] : [];

get_part('components/featured-resource/index');

if ( ! empty( $contributors_IDs ) ) {
	?><h2 class="featured-resource__title widget-title">Contributors</h2><?php
	foreach ( $contributors_IDs as $contributors_ID ) {
		get_part('components/info-widget-user/index', [
			'user_ID' => $contributors_ID,
		]);
	}
}

if ( ! empty( $relevant_post_ID ) ) {
	get_part('components/cpt-widget/index',
		[
			'title'   => __( 'Article Theme', 'weadapt' ),
			'cpt_IDs' => $relevant_post_ID,
			'buttons' => [ 'join', 'share' ]
		]
	);
}

$organisation_template_ID = get_page_id_by_template( 'organisation' );
$cpt_widget_args = [
	'title' => __( 'Contributing organisations', 'weadapt' ),
	'cpt_IDs' => $organisations,
	'buttons' => [ 'contact' ]
];

if ( ! empty( $organisation_template_ID ) ) {
	$cpt_widget_args['more_link'] = [get_permalink( $organisation_template_ID ), __( 'View all Organisations', 'weadapt' )];
}

if ( ! empty( $organisations ) ) {
	get_part('components/cpt-widget/index', $cpt_widget_args);
}

if ( ! empty( $theme_network ) ) {
	get_part('components/cpt-widget/index', [
		'title'     => __( 'Related Themes & Networks', 'weadapt' ),
		'cpt_IDs'   => $theme_network,
		'buttons'   => [ 'join', 'share' ]
	]);
}