<div class="popup__header">
	<button class="close" data-popup="sign-out" aria-label="<?php _e( 'Close', 'weadapt' ); ?>"><?php echo get_img( 'icon-close' ); ?></button>
	<h2 class="popup__header__title" id="sign-out"><?php _e( 'Sign out', 'weadapt' ); ?></h2>
	<?php the_field( 'popup_sign_out', 'options' ); ?>
</div>
<div class="popup__content">
	<div class="wp-block-buttons">
		<?php
			echo get_button( [
				'url'   => '#sign-out',
				'title' => __( 'Cancel', 'weadapt' )
			], 'outline', '', '', true );

			echo get_button( [
				'url'   => wp_logout_url( get_current_url() ),
				'title' => __( 'Sign out', 'weadapt' )
			] );
		?>
	</div>
</div>