<?php
/**
 * Video Iframe
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
		<?php if( have_rows( 'videos' ) ) : ?>
			<div class="<?php echo esc_attr( $name ); ?>__videos">
				<?php while( have_rows( 'videos' ) ) : the_row();
					$title = get_sub_field( 'title' );
					$video = get_sub_field( 'video' );

					if ( ! empty( $title ) || ! empty( $video ) ) :
				?>
					<div class="<?php echo esc_attr( $name ); ?>__video">
						<?php if ( ! empty( $title ) ): ?>
							<h5 class="<?php echo esc_attr( $name ); ?>__video__title"><?php echo wp_kses_post( $title ); ?></h5>
						<?php endif; ?>

						<?php if ( ! empty( $video ) ): ?>
							<div class="<?php echo esc_attr( $name ); ?>__video__container">
								<?php echo $video; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; endwhile; ?>
		<?php endif; ?>
	</div>
</section>