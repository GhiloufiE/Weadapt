<div class="popup__header">
	
	<button class="close" data-popup="org-creation"
		aria-label="<?php _e('Close', 'weadapt'); ?>"><?php echo get_img('icon-close'); ?></button>
	<h2 class="popup__header__title" id="forgot-password"><?php _e('Organisation Creation', 'weadapt'); ?></h2>
</div>
<div class="popup__content">

	<div class="popup__quote">
		<?php echo get_img('icon-alert-circle'); ?>
		<svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path
				d="M10.99 15H11M11 21C16.5228 21 21 16.5228 21 11C21 5.47715 16.5228 1 11 1C5.47715 1 1 5.47715 1 11C1 16.5228 5.47715 21 11 21ZM10.995 7L11 12.4993L10.995 7Z"
				stroke="currentColor" stroke-linecap="square"></path>
		</svg>
		<p>When you add your organisation you will be redirected to an interface to fill the necessary informations.</p>
	</div>
	<div class="wp-block-buttons">
	<div class="wp-block-button">
		<button class="wp-block-button__link" id="proceed-to-creation"><?php esc_html_e('Proceed To Creation', 'weadapt'); ?></button>
	</div>
	<div class="wp-block-button">
		<button class="wp-block-button__link" id="maybe-later"><?php esc_html_e('Maybe Later', 'weadapt'); ?></button>
	</div>
</div>

</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('proceed-to-creation').addEventListener('click', function() {
        const redirectUrl = "<?php echo home_url('wp-admin/post-new.php?post_type=organisation'); ?>";
        window.location.href = redirectUrl;
    });

    document.getElementById('maybe-later').addEventListener('click', function() {
        const popup = document.querySelector('[data-popup="org-creation"]');
        if (popup) {
            popup.closest('.popup__content').style.display = 'none';
        }
    });
});
</script>



<?php echo get_part('components/sign-up/index', ['template' => 'org-creation']); ?>
</div>