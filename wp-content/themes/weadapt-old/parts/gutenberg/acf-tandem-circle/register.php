<?php

$name  = 'tandem-circle';
$title = str_replace( '-', ' ', ucfirst( $name ) );

acf_register_block_type( [
	'name'            => $name,
	'title'           => __( $title, 'weadapt' ),
	'description'     => __( "$title block", 'weadapt' ),
	'category'        => 'theme_blocks',
	'icon'            => 'welcome-widgets-menus',
	'mode'            => 'edit',
	'align'           => false,
	'keywords'        => [ $title, 'content' ],
	'supports'        => [
		'align'  => false,
		'anchor' => true,
	],
	'render_template' => get_theme_file_path( "/parts/gutenberg/acf-$name/index.php" ),
	'enqueue_assets'  => function() use ( $name ) {
		wp_enqueue_script(
			"weadapt/$name",
			get_theme_file_uri( "/parts/gutenberg/acf-$name/index.min.js" ),
			[],
			filemtime( get_theme_file_path( "/parts/gutenberg/acf-$name/index.min.js" ) ),
			true
		);
	}
] );