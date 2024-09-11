<?php
/**
 * Organisations Block
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
		<?php if ( have_rows( 'organisations' ) ): ?>
			<div class="<?php echo esc_attr( $name ); ?>__row">
				<?php while( have_rows( 'organisations' ) ) : the_row();
					$title       = get_sub_field( 'title' ); 
					$description = get_sub_field( 'description' ); 
				?>

					<div class="<?php echo esc_attr( $name ); ?>__item">
						<?php if ( ! empty( $title ) || ! empty( $description ) ) : ?>
							<header class="<?php echo esc_attr( $name ); ?>__header">
								<?php if ( ! empty( $title ) ) : ?>
									<h2 class="<?php echo esc_attr( $name ); ?>__title"><?php echo $title; ?></h2>
								<?php endif; ?>

								<?php if ( ! empty( $description ) ) : ?>
									<div class="<?php echo esc_attr( $name ); ?>__description"><?php echo $description; ?></div>
								<?php endif; ?>
							</header>

							<?php if ( have_rows( 'items' ) ): ?>
								<ul class="<?php echo esc_attr( $name ); ?>__list">
									<?php while( have_rows( 'items' ) ) : the_row();
										$title       = get_sub_field( 'title' ); 
										$description = get_sub_field( 'description' ); 
									?>

									<li class="<?php echo esc_attr( $name ); ?>__list__item">
										<?php if ( ! empty( $title ) ) : ?>
											<h3 class="<?php echo esc_attr( $name ); ?>__subtitle"><?php echo $title; ?>: </h3>
										<?php endif; ?>

										<?php if ( have_rows( 'links' ) ): ?>
											<?php while( have_rows( 'links' ) ) : the_row(); 
												$link = get_sub_field( 'link' );

												if ( ! empty( $link ) ) :
													$link_url = $link['url'];
													$link_title = $link['title'];
													$link_target = $link['target'] ? $link['target'] : '_self';
											?>
											    <a href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
											<?php endif; endwhile; ?>
										<?php endif; ?>
									</li>

									<?php endwhile; ?>
								</ul>
							<?php endif; ?>

						<?php endif; ?>

					</div>

				<?php endwhile; ?>
			</div>
		<?php endif; ?>
	</div>
</section>