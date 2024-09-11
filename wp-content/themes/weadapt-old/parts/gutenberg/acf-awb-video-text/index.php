<?php
/**
 * Video
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$columns      = get_field('columns') ? get_field('columns') : 2;
$attr = $block_object->attr( 'background-' . get_field( 'background_color' ) . ' columns-' . $columns . ' media-' . get_field( 'media_position' ) );
$name = $block_object->name();

?>

<section <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>

	<div class="container">
		<div class="row <?php echo esc_attr( $name ); ?>__row">
			<div class="col-12 col-lg-6 <?php echo esc_attr( $name ); ?>__col <?php echo esc_attr( $name ); ?>__col--video">
				<?php
					$image_placeholder = get_field( 'image_placeholder' );
					$content_type      = get_field( 'content_type' );
					$iframe            = get_field( 'video_iframe' );
					$file              = get_field( 'video_file' );

					if ( ! empty( $iframe) || ! empty( $file ) ) : ?>
						<div class="<?php echo esc_attr( $name ); ?>__video">
							<?php if ( ! empty( $image_placeholder ) ) : ?>
								<div class="<?php echo esc_attr( $name ); ?>__video-placeholder">
									<?php echo get_img( $image_placeholder, 'content-thumbnail' ); ?>
								</div>

								<div class="<?php echo esc_attr( $name ); ?>__video-button">
									<button class="<?php echo esc_attr( $name ); ?>__video-play">
										<?php echo get_img( 'icon-play' ); ?>
									</button>
								</div>
							<?php endif;

							switch ( $content_type ) :
								case 'iframe':
									$attributes = 'tabindex="-1"';
									$iframe = str_replace('></iframe>', ' ' . $attributes . '></iframe>', $iframe);

									echo $iframe;
									break;
								default: ?>
									<video controls tabindex="-1">
										<source src="<?php echo esc_url( $file['url'] ); ?>" type="video/mp4">
									</video>
									<?php break;
							endswitch; ?>
						</div>
					<?php endif;
				?>
			</div>

			<div class="col-12 col-lg-6 <?php echo esc_attr( $name ); ?>__col <?php echo esc_attr( $name ); ?>__col--text">
				<InnerBlocks />
			</div>
		</div>
	</div>
</section>
