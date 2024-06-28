<?php
/**
 * Single Organisation Filter Tabs
 *
 * @package WeAdapt
 */

$items = [
	[
		'id'       => 'tab-trending',
		'controls' => 'tab-trending-panel',
		'selected' => true,
		'label'    => __( 'Trending', 'weadapt' ),
	],
];

get_part( 'components/single-tabs-nav/index', ['items' => $items] );