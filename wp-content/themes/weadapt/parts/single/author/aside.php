<?php
/**
 * Single Theme aside
 *
 * @package WeAdapt
 */

$user_ID    = ! empty( $args['user_ID'] ) ? intval( $args['user_ID'] ) : 0;
// $is_profile = ! empty( $args['is_profile'] ) ? wp_validate_boolean( $args['is_profile'] ) : false;

// get_part('components/info-widget-user/index', [
// 	'user_ID'           => $user_ID,
// 	'show_share_button' => $is_profile
// ]);

$organisations            = get_field('organisations', 'user_' . $user_ID);
$organisation_template_ID = get_page_id_by_template( 'organisation' );
$cpt_widget_args          = [
	'title' => __( 'Organisations', 'weadapt' ),
	'cpt_IDs' => $organisations,
	'buttons' => [ 'permalink' ]
];

if ( ! empty( $organisation_template_ID ) ) {
	$cpt_widget_args['more_link'] = [get_permalink( $organisation_template_ID ), __( 'View all Organisations', 'weadapt' )];
}
get_part('components/cpt-widget/index', $cpt_widget_args);