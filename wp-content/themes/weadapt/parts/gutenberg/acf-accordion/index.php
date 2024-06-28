<?php
/**
 * Block with Accordion
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$attr = $block_object->attr();
$name = $block_object->name();

if ( have_rows( 'accordion_row' ) ) : ?>

<section <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>

	<div class="container accordion__container">
		<div class="single-accordion">
			<ul class="single-accordion__row">
				<?php while( have_rows( 'accordion_row' ) ) : the_row();
					$title            = get_sub_field( 'title' );
					$content          = get_sub_field( 'content' );
					$background_color = get_sub_field( 'background_color' );

					if ( ! empty( $title ) && (! empty( $content ) || have_rows( 'sub_accordion_row' ))) :
				?>
					<li class="single-accordion__item">
						<button class="single-accordion__trigger" style="background-color: <?php echo $background_color; ?>">
							<span class="single-accordion__icon"></span>
							<h4 class="single-accordion__text"><?php echo $title; ?></h4>
						</button>

						<?php if ( ! empty( $content ) ) : ?>
							<div class="single-accordion__content" hidden>
								<?php echo $content; ?>
							</div>
						<?php endif; ?>

						<?php if ( have_rows( 'sub_accordion_row' ) && empty( $content ) ) : ?>
							<ul class="single-accordion__row single-accordion__row--child" hidden>
								<?php while ( have_rows( 'sub_accordion_row' ) ) : the_row(); 
									$title   = get_sub_field( 'item_title' );
									$content = get_sub_field( 'content' );

									if ( ! empty( $title ) ) :
								?>
									<li class="single-accordion__item">
										<button class="single-accordion__trigger">
											<span class="single-accordion__icon"></span>
											<h5 class="single-accordion__text"><?php echo $title; ?></h5>
										</button>

										<?php if ( ! empty( $content ) ) : ?>
											<div class="single-accordion__content" hidden>
												<?php echo $content; ?>
											</div>
										<?php endif; ?>

									</li>									
								<?php endif; endwhile; ?>
							</ul>
						<?php endif; ?>
					</li>
				<?php 
					endif; 
					endwhile; 
				?>
			</ul>
		</div>
	</div>
</section>

<?php endif;
