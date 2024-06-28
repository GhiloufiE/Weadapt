<?php
/**
 * Contributors And Organisations Block
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$attr = $block_object->attr();
$name = $block_object->name();

$post_types  = get_allowed_post_types( [ 'article', 'case-study', 'network', 'theme', 'event', 'course', 'blog' ] );
$current_tab = ! empty( $_GET['section-tab'] ) ? trim( esc_attr( $_GET['section-tab'] ) ) : '';
?>

<section <?php echo $attr; ?>>
	<?php load_inline_styles_plugin( 'swiper-bundle.min' ); ?>
	<?php load_inline_styles_shared( 'cpt-list-item' ); ?>
	<?php load_inline_styles( __DIR__, $name ); ?>

	<div class="container">
		<header class="section-header has-text-align-center">
			<?php
				echo $block_object->subtitle( "{$name}__subtitle" );
				echo $block_object->title( "{$name}__heading" );
				echo $block_object->desc( "{$name}__descriprion" );
			?>
		</header>

		<?php if ( ! empty( $post_types ) ) : ?>
			<nav class="what-is-trending__nav">
				<div class="swiper">
					<ul class="swiper-wrapper">
						<?php foreach ( $post_types as $key => $post_type ) : ?>
							<li class="swiper-slide"><button class="what-is-trending__nav-item<?php
								if ( ! empty( $current_tab ) ) {
									if ( $post_type === $current_tab ) {
										echo ' active';
									}
								} else {
									if ( $key === 0 ) {
										echo ' active';
									}
								}
							?>" data-tab="<?php echo esc_attr( $key ); ?>" data-key="<?php echo esc_attr( $post_type ); ?>"><?php
								if ( 'blog' === $post_type ) {
									_e( 'Blogs', 'weadapt' );
								}
								else {
									echo get_post_type_object( $post_type )->label;
								}
							?></button></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</nav>

			<div class="what-is-trending__content <?php echo substr_count($_SERVER['REQUEST_URI'], '/') === 1 ? 'home-trending' : '' ; ?>">
				<?php foreach ( $post_types as $key => $post_type ) : ?>
					<div class="what-is-trending__list<?php
							if ( ! empty( $current_tab ) ) {
								if ( $post_type === $current_tab ) {
									echo ' active';
								}
							} else {
								if ( $key === 0 ) {
									echo ' active';
								}
							}
						?>" data-tab="<?php echo esc_attr( $key ); ?>">
						<?php
							$query_args = [
								'post_type'            => $post_type,
								'posts_per_page'       => 6,
								'ignore_sticky_posts'  => true,
								'theme_short_excerpt'  => true,
								'theme_query'          => true, // multisite fix
							];

							if ( 'event' === $post_type ) {
								$query_args['orderby']   = 'meta_value';
								$query_args['meta_key']  = 'start_date';
								$query_args['meta_type'] = 'DATETIME';
							}

							if ( 'course' === $post_type ) {
								$main_theme_network = get_page_by_path( 'climate-adaptation-training', OBJECT, 'theme' );

								if ( ! empty( $main_theme_network->ID ) ) {
									$query_args['post_type']  = 'article';
									$query_args['meta_query'] = [ [
										'key'   => 'relevant_main_theme_network',
										'value' => $main_theme_network->ID,
									] ];
								}
							}

							if ( ( ! empty( $current_tab ) && ( $post_type === $current_tab ) ) || ( $key === 0 )) :
								get_part( 'components/cpt-query/index', [
									'query_args'   => $query_args,
									'show_filters' => false
								]);
							else :
							?>
								<div class="cpt-query">
									<form class="cpt-filters__form hidden">
										<input type="hidden" value="<?php echo esc_attr( json_encode( $query_args ) ); ?>" name="args" />
										<input type="hidden" value="<?php echo esc_url( get_current_clean_url() ); ?>" name="base_url" />
									</form>
									<div class="cpt-latest row--ajax" data-page="1" data-pages="0"></div>
									<div class="wp-block-button wp-block-button--template cpt-more hidden">
										<button type="button" class="wp-block-button__link cpt-more__btn">
											<?php _e('Load more', 'weadapt'); ?>
										</button>
									</div>
								</div>
							<?php
							endif;
						?>
					</div>
				<?php endforeach; ?>
				<div class="loader__wrap"><?php echo get_img( 'loader' ); ?></div>
			</div>
		<?php endif; ?>
	</div>
</section>
