<div class="popup__header">
	<button class="close" data-popup="newsletter" aria-label="<?php _e( 'Close', 'weadapt' ); ?>"><?php echo get_img( 'icon-close' ); ?></button>

    <?php if ( ! empty( $content = get_field( 'popup_newsletter', 'options' ) ) ) : 
        echo $content;
    endif; ?>
</div>

<div class="popup__content">
    newsletter
</div>