<?php
	$footer_newsletter = get_field( 'footer_newsletter', 'options' );
	$footer_custom_newsletter = get_field( 'footer_custom_code', 'options' );
	$footer_social     = get_field( 'footer_social', 'options' );
	$logo_image        = get_field( 'footer_logo_image', 'options' );
	$logo_url          = get_field( 'footer_logo_url', 'options' );
	$logo_text         = get_field( 'footer_logo_text', 'options' );
	$logo_image_right  = get_field( 'footer_logo_right_image', 'options' );
	$logo_text_right   = get_field( 'footer_logo_right_text', 'options' );

	if ( $footer_newsletter || $footer_social || $logo_image || $logo_url || $logo_text || $logo_image_right || $logo_text_right ) :
?>
<div class="main-footer__area">
	<?php load_inline_dependencies( '/parts/gutenberg/core-heading/', 'core-heading'); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-paragraph/', 'core-paragraph'); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-button/', 'core-button'); ?>

	<div class="row">
		<div class="col-12 col-md-5">
			<?php if ( ! empty( $logo_image_right ) ) : ?>
				<div class="main-footer__logo main-footer__logo--right">
					<?php echo get_img( $logo_image_right ); ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $logo_text_right ) ) : ?>
				<div class="text text--right"><?php echo $logo_text_right; ?></div>
			<?php endif; ?>

			<?php if ( ! empty( $footer_newsletter ) ) : ?>
				<div class="main-footer__newsletter"><?php echo do_shortcode( $footer_newsletter ); ?></div>
			<?php elseif ( ! empty ($footer_custom_newsletter) ): ?>
				<div class="main-footer__newsletter">
					<div class="main-footer__newsletter-custom">
						<?php echo $footer_custom_newsletter; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<div class="col-12 col-md-7">
		<?php

			// Social
			if ( have_rows( 'footer_social', 'options' ) ):
				?><ul class="main-footer__social"><?php

					while( have_rows( 'footer_social', 'options' ) ): the_row();
						$title = get_sub_field( 'title' );
						$url   = get_sub_field( 'url' );

						if ( $title && $url ) :
							?><li><a href="<?php echo esc_url( $url ) ?>" target="_blank"><?php echo $title; ?></a></li><?php
						endif;
					endwhile;

				?></ul><?php
			endif;

			if ( ! empty( $logo_image ) || ! empty( $logo_text ) ) {
				?><div class="main-footer__logo"><?php
					if ( ! empty( $logo_image ) ) {
						if ( ! empty( $logo_url ) ) {
							echo sprintf( '<a href="%s">%s</a>', esc_url( $logo_url ), get_img( $logo_image ) );
						}
						else {
							echo get_img( $logo_image );
						}
					}

					if ( ! empty( $logo_text ) ) {
						?><div class="text"><?php echo $logo_text; ?></div><?php
					}
				?></div><?php
			}

			// Copy
			if ( ! empty( $footer_copy = get_field( 'footer_copy', 'options' ) ) ) {
				?><div class="main-footer__copy"><?php echo $footer_copy; ?></div><?php
			}
		?>
		</div>
	</div>
</div>
<?php
	endif;