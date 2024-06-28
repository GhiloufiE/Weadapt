<p class="ajax-form__field">
	<label for="forgot-user-name"><?php _e( 'Username or e-mail address', 'weadapt' ); ?><span class="required">*</span></label>
	<input id="forgot-user-name" type="text" name="user_name" required="required" autocomplete="email">
	<span class="description"><?php _e( 'You may login with either your assigned username or your e-mail address.', 'weadapt' ); ?></span>
</p>

<?php wp_nonce_field( 'ajax-forgot-nonce', 'ajax_forgot_nonce' ); ?>
<input type="hidden" name="action" value="theme_ajax_forgot">

<div class="wp-block-buttons">
	<div class="wp-block-button">
		<button class="wp-block-button__link" type="submit"><?php esc_html_e( 'Email new password', 'weadapt' ); ?></button>
	</div>
</div>