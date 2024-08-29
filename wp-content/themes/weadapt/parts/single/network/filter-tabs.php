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
        'label' => __('Latest', 'weadapt'),
    ],
    [
        'id' => 'tab-about',
        'controls' => 'tab-about-panel',
        'selected' => false,
        'label' => __('About', 'weadapt'),
    ],
    [
        'id' => 'tab-editors',
        'controls' => 'tab-editors-panel',
        'selected' => false,
        'label' => __('Editors', 'weadapt'),
    ],
    [
        'id' => 'tab-members',
        'controls' => 'tab-members-panel',
        'selected' => false,
        'label' => __('Members', 'weadapt'),
    ],
    [
        'id' => 'tab-organisations',
        'controls' => 'tab-organisations-panel',
        'selected' => false,
        'label' => __('Organisations', 'weadapt'),
    ],
    [
        'id' => 'network-forum',
        'controls' => 'tab-forum-panel',
        'selected' => false,
        'label' => __('Forum topics', 'weadapt'),
    ],
];

if (get_the_ID() == 28392 && count($items) > 1) {
     foreach ($items as $key => $item) {
        if ($item['id'] === 'tab-about') {
             $items[$key]['selected'] = true;
             $aboutTab = array_splice($items, $key, 1);
            array_unshift($items, $aboutTab[0]);
            break;
        }
    }
     $items[1]['selected'] = false;
}

get_part('components/single-tabs-nav/index', ['items' => $items]);
