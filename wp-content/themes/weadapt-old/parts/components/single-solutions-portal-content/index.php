<?php
/**
 * Single resources
 *
 * @package WeAdapt
 */
load_inline_styles( __DIR__, 'single-solutions-portal-content' );


// Summary
if ( ! empty( get_the_content() ) ) {
	echo sprintf('<h2 class="heading">%s</h2>', __( 'Summary', 'weadapt' ));

	the_content();
}


// Overview
$overview_data = [];

foreach ( [
	'locations' => __( 'Location', 'weadapt' ),
] as $field_key => $field_label ) {
	if ( ! empty( $field_value = get_field( $field_key ) ) ) {
		$overview_data[$field_key] = [
			'label' => $field_label,
			'value' => $field_value
		];
	}
}

$country_text  = ( count( get_field( 'locations' ) ) > 1 ) ? __( 'Multiple countries', 'weadapt' ) : __( 'Single country', 'weadapt' );
$location_text = get_field( 'multiple_locations' ) ? __( 'Multiple locations', 'weadapt' ) : __( 'Single location', 'weadapt' );

$overview_data['implementation'] = [
	'label' => __( 'Implementation sites', 'weadapt' ),
	'value' => [$country_text, $location_text]
];

foreach ( [
	'mountain_range'  => __( 'Mountain region', 'weadapt' ),
	'region_province' => __( 'Province', 'weadapt' ),
	'site_locations'  => __( 'Site locations', 'weadapt' ),
	'solution_scale'  => __( 'Solution scale', 'weadapt' ),
] as $field_key => $field_label ) {
	if ( ! empty( $field_value = get_field( $field_key ) ) ) {
		$overview_data[$field_key] = [
			'label' => $field_label,
			'value' => $field_value
		];
	}
}
foreach ( [
	'solution_scale'             => __( 'Solution scale', 'weadapt' ),
	'solution_ecosystem_type'    => __( 'Ecosystem type(s)', 'weadapt' ),
	'solution_type'              => __( 'Solution type(s)', 'weadapt' ),
	'solution_sector'            => __( 'Sector(s)', 'weadapt' ),
	'solution_climate_impact'    => __( 'Climate impact(s) addressed', 'weadapt' ),
	'solution_climate_timescale' => __( 'Impact time-scales', 'weadapt' ),
	'solution_benefit'           => __( 'Benefits', 'weadapt' ),
	'solution_co_benefit'        => __( 'Co-benefits', 'weadapt' ),
] as $taxonomy => $field_label ) {
	if ( ! empty( $terms = wp_get_post_terms( get_the_ID(), str_replace('_', '-', $taxonomy), [ 'fields' => 'names' ] ) ) ) {
		$overview_data[$taxonomy] = [
			'label' => $field_label,
			'value' => $terms
		];
	}
}

$dates = [];

foreach ( [ 'start', 'end' ] as $dates_key ) {
	if ( ! empty( $dates_value = get_field( 'implementation_' . $dates_key . '_date' ) ) ) {
		$dates[] = $dates_value;
	}
}
if ( ! empty( $dates ) ) {
	$overview_data['implementation_date'] = [
		'label' => __( 'Implementation timeline', 'weadapt' ),
		'value' => implode( ' - ', $dates )
	];
}
foreach ( [
	'solution_addressed_target' => __( 'Sendai targets', 'weadapt' ),
	'solution_addressed_sdg'    => __( 'SDGs', 'weadapt' ),
] as $taxonomy => $field_label ) {
	if ( ! empty( $terms = wp_get_post_terms( get_the_ID(), str_replace('_', '-', $taxonomy), [ 'fields' => 'ids' ] ) ) ) {
		$overview_data[$taxonomy] = [
			'label' => $field_label,
			'ids'   => $terms
		];
	}
}

if ( ! empty( $overview_data ) ) {
	?><h2 class="heading"><?php _e( 'Overview', 'weadapt' ) ?></h2><?php

	foreach ( $overview_data as $key => $item ) {
	?>
	<dl>
		<dt><?php echo $item['label']; ?>:</dt>
		<dd><ul><?php
			if ( ! empty( $item['value'] ) ) {
			    $url_part = '';
			    switch ($item['label']) {
                    case 'Location':
                        $url_part = 'tags';
                        break;
                    case 'Solution scale':
                        $url_part = 'solution-scale';
                        break;
                    case 'Ecosystem type(s)':
                        $url_part = 'solution-ecosystem-type';
                        break;
                    case 'Co-benefits':
                        $url_part = 'solution-co-benefit';
                        break;
                    case 'Benefits':
                        $url_part = 'solution-benefit';
                        break;
                    case 'Impact time-scales':
                        $url_part = 'solution-climate-timescale';
                        break;
                    case 'Solution type(s)':
                        $url_part = 'solution-type';
                        break;
                    case 'Sector(s)':
                        $url_part = 'solution-sector';
                        break;
                    case 'Climate impact(s) addressed':
                        $url_part = 'solution-climate-impact';
                        break;


                }

				if ( is_array( $item['value'] ) ) {
					foreach ( $item['value'] as $single_value ) {
					    $slug = get_term_by('name', $single_value, $url_part)->slug;

						if( term_exists( $single_value ) || $item['label'] === 'Site locations' ) {
						    ?> <li><a class="overview-tag" href="/<?php echo $url_part; ?>/<?php echo $slug; ?>"><?php echo $single_value; ?></a></li> <?php
						} else {
						    ?> <li><?php echo $single_value; ?></li> <?php
						}
					}
				}
				else {

                    if( term_exists( $item['value'] || $item['label'] === 'Site locations' ) ) {
                        $slug = get_term_by('name', $single_value, $url_part)->slug;
                        ?> <li><a class="overview-tag" href="/<?php echo $url_part; ?>/<?php echo $slug; ?>"><?php echo $item['value']; ?></a></li> <?php
                    } else {
                        ?><li><?php echo $item['value']; ?></li><?php
                    }
				}
			}
			if ( ! empty( $item['ids'] ) ) {
				if ( is_array( $item['ids'] ) ) {
					foreach ( $item['ids'] as $term_id ) {
                        $url_part = 'solution-addressed-target';
                        if( $term_id === 'SDGs' ) { $url_part = 'solution-addressed-sdg'; }

					    $slug = get_term( $term_id )->slug;
						if ( ! empty( $image_id = get_field( 'logo', 'term_' . $term_id ) ) ) {
							?><li class="has-logo"><a class="overview-tag" href="/<?php echo $url_part; ?>/<?php echo $slug; ?>" ><?php echo get_img( $image_id ); ?></li><?php
						}
					}
				}
			}
		?></ul></dd>
	</dl>
	<?php
	}
}


// Details
$details_data = [ [
	'title'  => __( 'Solution details', 'weadapt' ),
	'fields' => [
		'beneficiaries'             => __( 'Main beneficiaries & outcomes', 'weadapt' ),
		'planning_implementation'   => __( 'Planning and implementation', 'weadapt' ),
		'financed_by'               => __( 'Finance', 'weadapt' ),
		'innovation'                => __( 'Innovation', 'weadapt' ),
		'performance_evaluation'    => __( 'Performance evaluation', 'weadapt' ),
		'longterm_sustain_maintain' => __( 'Long term project sustainability and maintenance', 'weadapt' ),
	]
], [
	'title'  => __( 'Capacities for design and implementation', 'weadapt' ),
	'fields' => [
		'knowledge_capacities'       => __( 'Knowledge', 'weadapt' ),
		'technology_capacities'      => __( 'Technology', 'weadapt' ),
		'political_legal_capacities' => __( 'Political / Legal', 'weadapt' ),
		'institutional_capacities'   => __( 'Institutional', 'weadapt' ),
		'socio_cultural_capacities'  => __( 'Socio-cultural', 'weadapt' ),
	]
], [
	'title'  => __( 'Outlook & Scalability', 'weadapt' ),
	'fields' => [
		'barriers_adverse_effects' => __( 'Barriers and adverse effects', 'weadapt' ),
		'transformation_future'    => __( 'Transformation and future outlook', 'weadapt' ),
		'upscaling_replication'    => __( 'Potential for upscaling and replication', 'weadapt' ),
	]
], [
	'title'  => __( 'Finally', 'weadapt' ),
	'fields' => [
		'acknowledgments'        => __( 'Acknowledgments', 'weadapt' ),
		'anything_else'          => __( 'CCA in mountains', 'weadapt' ),
		'institutional_contacts' => __( 'Contacts of key institutional partners involved with the solution planning and implementation', 'weadapt' ),
		'key_references_links'   => __( 'Key references/links', 'weadapt' ),
	]
]];

foreach ( $details_data as $single_detail ) {
	$temp_data = [];

	if ( ! empty( $single_detail['fields'] ) ) {
		foreach ( $single_detail['fields'] as $field_key => $field_label ) {
			if ( ! empty( $field_value = get_field( $field_key ) ) ) {
				$temp_data[$field_key] = [
					'label' => $field_label,
					'value' => $field_value
				];
			}
		}
	}

	if ( ! empty( $temp_data ) ) {
		if ( ! empty( $single_detail['title'] ) ) {
			?><h2 class="heading"><?php echo $single_detail['title']; ?></h2><?php
		}

		foreach ( $temp_data as $key => $item ) {
			if ( ! empty( $item['label'] ) ) {
				?><h3 class="subheading"><?php echo $item['label']; ?></h3><?php
			}

			if ( ! empty( $item['value'] ) ) {
				echo apply_filters( 'the_content', $item['value'] );
			}
		}
	}
}
