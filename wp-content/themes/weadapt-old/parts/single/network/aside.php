<?php
/**
 * Single Network Aside
 *
 * @package WeAdapt
 */

get_part('components/info-widget-cpt/index', [
	'cpt_ID'      => get_the_ID(),
	'cpt_buttons' => ['join', 'share']
]);

if ( ! empty( get_allowed_post_types( [ 'forums' ] ) ) ) {
	get_part('components/cta-widget/index', [
		'template' => 'join_the_conversation'
	]);
}

get_part('components/tags/index', ['title' => __( 'Tags', 'weadapt' )]);

$theme_network      = get_field( 'relevant_themes_networks' );
$networks_template_ID = get_page_id_by_template( 'network' );

$cpt_widget_args = [
	'title'     => __( 'Related Themes & Networks', 'weadapt' ),
	'cpt_IDs'   => $theme_network,
	'buttons'   => [ 'join', 'share' ]
];

if ( ! empty( $networks_template_ID ) ) {
	$cpt_widget_args['more_link'] = [get_permalink( $networks_template_ID ), __( 'View all networks', 'weadapt' )];
}

get_part('components/cpt-widget/index', $cpt_widget_args);