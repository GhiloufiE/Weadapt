<?php
/**
 * Single Network Filter Tabs
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

if ( is_user_logged_in() ) {
	$items = array_merge( $items, [
		[
			'id'       => 'tab-followed',
			'controls' => 'tab-followed-panel',
			'selected' => false,
			'label'    => __( 'Followed', 'weadapt' )
		]
	] );
}

get_part( 'components/single-tabs-nav/index', ['items' => $items] );