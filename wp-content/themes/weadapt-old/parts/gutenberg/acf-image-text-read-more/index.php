<?php
/**
 * Block Image Text Read More
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$attr = $block_object->attr( 'background-' . esc_attr( get_field( 'background_color' ) ) );
$name = $block_object->name();

$content_more = ! empty( get_field('content_more') ) ? get_field('content_more') : '';
$read_more_label = ! empty( get_field('read_more_label') ) ? get_field('read_more_label') : _e( 'Read more', 'weadapt' );

$image_position = get_field( 'image_position' );
$row_alignment  = get_field( 'row_alignment' );
$hide_button_icon    = get_field( 'button_icon' );
?>

<section <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-heading/', 'core-heading'); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-paragraph/', 'core-paragraph'); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-button/', 'core-button'); ?>

	<div class="container">
		<div class="row <?php echo esc_attr( $name ); ?>__row alignment-<?php echo esc_attr( $row_alignment ); ?>">
			<div class="col-12 col-md-6 <?php echo esc_attr( $name ); ?>__col <?php echo esc_attr( $name ); ?>__col--image <?php echo esc_attr( $image_position); ?>">
				<?php echo $block_object->image( "{$name}__image" ); ?>
			</div>

			<div class="col-12 col-md-6 <?php echo esc_attr( $name ); ?>__col <?php echo esc_attr( $name ); ?>__col--text">
				<div class="<?php echo esc_attr( $name ); ?>__content">
					<?php
						echo $block_object->title( "{$name}__heading", 'h2' );
						echo $block_object->desc( "{$name}__description" );
					?>
					<?php if ( ! empty( $content_more ) ) : ?>
                        <div class="wp-block-button image-text-read-more__more">
                            <div class="section-description image-text-read-more__more-content image-text-read-more__description">
                            	<?php echo wp_kses_post( $content_more ); ?>
							</div>
                            <button class="image-text-read-more__more-btn wp-block-button__link">
                                <span class="open-label"><?php echo $read_more_label ?></span>
                                <span class="close-label"><?php _e( 'Read Less', 'weadapt' ); ?></span>
                                <?php echo get_img( 'icon-chevron-down' ); ?>
                            </button>
                        </div>
                    <?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>