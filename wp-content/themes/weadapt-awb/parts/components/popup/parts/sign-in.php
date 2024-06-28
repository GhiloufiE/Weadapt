<div class="popup__header">
	<button class="close" data-popup="sign-in" aria-label="<?php _e( 'Close', 'weadapt' ); ?>"><?php echo get_img( 'icon-close' ); ?></button>
	<h2 class="popup__header__title" id="sign-in"><?php _e( 'Sign in', 'weadapt' ); ?></h2>
</div>
<div class="popup__content">
	<?php echo get_part( 'components/sign-up/index', [ 'template' => 'sign-in' ] ); ?>

	<div class="popup__separator"></div>

	<h2><?php _e( 'Sign up!', 'weadapt' ); ?></h2>
	<?php the_field( 'popup_sign_in', 'options' ); ?>

	<div class="wp-block-buttons">
		<div class="wp-block-button">
			<button data-popup="create-account" class="wp-block-button__link create-account"><?php esc_html_e( 'Create an account', 'weadapt' ); ?></button>
		</div>
	</div>
</div>
