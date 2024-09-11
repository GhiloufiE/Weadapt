<?php
/**
 * Block Feature Downloads
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
		<?php if( have_rows( 'files' ) ) : ?>
			<div class="row <?php echo esc_attr( $name ); ?>__row">
			    <h2 class="col-12 title">
			        <?php if ( get_field( 'title' ) ) : ?>
                    	<?php echo get_field( 'title' ); ?>
                    <?php endif; ?>
			    </h2>
				<?php while( have_rows( 'files' ) ) : the_row();
					$file_ID = get_sub_field( 'file' );
					$title   = get_sub_field( 'title' );

					if ( ! empty( $file_ID ) ) :
				?>
					<div class="card-container col-12 col-md-4">
						<div class="<?php echo esc_attr( $name ); ?>__file">

                            <div class="image-container">
                                <?php $image = wp_get_attachment_image( $file_ID, 'content-thumbnail' );
                            		if ( ! empty( $image ) ) : ?>
                            			<div class="<?php echo esc_attr( $name ); ?>__file-image">
                            				<a download="<?php echo get_the_title( $file_ID ); ?>" href="<?php echo wp_get_attachment_url( $file_ID ); ?>">
                            		            <?php echo $image; ?>
                            			    </a>
                            			</div>
                            	<?php endif; ?>
                            </div>

                            <div class="<?php echo esc_attr( $name ); ?>__file-header">
								<?php if ( ! empty( $title ) ) : ?>
									<p><?php echo $title; ?></p>
								<?php endif; ?>
							</div>

							<div class="awb-feature-downloads__file-content">
								<?php if ( ! empty( $description = '' ) ) : ?>
									<h3 class="single-resource__title"><?php echo wp_kses_post( $description ); ?></h3>
								<?php endif; ?>

								<?php echo get_button(
									[
										'url' => wp_get_attachment_url( $file_ID ),
										'title' => __( 'Download PDF', 'weadapt' ),
										'target' => '',
										'attributes' => [
											'download' => get_the_title( $file_ID ),
										]
									],
									'',
									'',
									'icon-download-light-small'
								); ?>
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
