<?php
	load_inline_styles( __DIR__, 'cpt-filters' );

	$options         		= ! empty( $args['args_for_query'] ) ? $args['args_for_query'] : [];
	$show_post_types 		= isset( $args['show_post_types'] ) ? wp_validate_boolean( $args['show_post_types'] ) : false;
	$show_categories 		= isset( $args['show_categories'] ) ? wp_validate_boolean( $args['show_categories'] ) : false;
	$initial_empty_sort_by 	= isset( $args['initial_empty_sort_by'] ) ? wp_validate_boolean( $args['initial_empty_sort_by'] ) : false;
	$sort_by_initial_value 	= $initial_empty_sort_by ? '' : 'Newest';

	$selected_categories = ! empty( $options['categories'] ) ? $options['categories'] : [];
	$selected_sort       = ! empty( $options['sort_by'] ) ? $options['sort_by'] : $initial_empty_sort_by;
	$selected_post_types = ! empty( $options['post_types'] ) ? explode( ',', $options['post_types'] ) : [];
	$query_args['query_post_types'] = ['theme', 'network', 'blog', 'article', 'course', 'event', 'case-study'];
	$query_type          = ! empty( $args['query_type'] ) ? $args['query_type'] : 'wp_query';
	$categories = [];
	$post_types = [];
	

	$sort_by = ['Newest', 'Oldest', 'A-Z', 'Z-A'];

	if ( ! in_array( $selected_sort, $sort_by ) ) {
		$selected_sort = $initial_empty_sort_by;
	}

	if ( $query_type === 'user_query' ) {
		if ( $show_categories ) {
			$categories = get_terms( [
				'taxonomy'   => 'interest',
				'hide_empty' => false
			] );
		}
	}
	else {
		if ( $show_post_types || is_category() ) {
			$args_post_types = ! empty( $query_args['query_post_types'] ) ? $query_args['query_post_types'] : get_allowed_post_types([ 'blog', 'article', 'course', 'event', 'case-study', 'theme', 'network', 'organisation', 'solutions-portal', 'forums', 'forum' ]);
			$post_types      = get_allowed_post_types( $args_post_types );
		}
		else {
			if ( is_single() ) {
				$query = new WP_Query( array_merge( $query_args, [
					'posts_per_page' => -1,
					'fields'         => 'ids',
				] ) );

				if ( ! empty( $query->posts ) ) {
					$categories = wp_get_object_terms( $query->posts, 'category' );
				}
			}
			else if ( apply_filters( 'show_post_types_instead_of_categories', false ) )  {
				$post_types = get_theme_network_post_types();
			}
			else if ( apply_filters( 'hide_categories_archive_filter', false ) ) {
				$categories = [];
			}
			else {
				$categories = [];

				if ( $show_categories ) {
					$page_ID = get_queried_object_id();

					if (
						is_search() ||
						! in_array( get_post_type(), [ 'organisation', 'solutions-portal' ] ) &&
						! in_array( $page_ID, [ get_page_id_by_template( 'organisation' ), get_page_id_by_template( 'theme' ), get_page_id_by_template( 'network' ) ])
					) {
						$categories = get_terms( [
							'taxonomy'   => 'category',
						] );
					}
				}
			}
		}
	}

	$unique_ID = get_unique_ID( 'form' );
?>

<?php if ( ! empty( $categories ) || ! empty( $post_types ) ) : ?>
	<div class="cpt-filters__item">
		<label class="visually-hidden" for="filter-by" aria-label="Filter by"><?php _e( 'Filter by', 'weadapt' ); ?></label>

		<div class="dropdown-wrapper">
			<div class="filter-by dropdown-wrapper__inner" tabindex="0" aria-labelledby="filter-by-label" aria-describedby="filter-by-desc">
				<span class="filter-by-label dropdown-wrapper__label"><?php _e( 'Filter by' ); ?></span>
				<span class="dropdown-wrapper__icon"><?php echo get_img( 'icon-chevron-down' ); ?></span>

				<div class="dropdown-wrapper__dropdown" aria-hidden="true">
					<span class="dropdown-wrapper__type">
						<?php ( ! empty( $post_types ) ) ? _e( 'Content type', 'weadapt' ) : _e( 'Categories', 'weadapt' ); ?>
					</span>

					<ul class="dropdown-wrapper__menu dropdown-wrapper__menu--wide" role="listbox">

						<?php if ( ! empty( $post_types ) ) : ?>
							<?php foreach ( $post_types as $post_type ) : ?>
								<li role="option">
									<input id="term-<?php echo esc_attr( "$unique_ID-$post_type" ); ?>" type="checkbox" name="post_types[]" value="<?php echo esc_attr( $post_type ); ?>" <?php echo in_array( $post_type, $selected_post_types ) ? 'checked' : null; ?>>
									<?php
										$post_type_name = $post_type;

										switch ($post_type) {
											case 'forums': $post_type_name = 'forum'; break;
											case 'forum':  $post_type_name = 'discussion'; break;
										}
									?>
									<label tabindex="0" for="term-<?php echo esc_attr( "$unique_ID-$post_type" ); ?>" class="dropdown-wrapper__btn"><?php echo ucfirst( str_replace( '-', ' ', $post_type_name ) ); ?></label>
								</li>
							<?php endforeach; ?>
						<?php else: ?>
							<?php foreach ( $categories as $term ) :
								$term_id = $term->term_id;
							?>
								<li role="option">
									<input id="term-<?php echo esc_attr( "$unique_ID-$term_id" ); ?>" type="checkbox" name="categories[]" value="<?php echo esc_attr( $term_id ); ?>" <?php echo in_array( $term_id, $selected_categories ) ? 'checked' : null; ?>>
									<label tabindex="0" for="term-<?php echo esc_attr( "$unique_ID-$term_id" ); ?>" class="dropdown-wrapper__btn"><?php echo $term->name; ?></label>
								</li>
							<?php endforeach; ?>
						<?php endif; ?>
					</ul>
				</div>
			</div>
		</div>

		<span class="filter-by-desc visually-hidden"><?php _e( 'Select a filter option to refine the search results', 'weadapt' ); ?></span>
	</div>
<?php endif; ?>

<?php if ( ! empty( $sort_by ) ) :
?>
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
