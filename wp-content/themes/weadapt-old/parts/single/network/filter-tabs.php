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
        'selected' => false,
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

$activeTab = isset($_GET['tab']) ? 'tab-' . sanitize_text_field($_GET['tab']) : null;

// Always move "About" tab to the beginning if the ID is 28392
if (get_the_ID() == 28392) {
    foreach ($items as $key => $item) {
        if ($item['id'] === 'tab-about') {
            $aboutTab = array_splice($items, $key, 1);
            array_unshift($items, $aboutTab[0]);
            break;
        }
    }
}

// Set the selected tab based on the URL parameter
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

// If no tab is found from the URL, and the ID is 28392, ensure "About" is selected
if (!$tabFound && get_the_ID() == 28392) {
    $items[0]['selected'] = true;
}

// If no URL parameter and ID is not 28392, default to the first tab being selected
if (!$tabFound && get_the_ID() != 28392) {
    $items[0]['selected'] = true;
}

get_part('components/single-tabs-nav/index', ['items' => $items]);
?>
