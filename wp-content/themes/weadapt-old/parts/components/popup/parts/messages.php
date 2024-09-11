<div class="popup__header">
	<button class="close" data-popup="messages" aria-label="<?php _e( 'Close', 'weadapt' ); ?>"><?php echo get_img( 'icon-close' ); ?></button>
</div>
<div class="popup__content" data-nonce="<?php echo wp_create_nonce( 'view-messages' ); ?>"></div>
<div class="popup__loader"><?php echo get_img( 'loader' ); ?></div>