<?php

$name  = 'case-studies-map-wac';
$title = str_replace( '-', ' ', ucfirst( $name ) );

add_filter( 'google_maps_vars_wac', function( $colors = [] ) {
	$colors = [
		'markerColor'      => '#3A3A34',
		'markerBgColor'    => '#FDF192',
		'markerCipColor'   => '#3A3A34',
		'markerCipBgColor' => '#FDF192',
	];

	return $colors;
} );


acf_register_block_type( [
	'name'            => $name,
	'title'           => __( $title, 'weadapt' ),
	'description'     => __( "$title block", 'weadapt' ),
	'category'        => 'theme_blocks',
	'icon'            => 'location-alt',
	'mode'            => 'edit',
	'align'           => false,
	'keywords'        => [ $title, 'content', 'map' ],
	'supports'        => [
		'align'  => false,
		'anchor' => true,
	],
	'render_template' => get_theme_file_path( "/parts/gutenberg/acf-$name/index.php" ),
	'enqueue_assets'  => function() use ( $name ) {
		wp_enqueue_script( 'map-google-googleapis', 'https://maps.googleapis.com/maps/api/js?key=' . get_field( 'google_maps_api_key', 'options' ), [], '', true );
		wp_enqueue_script( 'map-google-markerclusterer', 'https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js', [], '', true );
		wp_enqueue_script(
			"weadapt/$name",
			get_theme_file_uri( "/parts/gutenberg/acf-$name/index.min.js" ),
			['map-google-googleapis', 'map-google-markerclusterer', 'select'],
			filemtime( get_theme_file_path( "/parts/gutenberg/acf-$name/index.min.js" ) ),
			true
		);
		wp_localize_script( "weadapt/$name", 'googleMapsVarsWac', apply_filters( 'google_maps_vars_wac', [] ) );
	}
] );
