<div class="row">
	<div class="col-12 col-md-6">
		<p class="ajax-form__field">
			<label for="user-reset-pass"><?php _e( 'Password', 'weadapt' ); ?><span class="required">*</span></label>
			<input id="user-reset-pass" class="user-pass" type="password" name="user_pass" required="required" autocomplete="current-password">
		</p>
	</div>
	<div class="col-12 col-md-6">
		<div class="ajax-form__pass-strength ajax-form__description">
			<div class="ajax-form__pass-strength__line">
				<?php _e( 'Password strength:', 'weadapt' ); ?>
				<span></span>
			</div>
			<div class="ajax-form__pass-strength__status"></div>
		</div>
	</div>
	<div class="col-12 col-md-6">
		<p class="ajax-form__field">
			<label for="user-reset-pass-confirm"><?php _e( 'Confirm Password', 'weadapt' ); ?><span class="required">*</span></label>
			<input id="user-reset-pass-confirm" class="user-pass" type="password" name="user_pass_confirm" required="required" autocomplete="current-password">
		</p>
	</div>
	<div class="col-12 col-md-6">
		<span class="ajax-form__description"><?php _e( 'Provide a password for the new account in both fields.', 'weadapt' ); ?></span>
	</div>
</div>

<?php wp_nonce_field( 'ajax-reset-nonce', 'ajax_reset_nonce' ); ?>
<input type="hidden" name="user_login" value="<?php echo ( ! empty( $_GET['login'] ) ) ? $_GET['login'] : ''; ?>">
<input type="hidden" name="user_key" value="<?php echo ( ! empty( $_GET['key'] ) ) ? $_GET['key'] : ''; ?>">
<input type="hidden" name="action" value="theme_ajax_reset">

<div class="wp-block-buttons">
	<div class="wp-block-button">
		<button class="wp-block-button__link" type="submit"><?php esc_html_e( 'Update', 'weadapt' ); ?></button>
	</div>
</div>