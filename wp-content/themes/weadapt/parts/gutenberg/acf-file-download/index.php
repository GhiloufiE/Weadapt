<?php
/**
 * Block File Download
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$attr         = $block_object->attr( 'background-' . get_field( 'background_color' ) );
$name         = $block_object->name();
?>

<section <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>

	<div class="container">
		<div class="<?php echo esc_attr( $name ); ?>__header">
			<?php
				echo $block_object->title( '', 'h4' );
				echo $block_object->desc();
			?>
		</div>

		<?php if ( have_rows( 'files' ) ) : ?>
			<div class="<?php echo esc_attr( $name ); ?>__row">
				<?php while ( have_rows( 'files' ) ) : the_row();
					if ( ! empty( $file_ID = get_sub_field( 'file' ) ) ) : ?>
						<div class="<?php echo esc_attr( $name ); ?>__file">
							<?php
								$image = wp_get_attachment_image( $file_ID, 'content-thumbnail' );

								if ( ! empty( $image ) ) : ?>
									<div class="<?php echo esc_attr( $name ); ?>__file-image">
										<a download="<?php echo get_the_title( $file_ID ); ?>" href="<?php echo wp_get_attachment_url( $file_ID ); ?>">
											<?php echo $image; ?>
										</a>
									</div>
								<?php endif;
							?>

							<div class="<?php echo esc_attr( $name ); ?>__file-content">
								<?php echo get_button(
									[
										'url' => wp_get_attachment_url( $file_ID ),
										'title' => __( 'Read report', 'weadapt' ),
										'target' => '',
										'attributes' => [
											'download' => get_the_title( $file_ID ),
										]
									],
									'',
									'',
									'icon-arrow-right-button'
								); ?>
							</div>
                        </div>
				<?php endif;
                endwhile; ?>
			</div>
		<?php endif; ?>
	</div>
</section>