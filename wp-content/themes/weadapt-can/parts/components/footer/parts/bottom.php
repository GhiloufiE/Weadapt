<?php
	$logo_image        = get_field( 'footer_logo_image', 'options' );
	$logo_url          = get_field( 'footer_logo_url', 'options' );
	$logo_text         = get_field( 'footer_logo_text', 'options' );
?>

<div class="main-footer__area">
	<div class="row">
		<div class="col-12 col-md-6">
            <?php if ( is_active_sidebar( "footer-area-4" ) ) : ?>
				<?php dynamic_sidebar( "footer-area-4" ); ?>
			<?php endif; ?>
        </div>

		<div class="col-12 col-md-6">
			<?php
				if ( ! empty( $logo_image ) || ! empty( $logo_text ) ) { ?>
					<div class="main-footer__logo">
						<?php
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
						?>
					</div>
				<?php }
			?>
		</div>
	</div>
</div>