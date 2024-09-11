<div class="popup__header">
	<button class="close" data-popup="comments" aria-label="<?php _e( 'Close', 'weadapt' ); ?>"><?php echo get_img( 'icon-close' ); ?></button>
</div>
<div class="popup__content">
	<?php comments_template(); ?>
</div>