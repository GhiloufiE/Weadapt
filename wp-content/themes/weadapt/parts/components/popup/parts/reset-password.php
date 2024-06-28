<?php
	$is_active     = ! empty( $args['is_active'] ) ? wp_validate_boolean( $args['is_active'] ) : false;
	$button_classes = [ 'close' ];

	if ( $is_active ) {
		$button_classes[] = 'active';
	}
?>
<div class="popup__header">
	<button class="<?php echo implode( ' ', $button_classes ); ?>" data-popup="forgot-password" aria-label="<?php _e( 'Close', 'weadapt' ); ?>"><?php echo get_img( 'icon-close' ); ?></button>
	<h2 class="popup__header__title" id="forgot-password"><?php _e( 'Update Your password', 'weadapt' ); ?></h2>
</div>
<div class="popup__content">
	<?php the_field( 'popup_update_your_password', 'options' ); ?>

	<?php echo get_part( 'components/sign-up/index', [ 'template' => 'reset-password' ] ); ?>
</div>