<?php
/**
 * Logos Block Adaptation at Altitude
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$attr = $block_object->attr();
$name = $block_object->name();
$columnOrder = get_field( 'column_order' );
if( is_countable(get_field('logos')) ) {
    $countCards = count(get_field('logos'));
} else {
    $countCards = 0;
}
?>

<section <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-heading/', 'core-heading'); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-paragraph/', 'core-paragraph'); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-button/', 'core-button'); ?>

	<div class="container <?php echo $name; ?>__content order-<?php echo $columnOrder; ?>">
        <div class="<?php echo $name; ?>__container-content">
            <?php if ( ! empty( $main_title = get_field( 'main_title' ) ) ) :
				echo $block_object->title('cards-download__heading', 'h2'); ?>

                <h2 class="<?php echo $name; ?>__title section-title"><?php echo $main_title; ?></h2>
            <?php endif; ?>
            <?php if ( ! empty( $main_description = get_field( 'main_description' ) ) ) : ?>
                <div class="<?php echo $name; ?>__description section-description">
                    <?php echo $main_description; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php
            $cta_title = get_field( 'cta_title' );
            $cta_description = get_field( 'cta_description' );
            $cta_button = get_field( 'section_button' );
        ?>

        <?php if( !empty($cta_title) || !empty($cta_description) || !empty($cta_button) ) : ?>
          <div class="<?php echo $name; ?>__container__cta">
                    <?php if ( ! empty( $cta_title ) ) : ?>
                        <h2 class="<?php echo $name; ?>__container__cta-title section-title"><?php echo $cta_title; ?></h2>
                    <?php endif; ?>
                    <?php if ( ! empty( $cta_description ) ) : ?>
                        <div class="<?php echo $name; ?>__container__cta-description section-description">
                            <?php echo $cta_description; ?>
                        </div>
                    <?php endif; ?>
                    <?php echo $block_object->button('', "{$name}__button", 'icon-arrow-right-button'); ?>
                </div>
        <?php endif; ?>
	</div>

	<div class="<?php echo $name; ?>__container container" style="--var-count-cards: <?php echo $countCards ?>">
		<?php if ( have_rows( 'logos' ) ): ?>
			<div class="row <?php echo $name; ?>__row">
				<?php while ( have_rows( 'logos' ) ) : the_row();
					$image_id = get_sub_field( 'logo', 'medium' );
					$image_link = get_sub_field( 'logo_hyperlink' );

					if ( ! empty( $image_id ) ) : ?>
						<div class="<?php echo $name; ?>__col">
							<?php if ( ! empty( $image_link ) ) : ?>
								<a href="<?php echo $image_link; ?>" target="_blank">
							<?php endif; ?>
								<span>
									<?php echo get_img( $image_id ); ?>
								</span>
							<?php if ( ! empty( $image_link ) ): ?>
								</a>
							<?php endif; ?>
						</div>
					<?php endif;
				endwhile;
				while ( have_rows( 'logos' ) ) : the_row();
					$image_id = get_sub_field( 'logo', 'medium' );
					$image_link = get_sub_field( 'logo_hyperlink' );

					if ( ! empty( $image_id ) ) : ?>
						<div class="<?php echo $name; ?>__col">
							<?php if ( ! empty( $image_link ) ) : ?>
								<a href="<?php echo $image_link; ?>" target="_blank">
							<?php endif; ?>
								<span>
									<?php echo get_img( $image_id ); ?>
								</span>
							<?php if ( ! empty( $image_link ) ) : ?>
								</a>
							<?php endif; ?>
						</div>
					<?php endif;
				endwhile; ?>
			</div>
		<?php endif; ?>
	</div>
</section>