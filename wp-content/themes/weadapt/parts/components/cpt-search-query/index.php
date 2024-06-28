<?php
	$title         = ! empty( $args['title'] ) ? $args['title'] : '';
	$description   = ! empty( $args['description'] ) ? $args['description'] : '';
	$show_search   = isset( $args['show_search'] ) ? wp_validate_boolean( $args['show_search'] ) : true;

	$query_args    = ! empty( $args['query_args' ] ) ? $args['query_args' ] : [];
	$query_type    = isset( $args['query_type'] ) ? $args['query_type'] : 'wp_query';

	$max_num_pages = 0;

	if ( $query_type === 'user_query' ) {
		$user_query = new WP_User_Query( $query_args );

		$max_num_pages = ceil( $user_query->get_total() / get_option( 'posts_per_page' ) );

		if ( $show_search && empty( $user_query->results ) ) {
			$show_search = false;
		}
	}
	else {
		$query = new WP_Query( $query_args );

		$max_num_pages = $query->max_num_pages;

		if ( $show_search && !$query->have_posts() ) {
			$show_search = false;
		}
	}

	load_blocks_script( 'cpt-search-query', 'weadapt/cpt-search-query' );
?>

<div class="cpt-search-query">
	<?php
		load_inline_styles_shared( 'cpt-search-form' );
		load_inline_styles( __DIR__, 'cpt-search-query' );
	?>

	<?php if ( ! empty( $title ) || ! empty( $description ) ) : ?>
		<div class="cpt-content-heading">
			<?php if ( ! empty( $title ) ) : ?>
				<h2 class="cpt-content-heading__title"><?php echo $title; ?></h2>
			<?php endif; ?>

			<?php if ( ! empty( $description ) ) : ?>
				<p class="cpt-content-heading__text"><?php echo $description; ?></p>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if ( $show_search ) : ?>
		<form class="cpt-search-form">
			<input type="search" name="search" placeholder="<?php _e( 'Search...', 'weadapt' ); ?>" class="cpt-search-form__input">

			<button type="submit" class="wp-block-button__link cpt-search-form__button">
				<?php _e( 'Search', 'weadapt' ); ?>
				<?php echo get_img( 'icon-search-small' ); ?>
			</button>
		</form>
	<?php endif; ?>

	<?php if ( $query_type === 'user_query' && ! empty( $user_query->results ) ) : ?>

		<div class="cpt-search-query__row row--ajax" data-paged="1" data-pages="<?php echo $max_num_pages; ?>">
			<?php foreach ( $user_query->results as $user_ID ) :
				echo get_part( 'components/member-item/index', [
					'member_ID' => $user_ID
				] );
			endforeach; ?>
		</div>

	<?php elseif ( isset( $query ) && $query->have_posts() ) : ?>
		<?php load_inline_dependencies( '/parts/components/info-widget-cpt/', 'info-widget-cpt'); ?>

		<div class="row cpt-search-query__row row--ajax" data-paged="1" data-pages="<?php echo $max_num_pages; ?>">
			<?php while ( $query->have_posts() ) : $query->the_post(); ?>
				<div class="col-12 col-lg-6">
					<?php echo get_part('components/info-widget-cpt/index', [
						'cpt_ID'  => get_the_ID(),
						'cpt_buttons' => [ 'find-out-more' ]
					]); ?>
				</div>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>

	<?php else: ?>
		<p class="cpt-content-heading__text">
			<?php _e( 'There is no content', 'weadapt' ); ?>
		</p>
	<?php endif; ?>

	<?php if ( $max_num_pages > 1 ) : ?>
		<div class="wp-block-button wp-block-button--template cpt-more">

			<button type="button" class="wp-block-button__link cpt-more__btn">
				<?php _e('Load more', 'weadapt'); ?>
			</button>
		</div>
	<?php endif; ?>

	<input type="hidden" value=<?php echo esc_attr( $query_type ); ?> name="query_type">
	<input type="hidden" value="<?php echo esc_attr( json_encode( $query_args ) ); ?>" name="query_args" />
</div>