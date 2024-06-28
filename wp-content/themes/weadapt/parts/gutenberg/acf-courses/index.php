<?php
/**
 * Courses Block
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
		<?php if ( have_rows( 'courses' ) ): ?>
			<div class="<?php echo esc_attr( $name ); ?>__row">
				<?php while( have_rows( 'courses' ) ) : the_row(); ?>

					<div class="<?php echo esc_attr( $name ); ?>__item">
						<header class="<?php echo esc_attr( $name ); ?>__header">
							<?php if ( ! empty( $title = get_sub_field( 'title' ) ) ) : ?>
								<h2 class="<?php echo esc_attr( $name ); ?>__title"><?php echo $title; ?></h2>
							<?php endif; ?>
						</header>


						<div class="<?php echo esc_attr( $name ); ?>__main">
							<ul class="<?php echo esc_attr( $name ); ?>__list">
								<li><?php _e( 'CanAdapt Courses', 'weadapt' ); ?></li>
								<li><?php _e( 'Institution', 'weadapt' ); ?></li>
								<li><?php _e( 'Instructor', 'weadapt' ); ?></li>
							</ul>

							<?php if ( have_rows( 'list' ) ) : ?>
								<ul class="<?php echo esc_attr( $name ); ?>__list__items">
									<?php while( have_rows( 'list' ) ) : the_row();
										$title = get_sub_field( 'name' );
										$institution = get_sub_field( 'institution' );
										$instructor = get_sub_field( 'instructor' ); ?>

										<li class="<?php echo esc_attr( $name ); ?>__list__item">
											<?php if ( ! empty( $title ) ) : ?>
												<span class="name"><?php echo $title; ?></span>
											<?php endif; ?>

											<?php if ( ! empty( $institution ) ) : ?>
												<span><?php echo $institution; ?></span>
											<?php endif; ?>

											<?php if ( ! empty( $instructor ) ) : ?>
												<span><?php echo $instructor; ?></span>
											<?php endif; ?>
										</li>
									<?php endwhile; ?>
								</ul>
							<?php endif; ?>
						</div>
					</div>

				<?php endwhile; ?>
			</div>
		<?php endif; ?>
	</div>
</section>