<div class="popup__header">
	<button class="back" data-popup="sign-in" aria-label="<?php _e( 'Back', 'weadapt' ); ?>"><?php echo get_img( 'icon-arrow-left' ); ?></button>
	<button class="close" data-popup="forgot-password" aria-label="<?php _e( 'Close', 'weadapt' ); ?>"><?php echo get_img( 'icon-close' ); ?></button>
	<h2 class="popup__header__title" id="forgot-password"><?php _e( 'Request new password', 'weadapt' ); ?></h2>
</div>
<div class="popup__content">
	<?php the_field( 'popup_request_new_password', 'options' ); ?>

	<?php if ( $quote = get_field( 'popup_request_new_password_quote', 'options' ) ) : ?>
		<div class="popup__quote">
			<?php echo get_img( 'icon-alert-circle' ); ?>
			<?php the_field( 'popup_request_new_password_quote', 'options' ); ?>
		</div>
	<?php endif; ?>

	<?php echo get_part( 'components/sign-up/index', [ 'template' => 'forgot-password' ] ); ?>
</div>