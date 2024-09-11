<?php
/**
 * Edit Profile Filter Tabs
 *
 * @package WeAdapt
 */

$items = [
	[
		'id' => 'tab-account-settings',
		'controls' => 'tab-account-settings-panel',
		'selected' => true,
		'label' => __( 'Account settings', 'weadapt' ),
	],
	[
		'id' => 'tab-tell-us-more',
		'controls' => 'tab-tell-us-more-panel',
		'selected' => false,
		'label' => __( 'Tell us more about you', 'weadapt' ),
	],
	[
		'id' => 'tab-personal-details',
		'controls' => 'tab-personal-details-panel',
		'selected' => false,
		'label' => __( 'Personal details', 'weadapt' ),
	],
];

get_part( 'components/single-tabs-nav/index', ['items' => $items] );