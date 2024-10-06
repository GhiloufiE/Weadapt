<?php
/**
 * Archive Blog Aside
 *
 * @package WeAdapt
 */
$user_ID    = get_the_ID();
 
$organisations            = get_field('organisations', 'user_' . $user_ID);
$cpt_widget_args          = [
	'title' => __( 'Organisations', 'weadapt' ),
	'cpt_IDs' => $organisations,
	'buttons' => [ 'permalink' ]
];
get_part('components/cpt-widget/index', $cpt_widget_args);
