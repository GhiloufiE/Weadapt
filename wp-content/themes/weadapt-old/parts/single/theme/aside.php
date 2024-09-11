<?php
/**
 * Single Theme Aside
 *
 * @package WeAdapt
 */

get_part('components/info-widget-cpt/index', [
	'cpt_ID'      => get_the_ID(),
	'cpt_buttons' => ['join', 'share']
]);

if ( ! empty( get_allowed_post_types( [ 'forums' ] ) ) ) {
	get_part('components/discussions-widget/index');

	get_part('components/cta-widget/index', [
		'template' => 'contribute_now'
	]);
}

get_part('components/tags/index', ['title' => __( 'Tags', 'weadapt' )]);

$theme_network      = get_field( 'relevant_themes_networks' );
$themes_template_ID = get_page_id_by_template( 'theme' );

$cpt_widget_args = [
	'title'     => __( 'Related Themes & Networks', 'weadapt' ),
	'cpt_IDs'   => $theme_network,
	'buttons'   => [ 'join', 'share' ]
];

if ( ! empty( $themes_template_ID ) ) {
	$cpt_widget_args['more_link'] = [get_permalink( $themes_template_ID ), __( 'View all themes', 'weadapt' )];
}

get_part('components/cpt-widget/index', $cpt_widget_args);