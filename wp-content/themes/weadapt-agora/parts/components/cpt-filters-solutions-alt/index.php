<?php
	load_inline_styles( '/parts/components/cpt-filters', 'cpt-filters' );

	$options         		= ! empty( $args['args_for_query'] ) ? $args['args_for_query'] : [];
	$show_sort 	 			= ! empty( $args['show_sort'] ) ? $args['show_sort'] : false;
	$show_status 	 		= ! empty( $args['show_status'] ) ? $args['show_status'] : false;
	$selected_scales 		= ! empty( $options['scales'] ) ? $options['scales'] : [];
	$selected_ecosystems 	= ! empty( $options['ecosystems'] ) ? $options['ecosystems'] : [];
	$selected_types 		= ! empty( $options['types'] ) ? $options['types'] : [];
	$selected_sectors 		= ! empty( $options['sectors'] ) ? $options['sectors'] : [];
	$selected_impacts 		= ! empty( $options['impacts'] ) ? $options['impacts'] : [];
	$selected_statuses 		= ! empty( $options['status'] ) ? $options['status'] : [];

	$selected_sort       = ! empty( $options['sort_by'] ) ? $options['sort_by'] : 'Newest';
	$selected_post_types = ! empty( $options['post_type'] ) ? explode( ',', $options['post_type'] ) : [];
	$query_args          = ! empty( $options['args'] ) ? $options['args'] : [];
	$query_type          = ! empty( $args['query_type'] ) ? $args['query_type'] : 'wp_query';

	$sort_by = ['Newest', 'Oldest', 'A-Z', 'Z-A'];

	if ( ! in_array( $selected_sort, $sort_by ) ) {
		$selected_sort = 'Newest';
	}

	$all_scales 	= !empty( $args['all_scales'] ) ? $args['all_scales'] : [];
	$all_ecosystems = !empty( $args['all_ecosystems'] ) ? $args['all_ecosystems'] : [];
	$all_types 		= !empty( $args['all_types'] ) ? $args['all_types'] : [];
	$all_sectors 	= !empty( $args['all_sectors'] ) ? $args['all_sectors'] : [];
	$all_impacts 	= !empty( $args['all_impacts'] ) ? $args['all_impacts'] : [];
	$statuses 		= !empty( $args['all_statuses'] ) ? $args['all_statuses'] : [];

	$unique_ID = get_unique_ID( 'form' );
?>

<div class="dropdowns-container">
	<?php if ( ! empty( $sort_by ) and $show_sort ) : ?>
		<div class="cpt-filters__item">
			<label class="cpt-filters__caption" for="sort-by" aria-label="Sort by"><?php _e( 'Sort by:', 'weadapt' ); ?></label>

			<div class="dropdown-wrapper">
				<div class="sort-by dropdown-wrapper__inner" tabindex="0" aria-labelledby="sort-by-label" aria-describedby="sort-by-desc">
					<span class="sort-by-label" class="dropdown-wrapper__label"><?php echo esc_html( $selected_sort ); ?></span>
					<span class="dropdown-wrapper__icon"><?php echo get_img( 'icon-chevron-down' ); ?></span>

					<div class="dropdown-wrapper__dropdown" aria-hidden="true">
						<ul class="dropdown-wrapper__menu" role="listbox">
							<?php foreach ( $sort_by as $sort_name ) : ?>
								<li role="option">
									<input id="<?php echo esc_attr( "$unique_ID-$sort_name" ); ?>" type="radio" name="sort_by" <?php echo $sort_name == $selected_sort ? 'checked' : null ?> value="<?php echo esc_html( $sort_name ); ?>">
									<label tabindex="0" for="<?php echo esc_attr( "$unique_ID-$sort_name" ); ?>" class="dropdown-wrapper__btn"><?php _e( $sort_name, 'weadapt' ); ?></label>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
			<span class="sort-by-desc visually-hidden"><?php _e( 'Select a sort option to change the order of the search results', 'weadapt' ); ?></span>
		</div>
	<?php endif; ?>
</div>

<?php
	$all_taxonomies = [
		'scales' 			=> (object) array('label' => "Scales", "slug" => "solution-scale", "entries" => $all_scales, "selected" => $selected_scales),
		'ecosystems' 		=> (object) array('label' => "Ecosystems Types", "slug" => "solution-ecosystem-type", "entries" => $all_ecosystems, "selected" => $selected_ecosystems),
		'types' 			=> (object) array('label' => "Solution Types", "slug" => "solution-type", "entries" => $all_types, "selected" => $selected_types),
		'sectors' 			=> (object) array('label' => "Sectors", "slug" => "solution-sector" , "entries" => $all_sectors, "selected" => $selected_sectors),
		'impacts' 			=> (object) array('label' => "Impacts Addressed", "slug" => "solution-climate-impact" , "entries" => $all_impacts, "selected" => $selected_impacts),
	];
	$all_statuses = [
		'full' => (object) array('label' => "Full Solution", "slug" => "full" ),
		'pilot' => (object) array('label' => "Short Solution", "slug" => "pilot" ),
	];

?>

<?php if($show_status) : ?>
	<div class="status-filters__container">
		<?php foreach ( $all_statuses as $status ) : ?>
			<div class="status-filters__item">
				<input class="status-filters__item-input"
					type="checkbox"
					id="status_<?php echo esc_attr( $status->slug ); ?>"
					name="status_<?php echo esc_attr( $status->slug ); ?>"
					value="<?php echo esc_attr( $status->slug ); ?>"
					<?php echo in_array( $status->slug, $selected_statuses ) ? 'checked' : null; ?>
				>
				<label for="status_<?php echo esc_attr( $status->slug ); ?>">
					<?php _e( $status->label, 'weadapt' ); ?>
				</label>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>

<?php if ( ! empty( $all_scales ) || ! empty( $all_ecosystems ) || ! empty( $all_types ) || ! empty( $all_sectors ) || ! empty( $all_impacts ) ) : ?>
	<div class="taxonomies-filters__container">
		<?php foreach( $all_taxonomies as $taxonomy) :
			if(!empty( $taxonomy->entries)) : ?>
				<div class="cpt-filters__item">
					<label class="visually-hidden" for="filter-by" aria-label="<?php _e( 'Select Option (s)', 'weadapt' ); ?>">
						<?php _e( 'Select Option (s)', 'weadapt' ); ?>
					</label>
					<span class="dropdown-wrapper__info">
						<?php _e( $taxonomy->label, 'weadapt' ); ?>
					</span>
					<div class="dropdown-wrapper">
						<div class="filter-by dropdown-wrapper__inner" tabindex="0" aria-labelledby="filter-by-label" aria-describedby="filter-by-desc">
							<span class="filter-by-label dropdown-wrapper__label">
								<?php _e( 'Select Option (s)', 'weadapt' ); ?>
							</span>
							<span class="dropdown-wrapper__icon"><?php echo get_img( 'icon-chevron-down' ); ?></span>

							<div class="dropdown-wrapper__dropdown" aria-hidden="true">
								<span class="dropdown-wrapper__type">
									<?php _e( $taxonomy->label, 'weadapt' ); ?>
								</span>
								<ul class="dropdown-wrapper__menu dropdown-wrapper__menu--wide" role="listbox">
										<?php foreach ( $taxonomy->entries as $term ) :
											$term_id = $term->term_id;
										?>
											<li role="option">
												<input id="term-<?php echo esc_attr( "$unique_ID-$term_id" ); ?>" type="checkbox" name="<?php echo $taxonomy->slug; ?>[]" value="<?php echo esc_attr( $term_id ); ?>" <?php echo in_array( $term_id, $taxonomy->selected ) ? 'checked' : null; ?>>
												<label tabindex="0" for="term-<?php echo esc_attr( "$unique_ID-$term_id" ); ?>" class="dropdown-wrapper__btn"><?php echo $term->name; ?></label>
											</li>
										<?php endforeach; ?>
								</ul>
							</div>
						</div>
					</div>

					<span class="filter-by-desc visually-hidden"><?php _e( 'Select a filter option to refine the search results', 'weadapt' ); ?></span>
				</div>
			<?php endif;
		endforeach; ?>
	</div>
<?php endif; ?>