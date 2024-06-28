<?php
/**
 * Block List with images
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$attr = $block_object->attr( 'background-' . esc_attr( get_field( 'background_color' ) ) );
$name = $block_object->name();
?>

<section <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-heading/', 'core-heading'); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-paragraph/', 'core-paragraph'); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-button/', 'core-button'); ?>

	<div class="container">
		    <?php if ( have_rows( 'list_items' ) ) : ?>
		        <?php while ( have_rows( 'list_items' ) ) : the_row(); ?>
                    <div class="row <?php echo esc_attr( $name ); ?>__row">
                        <?php
                            $list_image = get_sub_field( 'section_image' );
                            $list_title = get_sub_field( 'section_title' );
                            $list_description = get_sub_field( 'section_description' );
                            $list_image_position = get_sub_field( 'image_position' );
                            $list_hyperlink = get_sub_field( 'item_hyperlink' );
                        ?>
                        <div class="col-12 col-md-6 <?php echo esc_attr( $name ); ?>__col <?php echo esc_attr( $name ); ?>__col--image <?php echo esc_attr( $list_image_position); ?>">
                            <?php if ( ! empty( $list_image ) ) : ?>
                            	<?php if ( ! empty( $list_hyperlink ) ) : ?>
									<a href="<?php echo esc_url( $list_hyperlink ); ?>" target="_blank" class="<?php echo esc_attr( $name ); ?>__col--image-container__link">
								<?php endif; ?>
                                <div class="<?php echo esc_attr( $name ); ?>__col--image-container">
									<?php echo get_img( $list_image, 'large' ); ?>
                                </div>
                                 <?php if ( ! empty( $list_hyperlink ) ) : ?>
									</a>
								<?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <div class="col-12 col-md-6 <?php echo esc_attr( $name ); ?>__col <?php echo esc_attr( $name ); ?>__col--text">
                            <div class="<?php echo esc_attr( $name ); ?>__content">
                                <?php if ( ! empty( $list_title ) ) : ?>
                                    <h3 class="<?php echo esc_attr( $name ); ?>__title">
                                        <?php echo esc_html( $list_title ); ?>
                                    </h3>
                                <?php endif; ?>
                                <?php if ( ! empty( $list_description ) ) : ?>
                                    <div class="<?php echo esc_attr( $name ); ?>__description section-description">
                                      <?php echo wp_kses_post( $list_description ); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
		        <?php endwhile; ?>
		   <?php endif; ?>
	</div>
</section>