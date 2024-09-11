<div class="row">
	<div class="col-12 col-md-6">
		<p class="ajax-form__field">
			<label for="user-first-name"><?php _e( 'First Name', 'weadapt' ); ?><span class="required">*</span></label>
			<input id="user-first-name" type="text" name="user_first_name" required="required" autocomplete="given-name">
		</p>
	</div>
	<div class="col-12 col-md-6">
		<p class="ajax-form__field">
			<label for="user-last-name"><?php _e( 'Last Name', 'weadapt' ); ?><span class="required">*</span></label>
			<input id="user-last-name" type="text" name="user_last_name" required="required" autocomplete="additional-name">
		</p>
	</div>
	<div class="col-12 col-md-6">
		<p class="ajax-form__field">
			<label for="create-user-name"><?php _e( 'Username', 'weadapt' ); ?><span class="required">*</span></label>
			<input id="create-user-name" type="text" name="user_name" required="required" autocomplete="nickname">
			<span class="description"><?php _e( 'Spaces are allowed; punctuation is not allowed except for periods, hyphens, apostrophes, and underscores.', 'weadapt' ); ?></span>
		</p>
	</div>
	<div class="col-12 col-md-6">
		<p class="ajax-form__field">
			<label for="create-user-email"><?php _e( 'E-mail address', 'weadapt' ); ?><span class="required">*</span></label>
			<input id="create-user-email" type="email" name="user_email" required="required" autocomplete="email">
			<span class="description"><?php _e( 'A valid e-mail address. All e-mails from the system will be sent to this address. The e-mail address is not made public and will only be used if you wish to receive a new password or wish to receive certain news or notifications by e-mail.', 'weadapt' ); ?></span>
		</p>
	</div>
	<div class="col-12 col-md-6">
		<p class="ajax-form__field">
			<label for="user-pass"><?php _e( 'Password', 'weadapt' ); ?><span class="required">*</span></label>
			<input id="user-pass" class="user-pass" type="password" name="user_pass" required="required" autocomplete="current-password">
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
			<label for="user-pass-confirm"><?php _e( 'Confirm Password', 'weadapt' ); ?><span class="required">*</span></label>
			<input id="user-pass-confirm" class="user-pass" type="password" name="user_pass_confirm" required="required" autocomplete="current-password">
		</p>
	</div>
	<div class="col-12 col-md-6">
		<span class="ajax-form__description"><?php _e( 'Provide a password for the new account in both fields.', 'weadapt' ); ?></span>
	</div>
	<div class="col-12 col-md-12">
    <div class="registration__checkbox">
        <label class="registration-form__checkbox">
            <input type="checkbox" name="AGREE_TO_TERMS" value="1" required="">
            <span class="checkbox-label">
			<?php $terms    = get_field( 'terms-policies', 'options' );
				echo wpautop( wp_kses_post( $terms['description'] ), false ); ?>
            </span>
        </label>
    </div>
</div>
	
</div>

<style>
	.checkbox-label{margin-left: 0.9rem;}
@media(max-width: 767px){
.registration__checkbox{
	margin-top: 1rem;
    margin-bottom: 1rem;
    margin-left: 0.5rem;
}

#g-recaptcha{margin-left: 0.5rem;}
}
.registration__checkbox {
    display: flex;
    align-items: center;
}

.registration__checkbox input {
	margin-bottom: auto;
	width: 4% !important;
	display: inline-flex;
}
.registration-form__checkbox{
	display:inline-flex !important;
}
</style>
<?php
	$google_recaptcha_site_key = get_field( 'google_recaptcha_site_key', 'options' );

	if ( ! empty( $google_recaptcha_site_key ) ) {
		echo sprintf( '<div id="g-recaptcha" data-sitekey="%s"></div>', esc_attr( $google_recaptcha_site_key ) );

		?>
			<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>

			<script type="text/javascript">
				var onloadCallback = function() {
					grecaptcha.render('g-recaptcha', {
						'sitekey' : '<?php echo esc_attr( $google_recaptcha_site_key ); ?>'
					});
				};
			</script>
		<?php
	}
?>

<?php wp_nonce_field( 'ajax-create-nonce', 'ajax_create_nonce' ); ?>
<input type="hidden" name="redirect_to" value="<?php echo get_current_url(); ?>">
<input type="hidden" name="action" value="theme_ajax_create">

<div class="wp-block-buttons">
	<div class="wp-block-button">
		<button class="wp-block-button__link" type="submit"><?php esc_html_e( 'Create', 'weadapt' ); ?></button>
	</div>
</div>