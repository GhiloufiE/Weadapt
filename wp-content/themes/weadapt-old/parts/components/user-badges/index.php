<?php
/**
 * CTA Widget
 *
 * @package WeAdapt
 */
$user_ID = ! empty( $args['user_ID'] ) ? $args['user_ID'] : 0;
$content = get_field( 'badges', 'options' );
?>

<div class="badges">
	<?php load_inline_styles( __DIR__, 'badges' ); ?>

	<?php if ( ! empty( $content['title'] ) ) : ?>
		<h2 class="badges__title"><?php echo wp_kses_post( $content['title'] ); ?></h2>
	<?php endif; ?>

	<?php if ( ! empty( $content['description'] ) ) : ?>
		<div class="badges__content"><?php echo wp_kses_post( $content['description'] ); ?></div>
	<?php endif; ?>

	<?php if ( ! empty( $badges = get_field( 'badges', 'user_' . $user_ID ) ) ) : ?>
		<div class="badges__list row">
			<?php
				foreach ( $badges as $term_ID ) :
					$term = get_term_by( 'ID', $term_ID, 'badge' );

					if ( ! empty( $term ) ) :
				?>
					<div class="col-12 col-md-6">
						<div class="badges__item">
							<?php if ( ! empty( $thumbnail_ID = get_field( 'thumbnail', $term ) ) ) : ?>
								<div class="badges__item__image"><?php echo get_img( $thumbnail_ID ); ?></div>
							<?php endif; ?>

							<h3 class="badges__item__title"><?php echo esc_html( $term->name ); ?></h3>

							<?php if ( ! empty( $term->description ) ) : ?>
								<div class="badges__item__desc"><?php echo wp_kses_post( $term->description ); ?></div>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>