<?php
/**
 * Single Network Filter Tabs
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
		'id' => 'tab-editors',
		'controls' => 'tab-editors-panel',
		'selected' => false,
		'label' => __( 'Editors', 'weadapt' ),
	],
	[
		'id' => 'tab-members',
		'controls' => 'tab-members-panel',
		'selected' => false,
		'label' => __( 'Members', 'weadapt' ),
	],
	[
		'id' => 'tab-organisations',
		'controls' => 'tab-organisations-panel',
		'selected' => false,
		'label' => __( 'Organisations', 'weadapt' ),
	],
];

get_part( 'components/single-tabs-nav/index', ['items' => $items] );