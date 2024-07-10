<?php
	$icon         = ! empty( $args['icon'] ) ? $args['icon'] : 0;
	$title        = ! empty( $args['title'] ) ? $args['title'] : '';
	$description  = ! empty( $args['description'] ) ? $args['description'] : '';
	$content_more = ! empty( $args['content_more'] ) ? $args['content_more'] : '';
	$person       = ! empty( $args['person'] ) ? $args['person'] : [];
?>

<div class="awb-card-item">
	<?php
		load_inline_styles( __DIR__, 'awb-card-item' );
		load_blocks_script( 'awb-card-item', 'weadapt/awb-card-item' );
	?>

	<?php if (!empty($icon)) : ?>
        <div class="awb-card-item__icon"><?php echo get_img($icon, 'thumbnail'); ?></div>
    <?php else : ?>
        <div class="awb-card-item__empty-icon"></div>
    <?php endif; ?>

	<?php if ( ! empty( $title ) ) : ?>
		<h3 class="awb-card-item__title"><?php echo wp_kses_post( $title ); ?></h3>
	<?php endif; ?>

	<?php if ( ! empty( $description ) ) : ?>
		<div class="awb-card-item__description"><?php echo wp_kses_post( $description ); ?></div>
	<?php endif; ?>

	<?php if ( ! empty( $content_more ) ) : ?>
		<div class="awb-card-item__more">
		    <?php if ( preg_match('/<a\s+href="([^"]+)"/', $content_more, $matches) ) : ?>
                <a href="<?php echo $matches[1] ?>" class="wp-block-button__link awb-card-item__more-link">
            				<?php _e( 'More', 'weadapt' ); ?>
            				<?php echo get_img( 'icon-arrow-right-button' ); ?>
            	</a>
            <?php else : ?>
               <button class="wp-block-button__link awb-card-item__more-btn">
               				<?php _e( 'More', 'weadapt' ); ?>
               				<?php echo get_img( 'icon-arrow-right-button' ); ?>
               	</button>
            <?php endif; ?>

		</div>

		<div class="awb-card-item__more-content"><?php echo wp_kses_post( $content_more ); ?></div>
	<?php endif; ?>

	<?php if ( ! empty( $person['image'] ) || ! empty( $person['name'] ) || ! empty( $person['position'] ) ) : ?>
		<div class="awb-card-item__person">
			<?php if ( ! empty( $person['image'] ) ) : ?>
				<div class="awb-card-item__person-image">
					<?php echo get_img( $person['image'], 'thumbnail' ); ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $person['name'] ) || ! empty( $person['position'] ) ) : ?>
				<div class="awb-card-item__person-content">
					<?php if ( ! empty( $person['name'] ) ) : ?>
						<h6 class="awb-card-item__person-title"><?php echo $person['name']; ?></h6>
					<?php endif; ?>

					<?php if ( ! empty( $person['position'] ) ) : ?>
						<div class="awb-card-item__person-desc"><?php echo $person['position']; ?></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
