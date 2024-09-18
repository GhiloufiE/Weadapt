<?php
load_inline_styles(__DIR__, 'single-solutions-portal-content');

if (! empty(get_the_content())) {
	echo sprintf('<h2 class="heading">%s</h2>', __('Summary', 'weadapt'));
	the_content();
}

$overview_data = [];

foreach (['locations' => __('Location', 'weadapt')] as $field_key => $field_label) {
	if (! empty($field_value = get_field($field_key))) {
		$overview_data[$field_key] = [
			'label' => $field_label,
			'value' => $field_value
		];
	}
}

$country_text  = (count(get_field('locations')) > 1) ? __('Multiple countries', 'weadapt') : __('Single country', 'weadapt');
$location_text = get_field('multiple_locations') ? __('Multiple locations', 'weadapt') : __('Single location', 'weadapt');
$overview_data['implementation'] = [
	'label' => __('Implementation sites', 'weadapt'),
	'value' => [$country_text, $location_text]
];

foreach (
	[
		'mountain_range'  => __('Mountain region', 'weadapt'),
		'region_province' => __('Province', 'weadapt'),
		'site_locations'  => __('Site locations', 'weadapt'),
		'solution_scale'  => __('Solution scale', 'weadapt'),
	] as $field_key => $field_label
) {
	if (! empty($field_value = get_field($field_key))) {
		$overview_data[$field_key] = [
			'label' => $field_label,
			'value' => $field_value
		];
	}
}

foreach (['solution_scale' => __('Solution scale', 'weadapt')] as $taxonomy => $field_label) {
	if (! empty($terms = wp_get_post_terms(get_the_ID(), str_replace('_', '-', $taxonomy), ['fields' => 'names']))) {
		$overview_data[$taxonomy] = [
			'label' => $field_label,
			'value' => $terms
		];
	}
}

foreach (['area_covered' => __('Area Covered', 'weadapt')] as $field_key => $field_label) {
	if (! empty($field_value = get_field($field_key))) {
		$overview_data[$field_key] = [
			'label' => $field_label,
			'value' => $field_value
		];
	}
}

foreach (['solution_ecosystem_type' => __('Ecosystem type(s)', 'weadapt')] as $taxonomy => $field_label) {
	if (! empty($terms = wp_get_post_terms(get_the_ID(), str_replace('_', '-', $taxonomy), ['fields' => 'names']))) {
		$overview_data[$taxonomy] = [
			'label' => $field_label,
			'value' => $terms
		];
	}
}

foreach (['ecosystem_other' => __('Other Ecosystem type(s)', 'weadapt')] as $field_key => $field_label) {
	if (! empty($field_value = get_field($field_key))) {
		$overview_data[$field_key] = [
			'label' => $field_label,
			'value' => $field_value
		];
	}
}

foreach (['solution_type' => __('Solution type(s)', 'weadapt')] as $taxonomy => $field_label) {
	if (! empty($terms = wp_get_post_terms(get_the_ID(), str_replace('_', '-', $taxonomy), ['fields' => 'names']))) {
		$overview_data[$taxonomy] = [
			'label' => $field_label,
			'value' => $terms
		];
	}
}

foreach (['solution_type_other' => __('Other Solution(s) type(s)', 'weadapt')] as $field_key => $field_label) {
	if (! empty($field_value = get_field($field_key))) {
		$overview_data[$field_key] = [
			'label' => $field_label,
			'value' => $field_value
		];
	}
}

foreach (['solution_sector' => __('Sector(s)', 'weadapt')] as $taxonomy => $field_label) {
	if (! empty($terms = wp_get_post_terms(get_the_ID(), str_replace('_', '-', $taxonomy), ['fields' => 'names']))) {
		$overview_data[$taxonomy] = [
			'label' => $field_label,
			'value' => $terms
		];
	}
}

foreach (['sectors_other' => __('Other sector(s) type(s)', 'weadapt')] as $field_key => $field_label) {
	if (! empty($field_value = get_field($field_key))) {
		$overview_data[$field_key] = [
			'label' => $field_label,
			'value' => $field_value
		];
	}
}

foreach (['solution_climate_impact' => __('Climate impact(s) addressed', 'weadapt')] as $taxonomy => $field_label) {
	if (! empty($terms = wp_get_post_terms(get_the_ID(), str_replace('_', '-', $taxonomy), ['fields' => 'names']))) {
		$overview_data[$taxonomy] = [
			'label' => $field_label,
			'value' => $terms
		];
	}
}

foreach (['climate_impact_other' => __('Other climate impact(s) addressed', 'weadapt')] as $field_key => $field_label) {
	if (! empty($field_value = get_field($field_key))) {
		$overview_data[$field_key] = [
			'label' => $field_label,
			'value' => $field_value
		];
	}
}

foreach (
	[
		'solution_climate_timescale' => __('Climate impact time-scale(s)', 'weadapt'),
		'solution_benefit' => __('Main benefit associated with the solution ', 'weadapt'),
	] as $taxonomy => $field_label
) {
	if (! empty($terms = wp_get_post_terms(get_the_ID(), str_replace('_', '-', $taxonomy), ['fields' => 'names']))) {
		$overview_data[$taxonomy] = [
			'label' => $field_label,
			'value' => $terms
		];
	}
}

foreach (['benefit_other' => __('Other benefit(s) associated with the solution implementation', 'weadapt')] as $field_key => $field_label) {
	if (! empty($field_value = get_field($field_key))) {
		$overview_data[$field_key] = [
			'label' => $field_label,
			'value' => $field_value
		];
	}
}

foreach (['solution_co_benefit' => __('Co-benefit(s) associated with the solution implementation', 'weadapt')] as $taxonomy => $field_label) {
	if (! empty($terms = wp_get_post_terms(get_the_ID(), str_replace('_', '-', $taxonomy), ['fields' => 'names']))) {
		$overview_data[$taxonomy] = [
			'label' => $field_label,
			'value' => $terms
		];
	}
}

foreach (['co_benefit_other' => __('Other co-benefits(s) associated with the solution implementation', 'weadapt')] as $field_key => $field_label) {
	if (! empty($field_value = get_field($field_key))) {
		$overview_data[$field_key] = [
			'label' => $field_label,
			'value' => $field_value
		];
	}
}

$dates = [];
foreach (['start', 'end'] as $dates_key) {
	if (! empty($dates_value = get_field('implementation_' . $dates_key . '_date'))) {
		$dates[] = $dates_value;
	}
}
if (! empty($dates)) {
	$overview_data['implementation_date'] = [
		'label' => __('Implementation timeline', 'weadapt'),
		'value' => implode(' - ', $dates)
	];
}

foreach (
	[
		'solution_addressed_target' => __('Sendai targets', 'weadapt'),
		'solution_addressed_sdg'    => __('SDGs', 'weadapt'),
	] as $taxonomy => $field_label
) {
	if (! empty($terms = wp_get_post_terms(get_the_ID(), str_replace('_', '-', $taxonomy), ['fields' => 'ids']))) {
		$overview_data[$taxonomy] = [
			'label' => $field_label,
			'ids'   => $terms
		];
	}
}
if (! empty($overview_data)) {
	echo '<h2 class="heading">' . __('Overview', 'weadapt') . '</h2>';
	foreach ($overview_data as $item) {
		echo '<dl><dt>' . esc_html($item['label']) . ':</dt><dd><ul>';
		if (isset($item['value']) && is_array($item['value'])) {
			foreach ($item['value'] as $single_value) {
				if (is_array($single_value) && isset($single_value['url'])) {
?>
					<li><img src="<?php echo esc_url($single_value['url']); ?>" alt="<?php echo esc_attr($single_value['alt']); ?>" /></li>
				<?php
				} else {
				?>
					<li><?php echo wp_kses_post($single_value); ?></li>
				<?php
				}
			}
		} elseif (isset($item['value'])) {
			if (is_array($item['value']) && isset($item['value']['url'])) {
				?>
				<li><img src="<?php echo esc_url($item['value']['url']); ?>" alt="<?php echo esc_attr($item['value']['alt']); ?>" /></li>
			<?php
			} else {
			?>
				<li><?php echo wp_kses_post($item['value']); ?></li>
				<?php
			}
		}

		if (! empty($item['ids']) && is_array($item['ids'])) {
			foreach ($item['ids'] as $term_id) {
				$term = get_term($term_id);
				$image_id = get_field('logo', 'term_' . $term_id);
				$slug = $term->slug;

				if ($image_id) {
				?>
					<li class="has-logo"><a class="overview-tag" href="/<?php echo esc_attr($url_part); ?>/<?php echo esc_attr($slug); ?>">
							<?php echo wp_get_attachment_image($image_id, 'thumbnail'); ?>
						</a></li>
				<?php
				} else {
				?>
					<li><a class="overview-tag" href="/<?php echo esc_attr($url_part); ?>/<?php echo esc_attr($slug); ?>">
							<?php echo esc_html($term->name); ?>
						</a></li>
<?php
				}
			}
		}

		echo '</ul></dd></dl>';
	}
}

$details_data = [
	[
		'title'  => __('Solution details', 'weadapt'),
		'fields' => [
			'beneficiaries'             => __('Main beneficiaries & outcomes', 'weadapt'),
			'planning_implementation'   => __('Planning and implementation', 'weadapt'),
			'financed_by'               => __('Finance', 'weadapt'),
			'innovation'                => __('Innovation', 'weadapt'),
			'performance_evaluation'    => __('Performance evaluation', 'weadapt'),
			'longterm_sustain_maintain' => __('Long term project sustainability and maintenance', 'weadapt'),
		]
	],
	[
		'title'  => __('Capacities for design and implementation', 'weadapt'),
		'fields' => [
			'knowledge_capacities'       => __('Knowledge', 'weadapt'),
			'technology_capacities'      => __('Technology', 'weadapt'),
			'political_legal_capacities' => __('Political / Legal', 'weadapt'),
			'institutional_capacities'   => __('Institutional', 'weadapt'),
			'socio_cultural_capacities'  => __('Socio-cultural', 'weadapt'),
		]
	],
	[
		'title'  => __('Outlook & Scalability', 'weadapt'),
		'fields' => [
			'barriers_adverse_effects' => __('Barriers and adverse effects', 'weadapt'),
			'transformation_future'    => __('Transformation and future outlook', 'weadapt'),
			'upscaling_replication'    => __('Potential for upscaling and replication', 'weadapt'),
		]
	]
];

foreach ($details_data as $single_detail) {
	$temp_data = [];
	foreach ($single_detail['fields'] as $field_key => $field_label) {
		if (! empty($field_value = get_field($field_key))) {
			$temp_data[$field_key] = [
				'label' => $field_label,
				'value' => $field_value
			];
		}
	}
	if (! empty($temp_data)) {
		echo '<h2 class="heading">' . $single_detail['title'] . '</h2>';
		foreach ($temp_data as $item) {
			if (! empty($item['label'])) {
				echo '<h3 class="subheading">' . $item['label'] . '</h3>';
			}
			if (! empty($item['value'])) {
				echo apply_filters('the_content', $item['value']);
			}
		}
	}
}
