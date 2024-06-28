<div class="popup__header">
	<button class="close" data-popup="messages-new" aria-label="<?php _e( 'Close', 'weadapt' ); ?>"><?php echo get_img( 'icon-close' ); ?></button>
</div>
<div class="popup__content">
	<?php require_once( fep_locate_template( 'form-message.php' ) ); ?>
</div>