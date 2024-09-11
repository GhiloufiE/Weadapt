<?php

/**
 * Register Sidebars
 */
add_action( 'widgets_init', function() {
	global $custom_settings;

	if ( isset( $custom_settings->sidebars ) ) {
		foreach ( $custom_settings->sidebars as $id => $name ) {
			register_sidebar( array(
				'name'          => $name,
				'id'            => $id,
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h4 class="widget__title">',
				'after_title'   => '</h4>'
			) );
		}
	}
} );