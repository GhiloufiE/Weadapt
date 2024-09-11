<?php
/**
 * Block with Slider
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$attr = $block_object->attr( 'background-' . get_field( 'background_color' ) );
$name = $block_object->name();
$section_title = get_field('title');

if ( have_rows( 'slider' ) ) : ?>
	<section <?php echo $attr; ?>>
		<?php load_inline_styles( __DIR__, $name ); ?>
		<?php load_inline_styles_plugin( 'swiper-bundle.min' ); ?>
		<?php load_inline_styles_shared( 'sliders' ); ?>

		<div class="container">
			<div class="<?php echo esc_attr( $name ); ?>__body">
			    <?php if ( ! empty( $section_title ) ) : ?>
                	<p class="<?php echo esc_attr( $name ); ?>-section-title">
                		<?php echo $section_title; ?>
                	</p>
                <?php endif; ?>
				<div class="swiper <?php echo esc_attr( $name ); ?>-swiper">
					<div class="swiper-wrapper">
						<?php while ( have_rows( 'slider' ) ) : the_row();
							$description = get_sub_field( 'description' );
							$title       = get_sub_field( 'title' );

							if ( ! empty( $title ) || ! empty( $description ) ) :
						?>
							<div class="swiper-slide <?php echo esc_attr( $name ); ?>-slide">
								<?php if ( ! empty( $description ) ) : ?>
									<div class="<?php echo esc_attr( $name ); ?>-slide__description">
										<?php echo $description; ?>
									</div>
								<?php endif; ?>

								<?php if ( ! empty( $title ) ) : ?>
									<h5 class="<?php echo esc_attr( $name ); ?>-slide__title">
										<?php echo $title; ?>
									</h5>
								<?php endif; ?>
							</div>
						<?php
							endif;
							endwhile;
						?>
					</div>
				</div>

				<div class="swiper-pagination" data-i18n="<?php _e( 'Slide', 'weadapt' ); ?>"></div>
			</div>
		</div>
	</section>
<?php endif;
