<?php
/**
 * Single Blog Breadcrumbs
 *
 * @package WeAdapt
 */

$breadcrumbs = [];
$current_blog_id = get_current_blog_id();
$main_blog_id = 1;

foreach (['learn', get_post_type()] as $template_name) {
    switch_to_blog($main_blog_id);
    $page_ID = get_page_id_by_template($template_name);
    restore_current_blog();
    
    if (!empty($page_ID)) {
        // Generate the URL for the current blog
        $url = get_site_url($current_blog_id, get_page_uri($page_ID));
        $breadcrumbs[] = [
            'url' => $url,
            'label' => get_the_title($page_ID)
        ];
    }
}

$breadcrumbs[] = ['url' => '', 'label' => get_the_title()];

get_part('components/breadcrumbs/index', ['breadcrumbs' => $breadcrumbs]);
?>