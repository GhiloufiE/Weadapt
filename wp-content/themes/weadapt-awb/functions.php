<?php
function enqueue_child_theme_scripts() {
   /* This was removed because it was causing the header to be transparent at top of page
   wp_enqueue_script(
        "child-theme-transparent-bg",
        get_stylesheet_directory_uri() . "/parts/components/header/transparent-bg.js",
        [],
        '1.0',
        true
    ); */
    if ( get_post_type() === 'article' ) {
      wp_enqueue_script(
            "article-image-text",
            get_stylesheet_directory_uri() . "/parts/single/blog/image.js",
            [],
            '1.0',
            true
        );
    }
}

add_action('wp_enqueue_scripts', 'enqueue_child_theme_scripts');

