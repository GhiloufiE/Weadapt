<?php

// Enqueue google fonts
add_filter( 'enqueue_google_fonts_url', function( $url ) {
    return 'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap';
} );

// Hide related content in single/blog/content.php
add_filter( 'show_related_single_blog_content', '__return_false' );