<?php
/**
 * Single Blog Breadcrumbs
 *
 * @package WeAdapt
 */

$breadcrumbs = [];
$current_blog_id = get_current_blog_id();
$main_blog_id = 1; // Assuming 1 is the ID of the main site

// Switching to the main site context to get the correct pages
switch_to_blog($main_blog_id);

foreach (['learn', get_post_type()] as $template_name) {
    if (!empty($page_ID = get_page_id_by_template($template_name))) {
        $breadcrumbs[] = [
            'url' => get_permalink($page_ID),
            'label' => get_the_title($page_ID)
        ];
    }
}

// Switch back to the current site context
restore_current_blog();

$breadcrumbs[] = ['url' => '', 'label' => get_the_title()];

get_part('components/breadcrumbs/index', ['breadcrumbs' => $breadcrumbs]);
?>
