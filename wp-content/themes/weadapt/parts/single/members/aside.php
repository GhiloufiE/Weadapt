<?php
/**
 * Archive Blog Aside
 *
 * @package WeAdapt
 */
$user_ID    = get_the_ID();
 
$organisations            = get_field('organisations', 'user_' . $user_ID);
error_log(print_r($organisations, true));
$cpt_widget_args          = [
	'title' => __( 'Organisations', 'weadapt' ),
	'cpt_IDs' => $organisations,
	'buttons' => [ 'permalink' ]
];
get_part('components/cpt-widget/index', $cpt_widget_args);
