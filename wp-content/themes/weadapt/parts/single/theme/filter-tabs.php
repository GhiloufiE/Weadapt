<?php
/**
 * Single Theme Filter Tabs
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
		'id' => 'tab-forum',
		'controls' => 'tab-forum-panel',
		'selected' => false,
		'label' => __( 'Forum topics', 'weadapt' ),
	],
];


$activeTab = isset($_GET['tab']) ? 'tab-' . sanitize_text_field($_GET['tab']) : null;

foreach ($items as $key => $item) {
    if ($item['id'] === 'tab-forum') {
        $forumTab = array_splice($items, $key, 1);
        array_unshift($items, $forumTab[0]);
        break;
    }
}

if (get_the_ID() == 28392) {
    foreach ($items as $key => $item) {
        if ($item['id'] === 'tab-about') {
            $aboutTab = array_splice($items, $key, 1);
            array_splice($items, 1, 0, $aboutTab); 
            break;
        }
    }
}

$tabFound = false;

if ($activeTab) {
    foreach ($items as $key => $item) {

        if ($item['id'] === $activeTab) {
            $items[$key]['selected'] = true;
            $tabFound = true;
        } else {
            $items[$key]['selected'] = false;
        }
    }
}

if (!$tabFound) {
    foreach ($items as $key => $item) {
        if ($item['id'] === 'tab-forum') {
            $items[$key]['selected'] = true;
        } else {
            $items[$key]['selected'] = false;  
        }
    }
}

get_part( 'components/single-tabs-nav/index', ['items' => $items] );