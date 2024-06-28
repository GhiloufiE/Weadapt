<p class="ajax-form__field">
	<label for="sign-in-user-name"><?php _e( 'Username or e-mail address', 'weadapt' ); ?><span class="required">*</span></label>
	<input id="sign-in-user-name" type="text" name="user_name" required="required" autocomplete="email">
	<span class="description"><?php _e( 'You may login with either your assigned username or your e-mail address.', 'weadapt' ); ?></span>
</p>
<p class="ajax-form__field">
	<label for="current-password"><?php _e( 'Password', 'weadapt' ); ?><span class="required">*</span></label>
	<input id="current-password" type="password" name="user_pass" required="required" autocomplete="current-password">
	<span class="description"><?php _e( 'The password field is case sensitive.', 'weadapt' ); ?></span>
</p>
<p class="ajax-form__url">
	<a href="#forgot-password" data-popup><?php _e( 'Forgotten password? Request new password here', 'weadapt' ); ?></a>
</p>

<?php wp_nonce_field( 'ajax-login-nonce', 'ajax_login_nonce' ); ?>
<input type="hidden" name="redirect_to" value="<?php echo get_current_url(); ?>">
<input type="hidden" name="action" value="theme_ajax_login">

<div class="wp-block-buttons">
	<div class="wp-block-button is-style-outline">
		<button class="wp-block-button__link" type="submit"><?php esc_html_e( 'Login', 'weadapt' ); ?></button>
	</div>
</div>