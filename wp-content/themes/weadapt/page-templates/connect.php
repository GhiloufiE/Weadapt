<?php
/*
* Template Name: Page Connect
*
* @package    WordPress
* @subpackage weadapt
* @since      weadapt 1.0
*/

global $is_forum_page;

if (isset($_GET['post_types']) && $_GET['post_types'] === 'forum') {
    $is_forum_page = true;
    error_log("Post type is set to forum on connect page");
} else {
    $is_forum_page = false;
    error_log("Post type is not set to forum on connect page");
}

// Log the global variable here to confirm it is set
error_log("Global variable in page-connect.php: " . ($is_forum_page ? 'true' : 'false'));

get_header();

get_template_part('index', null, [
    'type'             => 'blog',
    'query_post_types' => ['forums', 'forum'], 
]);

get_footer();