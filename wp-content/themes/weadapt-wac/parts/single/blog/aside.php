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
if ( get_field( 'document_list' )[0] ) {
    echo  '<hr>';
    get_part('components/featured-resource/index');
}
if ( ! empty( $contributors_IDs ) ) {
	?><h2 class="featured-resource__title widget-title">Contributors</h2><?php
	foreach ( $contributors_IDs as $contributors_ID ) {
		get_part('components/info-widget-user/index', [
			'user_ID' => $contributors_ID,
		]);
	}
}

get_part('components/tags/index', ['title' => __( 'Tags', 'weadapt' )]);



if ( $relevant_post_ID ) {
    echo  '<hr>';
    get_part('components/cpt-widget/index',
    	[
    		'title'   => __( 'Network', 'weadapt' ),
    		'cpt_IDs' => $relevant_post_ID,
    		'buttons' => [ 'find-out-more' ]
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
get_part('components/cpt-widget/index', $cpt_widget_args);

echo  '<hr>';

get_part('components/cpt-widget/index', [
	'title'     => __( 'Trending Discussions', 'weadapt' ),
	'cpt_IDs'   => $theme_network,
	'buttons'   => [ 'find-out-more' ],
	'colored_bg' => true
]);

