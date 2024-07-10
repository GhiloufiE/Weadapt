<?php
	$temp_args       = ! empty( $args['query_args'] ) ? $args['query_args'] : [];
	$acf_taxonomies  = ! empty( $args['acf_taxonomies'] ) ? $args['acf_taxonomies'] : [];
	$show_filters    = isset( $args['show_filters'] ) ? wp_validate_boolean( $args['show_filters'] ) : true;
	$show_loadmore   = isset( $args['show_loadmore'] ) ? wp_validate_boolean( $args['show_loadmore'] ) : true;
	$base_url        = get_current_clean_url();

	$show_search     = isset( $args['show_search'] ) ? $args['show_search'] : false;
	$show_sort       = isset( $args['show_sort'] ) ? $args['show_sort'] : false;
	$show_status     = isset( $args['show_status'] ) ? $args['show_status'] : false;

	$query_type      	= 'wp_query';
	$args_for_query 	= apply_filters( 'args_for_solutions_query', $temp_args, $_GET, $query_type);
	$query_args     	= $args_for_query['args'];

	$tax_query = [
		'relation' => 'AND'
	];

	$meta_query = [
		'relation' => 'OR',
	];

	load_blocks_script( 'cpt-solutions-query', 'weadapt/cpt-solutions-query' );

	$acf_taxonomies_slugs = [];
	foreach ( $acf_taxonomies as $taxonomy ) {
		$acf_taxonomies_slugs[] = $taxonomy['value'];
	}

	if( !empty($args_for_query["scales"]) and in_array('solution-scale', $acf_taxonomies_slugs) ) {
		$tax_query[] = [
			'taxonomy' => 'solution-scale',
			'field'    => 'term_id',
			'terms'    => wp_parse_id_list($args_for_query["scales"]),
			'operator' => 'IN',
		];
	}
	if( !empty($args_for_query["ecosystems"])  and in_array('solution-ecosystem-type',$acf_taxonomies_slugs) ) {
		$tax_query[] = [
			'taxonomy' => 'solution-ecosystem-type',
			'field'    => 'term_id',
			'terms'    =>  wp_parse_id_list($args_for_query["ecosystems"]),
			'operator' => 'IN',
		];
	}
	if( !empty($args_for_query["types"]) and in_array('solution-type', $acf_taxonomies_slugs) ) {
		$tax_query[] = [
			'taxonomy' => 'solution-type',
			'field'    => 'term_id',
			'terms'    =>  wp_parse_id_list($args_for_query["types"]),
			'operator' => 'IN',
		];
	}
	if( !empty($args_for_query["sectors"]) and in_array('solution-sector', $acf_taxonomies_slugs) ) {
		$tax_query[] = [
			'taxonomy' => 'solution-sector',
			'field'    => 'term_id',
			'terms'    =>  wp_parse_id_list($args_for_query["sectors"]),
			'operator' => 'IN',
		];
	}
	if( !empty($args_for_query["impacts"]) and in_array('solution-climate-impact', $acf_taxonomies_slugs) ) {
		$tax_query[] = [
			'taxonomy' => 'solution-climate-impact',
			'field'    => 'term_id',
			'terms'    =>  wp_parse_id_list($args_for_query["impacts"]),
			'operator' => 'IN',
		];
	}

	if ( !empty($args_for_query["status"]) ) {
		foreach ($args_for_query["status"] as $status) {
			$meta_query[] = [
				'key'     => 'status',
				'value'   => $status,
				'compare' => '=',
			];
		}
	}

	if ( ! empty( $tax_query ) ) {
		$query_args['tax_query'] = $tax_query;
	}
	if ( ! empty( $meta_query ) ) {
		$query_args['meta_query'] = $meta_query;
	}

	$query = new WP_Query( $query_args );
	$max_num_pages = $query->max_num_pages;
?>

<div class="cpt-query cpt-solutions-query">
	<?php load_inline_styles( __DIR__, 'cpt-solutions-query' ); ?>
	<div class="cpt-filters">
		<form class="cpt-filters__form">
			<div class="filter-container" >
				  <?php if ( $show_search ) :
				  	 $search_term = isset( $_GET['search'] ) ? $_GET['search'] : '';
				  ?>
					 <div class="cpt-filters__form__search">
						 <input value="<?php echo $search_term; ?>" type="search" name="search" placeholder="<?php _e( 'Search...', 'weadapt' ); ?>" class="cpt-filters__form__input">

						 <button type="submit" class="wp-block-button__link cpt-filters__form__button cpt-filters__item">
							 <?php _e( 'Search', 'weadapt' ); ?>
							 <?php echo get_img( 'icon-search-small' ); ?>
						 </button>

						 <a href="<?php echo $base_url; ?>" class="wp-block-button__link cpt-filters__item reset-btn">
						 	<?php echo __( 'Reset', 'weadapt' ); ?>
						 </a>
					 </div>
			   	<?php endif; ?>
				<?php
					$all_scales = get_terms( array(
						'taxonomy'   => 'solution-scale',
						'hide_empty' => true,
					) );
					$all_ecosystems = get_terms( array(
						'taxonomy'   => 'solution-ecosystem-type',
						'hide_empty' => true,
					) );
					$all_types = get_terms( array(
						'taxonomy'   => 'solution-type',
						'hide_empty' => true,
					) );
					$all_sectors = get_terms( array(
						'taxonomy'   => 'solution-sector',
						'hide_empty' => true,
					) );
					$all_impacts = get_terms( array(
						'taxonomy'   => 'solution-climate-impact',
						'hide_empty' => true,
					) );
				?>
				 <?php
					if ( $show_filters ) {
						get_part( 'components/cpt-filters-solutions-alt/index', [
							'args_for_query'  		=> $args_for_query,
							'acf_taxonomies_slugs'	=> $acf_taxonomies_slugs,
							'all_scales' 			=> in_array('solution-scale', $acf_taxonomies_slugs) ? $all_scales : [],
							'all_ecosystems' 		=> in_array('solution-ecosystem-type', $acf_taxonomies_slugs) ? $all_ecosystems : [],
							'all_types' 			=> in_array('solution-type', $acf_taxonomies_slugs) ? $all_types : [],
							'all_sectors' 			=> in_array('solution-sector', $acf_taxonomies_slugs) ? $all_sectors : [],
							'all_impacts' 			=> in_array('solution-climate-impact', $acf_taxonomies_slugs) ? $all_impacts : [],
							'all_statuses'			=> ['full', 'pilot'],
							'show_sort' 			=> $show_sort,
							'show_status' 			=> $show_status,
						] );
					}
				?>
			</div>

			<?php
				$scales = $args_for_query['scales'];
				$ecosystems = $args_for_query['ecosystems'];
				$types = $args_for_query['types'];
				$sectors = $args_for_query['sectors'];
				$impacts = $args_for_query['impacts'];

			?>

			<div class="cpt-filters__terms<?php echo ( !empty( $scales) || !empty( $ecosystems) || !empty( $types) || !empty( $sectors) || !empty( $impacts) ) ? '' : ' hidden'; ?>">
				<ul class="cpt-filters__list">
					<?php if ( ! empty( $scales ) || ! empty( $ecosystems ) || ! empty( $types ) || ! empty( $sectors ) || ! empty( $impacts )) :
						do_action( 'selected_taxonomies_filter', $base_url, $args_for_query );
					endif; ?>
				</ul>
			</div>

			<input type="hidden" value="<?php echo esc_attr( $query_type ); ?>" name="query_type">
			<input type="hidden" value="<?php echo esc_attr( json_encode( $temp_args ) ); ?>" name="args" />
			<input type="hidden" value="<?php echo esc_attr( $args_for_query['search_query'] ); ?>" name="s" />
			<input type="hidden" value="<?php echo esc_url( $base_url ); ?>" name="base_url" />
			<input type="hidden" value="<?php echo esc_attr( $args_for_query['post_type'] ); ?>" name="post_type" />
		</form>
	</div>

	<div
		class="solutions-list cpt-latest row--ajax"
		data-page="<?php echo get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1; ?>"
		data-pages="<?php echo $max_num_pages; ?>"
	>
	<?php
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				echo get_part( 'components/alt-resource-item/index', [
					'resource_ID' => get_the_ID(),
					'resource_type' 		=> 'resource',
					'resource_cta_label' 	=> 'View Resource',
				] );
			}
		} else {
			echo sprintf( '<span class="empty-result">%s</span>', __( 'Nothing found.', 'weadapt' ) );
		}
	?>
	</div>


	<?php if ( $show_loadmore ) : ?>
		<div class="wp-block-button wp-block-button--template cpt-more<?php echo $max_num_pages > 1 ? '' : ' hidden'; ?>">
			<button type="button" class="wp-block-button__link cpt-more__btn">
				<?php _e('Load more', 'weadapt'); ?>
			</button>
		</div>
	<?php endif; ?>
</div>