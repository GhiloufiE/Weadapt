<?php
/**
 * Single Blog Filter Tabs
 *
 * @package WeAdapt
 */

 $items = [
	[
		'id' => 'tab-latest',
		'controls' => 'tab-latest-panel',
		'selected' => true,
		'label' => __( 'Latest', 'weadapt' ),
	],
	[
		'id' => 'tab-about',
		'controls' => 'tab-about-panel',
		'selected' => false,
		'label' => __( 'About', 'weadapt' ),
	],
	[
		'id' => 'tab-members',
		'controls' => 'tab-members-panel',
		'selected' => false,
		'label' => __( 'Members', 'weadapt' ),
	],
];

get_part( 'components/single-tabs-nav/index', ['items' => $items] );