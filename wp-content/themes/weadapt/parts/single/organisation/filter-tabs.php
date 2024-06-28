<?php
/**
 * Single Organisation Filter Tabs
 *
 * @package WeAdapt
 */

$items = [
	[
		'id' => 'tab-about',
		'controls' => 'tab-about-panel',
		'selected' => true,
		'label' => __( 'About', 'weadapt' ),
	],
	[
		'id' => 'tab-latest',
		'controls' => 'tab-latest-panel',
		'selected' => false,
		'label' => __( 'Latest', 'weadapt' ),
	],
	[
		'id' => 'tab-members',
		'controls' => 'tab-members-panel',
		'selected' => false,
		'label' => __( 'Members', 'weadapt' ),
	],
	// temp-hide organisation resources content
	// [
	// 	'id' => 'tab-resources',
	// 	'controls' => 'tab-resources-panel',
	// 	'selected' => false,
	// 	'label' => __( 'Resources ', 'weadapt' ),
	// ],
];

get_part( 'components/single-tabs-nav/index', ['items' => $items] );