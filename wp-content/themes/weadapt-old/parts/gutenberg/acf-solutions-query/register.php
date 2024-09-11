<?php

$name  = 'solutions-query';
$title = str_replace( '-', ' ', ucfirst( $name ) );

acf_register_block_type( [
	'name'            => $name,
	'title'           => __( $title, 'weadapt' ),
	'description'     => __( "$title block", 'weadapt' ),
	'category'        => 'theme_blocks',
	'icon'            => 'welcome-view-site',
	'mode'            => 'edit',
	'align'           => false,
	'keywords'        => [ $title, 'content' ],
	'supports'        => [
		'align'  => false,
		'anchor' => true,
	],
	'render_template' => get_theme_file_path( "/parts/gutenberg/acf-$name/index.php" ),
] );