<?php
	$number            = $args['number'] ;
	$temp_args       = ! empty( $args['query_args'] ) ? $args['query_args'] : [];
	$filters_args    = $args['filters_args'];
	$categories    = $args['categories'];
	$show_filters    = isset( $args['show_filters'] ) ? wp_validate_boolean( $args['show_filters'] ) : true;
	$show_search     = isset( $args['show_search'] ) ? wp_validate_boolean( $args['show_search'] ) : false;
	$show_post_types = isset( $args['show_post_types'] ) ? wp_validate_boolean( $args['show_post_types'] ) : true;
	$show_categories = isset( $args['show_categories'] ) ? wp_validate_boolean( $args['show_categories'] ) : false;
	$show_loadmore   = isset( $args['show_loadmore'] ) ? wp_validate_boolean( $args['show_loadmore'] ) : true;
	$base_url        = get_current_clean_url();

	$query_type     = isset( $args['query_type'] ) ? $args['query_type'] : 'wp_query';
	$args_for_query = apply_filters( 'args_for_query', $temp_args, $_GET, $query_type );
	$args_for_filters = apply_filters( 'args_for_query', $filters_args, $_GET, $query_type );
	$query_args     = $args_for_query['args'];

	if ( $query_type === 'user_query' ) {
		$user_query = new WP_User_Query( $query_args );

		$posts_per_page = $temp_args['number'] ? intval( $temp_args['number'] ) : get_option( 'posts_per_page' );
		$max_num_pages  = ceil( $user_query->get_total() / $posts_per_page );

		if ( $show_search && empty( $user_query->results ) ) {
			$show_search = false;
		}
	}
	else {

		$query = new WP_Query( $query_args );
		$max_num_pages = $query->max_num_pages;
	}

?>
<div class="cpt-query" data-idx="<?php echo $number; ?>" >
	<div class="cpt-filters<?php echo ! $show_filters ? ' hidden' : ''; ?>">
		<form class="cpt-filters__form">
            <div class="filter-container" >
                  <?php if ( $show_search ) : ?>
                        <div class="cpt-filters__form__search">
                            <input type="search" name="search" placeholder="<?php _e( 'Search...', 'weadapt' ); ?>" class="cpt-filters__form__input">

                            <button type="submit" class="wp-block-button__link cpt-filters__form__button cpt-filters__item">
                                <?php _e( 'Search', 'weadapt' ); ?>
                                <?php echo get_img( 'icon-search-small' ); ?>
                            </button>
                        </div>
                  <?php endif; ?>

                 <div class="dropdowns-container" >
                        <?php
                            if ( $show_filters ) {
                                get_part( 'components/cpt-filters-awb/index', [
                                    'args_for_query'  => $args_for_filters,
                                    'categories'      => $categories,
                                    'query_type'      => $query_type,
                                    'show_post_types' => $show_post_types,
                                    'show_categories' => $show_categories,
                                ] );
                            }
                        ?>
                 </div>
            </div>

			<div class="cpt-filters__terms<?php echo ! empty( $post_types = $args_for_query['post_types'] ) || (!$show_categories && ! empty( $categories = $args_for_query['categories'] ) ) ? '' : ' hidden'; ?>">
				<ul class="cpt-filters__list">

					<?php if ( ! empty( $post_types ) ) :
						do_action( 'selected_post_types_filter', $base_url, $args_for_query );
					endif; ?>

					<?php if ( !$show_categories && ! empty( $categories ) ) :
						do_action( 'selected_categories_filter', $base_url, $args_for_query );
					endif; ?>
				</ul>
			</div>



			<?php
				if ( $show_categories ) {
					$selected_categories = ! empty( $_GET['categories'] ) ? wp_parse_id_list( $_GET['categories'] ) : [];
					$categories = wp_get_post_terms( get_the_ID(), 'category', [
						'hide_empty' => false
					] );
					$unique_ID = get_unique_ID( 'form' );

					if ( ! empty( $categories ) ) :
						?><ul class="cpt-filters__categories"><?php
							foreach ( $categories as $term ) {
							?>
								<li class="cpt-filters__category">
									<input id="term-<?php echo esc_attr( "$unique_ID-$term->term_id" ); ?>" type="checkbox" name="categories[]" value="<?php echo esc_attr( $term->term_id ); ?>" <?php echo in_array( $term->term_id, $selected_categories ) ? 'checked' : null; ?>>
									<label tabindex="0" for="term-<?php echo esc_attr( "$unique_ID-$term->term_id" ); ?>" class="dropdown-wrapper__btn"><?php echo $term->name; ?></label>
								</li>
							<?php
							}
						?>
						</ul>
						<input type="hidden" value="1" name="has_categories">
						<?php
					endif;
				}
			?>

			<input type="hidden" value="<?php echo esc_attr( $query_type ); ?>" name="query_type">
			<input type="hidden" value="<?php echo esc_attr( json_encode( $temp_args ) ); ?>" name="args" />
			<input type="hidden" value="<?php echo esc_attr( $args_for_query['search_query'] ); ?>" name="s" />
			<input type="hidden" value="<?php echo esc_url( $base_url ); ?>" name="base_url" />
			<input type="hidden" value="<?php echo esc_attr( $args_for_query['post_type'] ); ?>" name="post_type" />
		</form>


	</div>

	<h2 class="section-title cpt-query__heading"><?php echo $args['title'] ?></h2>

	<div
		class="cpt-latest row--ajax row"
		data-page="<?php echo get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1; ?>"
		data-pages="<?php echo $max_num_pages; ?>"
	>
		<?php
			// WP_User_Query
			if ( $query_type === 'user_query' ) {
				load_inline_styles( __DIR__, 'member-item' );

				if ( ! empty( $user_query->results ) ) {
					foreach ( $user_query->results as $user_ID ) :
						echo get_part( 'components/member-item/index', [
							'member_ID' => $user_ID
						] );
					endforeach;
				}
				else {
					echo sprintf( '<span class="empty-result">%s</span>', __( 'Nothing found.', 'weadapt' ) );
				}
			}

			// WP_Query
			else {
				if (
					( isset( $query_args['post__in'] ) && empty( $query_args['post__in'] ) ) ||
					! $query->have_posts()
				) {
					$max_num_pages = 0;

					echo sprintf( '<span class="empty-result">%s</span>', __( 'Nothing found.', 'weadapt' ) );
				}
				else {
					if ( $query->have_posts() ) {
						while ( $query->have_posts() ) {
							$query->the_post();
                            $part = get_post_type() == 'event' ? 'event' : 'blog' ;
							$post_type = get_post_type();

							foreach( [
								'theme_show_buttons',
								'theme_is_author_page',
								'theme_short_excerpt'
							] as $query_var ) {
								if ( ! empty( $query_args[$query_var] ) ) {
									set_query_var( $query_var, wp_validate_boolean( $query_args[$query_var] ) );
								}
							}
							if($post_type == 'event' OR $post_type == 'article') {
								echo get_part( 'components/alt-resource-item/index', [
									'resource_ID' 			=> get_the_ID(),
									'resource_type' 		=> 'resource',
									'resource_cta_label' 	=> $post_type == 'event' ?  'View Event' :  'View Resource',
								]);
							} else {
								get_part( "archive/templates/grid/$part", [
									'post_ID' => get_the_ID()
								] );
							}

						}
					}
				}

				wp_reset_postdata();
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
