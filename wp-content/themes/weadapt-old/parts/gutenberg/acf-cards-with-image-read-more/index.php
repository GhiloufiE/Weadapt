<?php
/**
 * Cards With Image Read More
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
	<?php load_inline_dependencies( '/parts/gutenberg/core-heading/', 'core-heading'); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-paragraph/', 'core-paragraph'); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-button/', 'core-button'); ?>

	<div class="container">
		<?php echo $block_object->title( "{$name}__heading" ); ?>

		<?php if ( have_rows( 'cards' ) ) : ?>
			<div class="row <?php echo esc_attr( $name ); ?>__row">
				<?php while ( have_rows( 'cards' ) ) : the_row(); ?>
                    <div class="col-12 col-md-6 <?php echo esc_attr( $name ); ?>__col">
                        <div class="card-with-image">
                            <?php if ( ! empty( $image = get_sub_field( 'image' ) ) ) : ?>
                                <div class="card-with-image__image">
                                    <?php echo get_img( $image, 'large' ); ?>
                                </div>
                            <?php endif; ?>

                            <div class="card-with-image__content">
                                <?php if ( ! empty( $title = get_sub_field( 'title' ) ) ) : ?>
                                    <h4 class="card-with-image__title">
                                        <?php echo esc_html( $title ); ?>
                                    </h4>
                                <?php endif; ?>

                                <?php if ( ! empty( $description = get_sub_field( 'description' ) ) ) : ?>
                                    <div class="card-with-image__description">
                                        <?php echo wp_kses_post( $description ); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ( ! empty( $content_more = get_sub_field( 'content_more' ) ) ) :
                                    $read_more_label = get_sub_field( 'read_more_label' ) ?: _e( 'Read more', 'weadapt' );
                                ?>
                                    <div class="wp-block-button cards-with-image-read-more__more">
                                        <div class="cards-with-image-read-more__more-content card-with-image__description"><?php echo wp_kses_post( $content_more ); ?></div>
                                        <button class="cards-with-image-read-more__more-btn wp-block-button__link">
                                           <span class="open-label"><?php echo $read_more_label ?></span>
										   <span class="close-label"><?php _e( 'Read Less', 'weadapt' ); ?></span>
                                            <?php echo get_img( 'icon-chevron-down' ); ?>
                                        </button>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>
	</div>
</section>