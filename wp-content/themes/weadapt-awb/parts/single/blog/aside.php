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
?>
<div class="featured-desktop"><?php
get_part('components/featured-resource/index');?></div><?php
if ( ! empty( $contributors_IDs ) ) {
	?><h2 class="featured-resource__title widget-title">Contributors</h2><?php
	foreach ( $contributors_IDs as $contributors_ID ) {
		get_part('components/info-widget-user/index', [
			'user_ID' => $contributors_ID,
		]);
	}
}



get_part('components/cpt-widget/index',
	[
		'title'   => __( 'Article Theme', 'weadapt' ),
		'cpt_IDs' => $relevant_post_ID,
		'buttons' => [ 'join', 'share' ]
	]
);

$organisation_template_ID = get_page_id_by_template( 'organisation' );
$cpt_widget_args = [
	'title' => __( 'Contributing organisations', 'weadapt' ),
	'cpt_IDs' => $organisations,
	'buttons' => [ 'contact' ]
];
if ( ! empty( $organisation_template_ID ) ) {
	$cpt_widget_args['more_link'] = [get_permalink( $organisation_template_ID ), __( 'View all Organisations', 'weadapt' )];
}
get_part('components/cpt-widget/index', $cpt_widget_args);

get_part('components/cpt-widget/index', [
	'title'     => __( 'Related Themes & Networks', 'weadapt' ),
	'cpt_IDs'   => $theme_network,
	'buttons'   => [ 'join', 'share' ]
]);
?>

<style>
@media only screen and (min-width: 1024px) {
    .featured-desktop {
        display: block;
    }
}

@media only screen and (max-width: 1023px) {
    .featured-desktop {
        display: none;
    }
}
</style>