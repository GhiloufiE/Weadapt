<?php if ( 'publish' === get_post_status() ) : ?>
<div class="archive-main__published">
	<span class="archive-main__published-icon"><?php echo get_img('icon-calendar'); ?></span>
	<time class="archive-main__published-text" datetime="<?php echo get_the_date('Y-m-d'); ?>">
		<?php _e('Published', 'weadapt'); ?>: <?php echo get_the_date(); ?>
	</time>
</div>
<?php endif; ?>