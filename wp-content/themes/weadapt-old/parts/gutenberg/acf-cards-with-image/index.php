<?php
/**
 * Cards With Image
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$attr = $block_object->attr();
$name = $block_object->name();
?>

<section <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>

	<div class="container">
		<?php echo $block_object->title( "{$name}__heading" ); ?>

		<?php if ( have_rows( 'cards' ) ) : ?>
			<div class="row <?php echo esc_attr( $name ); ?>__row">
				<?php while ( have_rows( 'cards' ) ) : the_row();
					$button = get_sub_field( 'button' );

					if ( ! empty( $button ) ) :
						$url          = $button['url'];
						$button_title = $button['title'];
						$target       = $button['target'] ? $button['target'] : '_self';
				?>
					<div class="col-12 col-md-6 col-lg-4 <?php echo esc_attr( $name ); ?>__col">
						<div class="card-with-image">
							<?php if ( ! empty( $image = get_sub_field( 'image' ) ) ) : ?>
								<div class="card-with-image__image">
									<?php if ( empty( $button_title ) ) : ?>
										<a class="card-with-image__image-link" target="<?php echo esc_attr( $target ); ?>" href="<?php echo esc_url( $url ); ?>">
											<?php echo get_img( $image, 'large' ); ?>
										</a>
									<?php else: ?>
										<?php echo get_img( $image, 'large' ); ?>
									<?php endif; ?>
								</div>
							<?php endif; ?>

							<div class="card-with-image__content">
								<?php if ( ! empty( $title = get_sub_field( 'title' ) ) ) : ?>
									<h4 class="card-with-image__title">
										<?php if ( empty( $button_title ) ) : ?>
											<a class="card-with-image__link" target="<?php echo esc_attr( $target ); ?>" href="<?php echo esc_url( $url ); ?>">
												<?php echo esc_html( $title ); ?>
											</a>
										<?php else: ?>
											<?php echo esc_html( $title ); ?>
										<?php endif; ?>
									</h4>
								<?php endif; ?>

								<?php if ( ! empty( $description = get_sub_field( 'description' ) ) ) : ?>
									<div class="card-with-image__description">
										<?php echo wp_kses_post( $description ); ?>
									</div>
								<?php endif; ?>

								<?php if ( ! empty( $button_title ) && ! empty( $url ) ) : ?>
									<div class="card-with-image__button">
										<a
											class="card-with-image__button-link"
											href="<?php echo esc_url( $url )?>"
											target=<?php echo esc_attr( $target ); ?>
										>
											<?php echo esc_html( $button_title ); ?>
										</a>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php
					endif;
					endwhile;
				?>
			</div>
		<?php endif; ?>
	</div>
</section>