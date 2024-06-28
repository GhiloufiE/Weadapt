<?php
	$user = ! empty( $args['user'] ) ? $args['user'] : null;

	if ( ! empty( $user ) ) :
		$user_ID = $user->ID;

		$args = [
			'post_status'    => ['publish', 'draft'],
			'post_type'      => 'organisation',
			'posts_per_page' => -1,
		];

		$query = new WP_Query( $args );

		$user_organisation = ! empty( get_field( 'organisations', "user_$user_ID" ) ) ? get_field( 'organisations', "user_$user_ID" ) : [];

		$user_timezone    = get_field( 'timezone', "user_$user_ID" );
		$timezone         = get_field_object( 'field_64a69d1ede07f' );
		$timezone_choices = ! empty( $timezone ) ? $timezone['choices'] : [];

		$user_country    = get_field( 'address_country', "user_$user_ID" );
		$country         = get_field_object( 'field_6437a20cbbc21' );
		$country_choices = ! empty( $country ) ? $country['choices'] : [];
?>

<div class="edit-profile__item">
	<h4 class="edit-profile__title"><?php _e( 'Main settings', 'weadapt' ); ?></h4>

	<div class="row">
		<div class="col-12 col-md-6">
			<label for="account-first-name"><?php _e( 'First Name', 'weadapt' ); ?></label>
			<input id="account-first-name" type="text" name="first_name" value="<?php echo $user->user_firstname; ?>">
		</div>

		<div class="col-12 col-md-6">
			<label for="account-last-name"><?php _e( 'Last Name', 'weadapt' ); ?></label>
			<input id="account-last-name" type="text" name="last_name" value="<?php echo $user->user_lastname; ?>">
		</div>

		<div class="col-12">
			<label for="account-current-password"><?php _e( 'Current password', 'weadapt' ); ?></label>
			<input id="account-current-password" type="password" name="current_password">
			<span class="description">
				<?php _e( 'Enter your current password to change the E-mail address or Password.', 'weadapt' ); ?>
				<a data-popup href="#forgot-password"><?php _e( 'Request new password', 'weadapt' ); ?></a>
			</span>
		</div>

		<div class="col-12">
			<label for="account-email-address"><?php _e( 'E-mail address', 'weadapt' ); ?><span class="required">*</span></label>
			<input id="account-email-address" type="email" name="email_address" required="required" value="<?php echo $user->user_email; ?>">
			<span class="description"><?php _e( 'A valid e-mail address. All e-mails from the system will be sent to this address. The e-mail address is not made public and will only be used if you wish to receive a new password or wish to receive certain news or notifications by e-mail.', 'weadapt' ); ?></span>
		</div>

		<div class="col-12 col-md-6">
			<label for="account-new-password"><?php _e( 'Password', 'weadapt' ); ?></label>
			<input id="account-new-password" type="password" name="new_password">
		</div>

		<div class="col-12 col-md-6">
			<label for="account-new-confirm-password"><?php _e( 'Confirm Password', 'weadapt' ); ?></label>
			<input id="account-new-confirm-password" type="password" name="new_confirm_password">
		</div>

		<div class="col-12 col__description">
			<span class="description"><?php _e( 'To change the current user password, enter the new password in both fields.', 'weadapt' ); ?></span>
		</div>
	</div>
</div>

<?php if ( $query->have_posts() ): ?>
	<div class="edit-profile__item">
		<h4 class="edit-profile__title"><?php _e( 'Organisation', 'weadapt' ); ?></h4>

		<div class="row">
			<div class="col-12">
				<div class="theme-select-wrap">
					<select multiple name="organisation[]">
						<?php while ( $query->have_posts() ) :
							$query->the_post();
							$ID = get_the_ID();

							$is_selected = in_array( $ID, $user_organisation ) ? 'selected' : null;
						?>
							<option <?php echo $is_selected; ?> value="<?php echo $ID; ?>"><?php the_title(); ?></option>
						<?php endwhile; ?>
					</select>

					<div class="theme-select"></div>
				</div>
			</div>
		</div>
		<span class="description"><a href="<?php echo admin_url( 'post-new.php?post_type=organisation' ); ?>" target="_blank"><?php _e( 'Add new organisation', 'weadapt' ); ?></a></span>
	</div>
<?php endif; wp_reset_postdata(); ?>

<div class="edit-profile__item">
	<h4 class="edit-profile__title"><?php _e( 'Address', 'weadapt' ); ?></h4>

	<div class="row">
		<?php if ( ! empty( $country_choices ) ) : ?>
			<div class="col-12">
				<div class="theme-select-wrap">
					<label for="account-country"><?php _e( 'Country', 'weadapt' ); ?></label>

					<select name="country" id="account-country">
						<option selected value="default"><?php _e( 'Choose an option', 'weadapt' ); ?></option>

						<?php foreach ( $country_choices as $choice_key => $choice_value ) :
							$is_selected = false;

							if ( ! empty( $user_country ) ) {
								$is_selected = $user_country['value'] === $choice_key ? 'selected' : null;
							}
						?>
							<option <?php echo $is_selected; ?> value="<?php echo $choice_key . '||' . $choice_value; ?>"><?php echo $choice_value; ?></option>
						<?php endforeach; ?>
					</select>

					<div class="theme-select"></div>
				</div>
			</div>
		<?php endif; ?>

		<div class="col-12 col-md-6">
			<label for="account-town-city"><?php _e( 'Town/City', 'weadapt' ); ?></label>
			<input id="account-town-city" type="text" name="town_city" value="<?php the_field( 'address_city', "user_$user_ID" ); ?>">
		</div>

		<div class="col-12 col-md-6">
			<label for="account-county"><?php _e( 'County', 'weadapt' ); ?></label>
			<input id="account-county" type="text" name="county" value="<?php the_field( 'address_county', "user_$user_ID" ); ?>">
		</div>
	</div>
</div>

<div class="edit-profile__item">
	<h4 class="edit-profile__title"><?php _e('Contact settings', 'weadapt'); ?></h4>

	<div class="row">
		<div class="col-12">
			<label for="account-contact-form" class="true-false-button" tabindex="0">
				<input type="checkbox" id="account-contact-form" name="contact_form"<?php echo get_field( 'contact_form', "user_$user_ID" ) ? ' checked' : null; ?>>
				<span class="icon"></span>
				<?php _e( 'Personal contact form', 'weadapt' ); ?>
			</label>

			<?php
				$contact_form_instructions = get_field_object( 'field_6437a5be304b4' );

				if ( ! empty( $contact_form_instructions ) ) : ?>
					<span class="description"><?php echo $contact_form_instructions['instructions']; ?></span>
				<?php endif;
			?>
		</div>
	</div>
</div>

<?php if ( ! empty( $timezone_choices ) ) : ?>
	<div class="edit-profile__item">
		<h4 class="edit-profile__title"><?php _e( 'Locale settings', 'weadapt' ); ?></h4>

		<div class="row">
			<div class="col-12">
				<div class="theme-select-wrap">
					<select name="timezone">
						<option selected value="default"><?php _e( 'Choose an option', 'weadapt' ); ?></option>

						<?php foreach ( $timezone_choices as $choice_key => $choice_value ) :
							$is_selected = false;

							if ( ! empty( $user_timezone ) ) {
								$is_selected = $user_timezone['value'] === $choice_key ? 'selected' : null;
							}
						?>
							<option <?php echo $is_selected; ?> value="<?php echo $choice_key . '||' . $choice_value; ?>"><?php echo $choice_value; ?></option>
						<?php endforeach; ?>
					</select>

					<div class="theme-select"></div>
				</div>

				<?php
					$timezone_instructions = get_field_object( 'field_64a69d1ede07f' );

					if ( ! empty( $timezone_instructions ) ) : ?>
						<span class="description"><?php echo $timezone_instructions['instructions']; ?></span>
					<?php endif;
				?>
			</div>
		</div>
	</div>
<?php endif; ?>

<div class="edit-profile__item">
	<h4 class="edit-profile__title"><?php _e( 'Email settings', 'weadapt' ); ?></h4>

	<div class="row">
		<div class="col-12">
			<label for="account-mimemail-textonly" class="true-false-button" tabindex="0">
				<input type="checkbox" id="account-mimemail-textonly" name="mimemail_textonly"<?php echo get_field( 'mimemail_textonly', "user_$user_ID" ) ? ' checked' : null; ?>>
				<span class="icon"></span>
				<?php _e( 'Plaintext email only', 'weadapt' ); ?>
			</label>

			<?php
				$mimemail_textonly_instructions = get_field_object( 'field_6437a60f304b5' );

				if ( ! empty( $mimemail_textonly_instructions ) ) : ?>
					<span class="description"><?php echo $mimemail_textonly_instructions['instructions']; ?></span>
				<?php endif;
			?>
		</div>
	</div>
</div>

<div class="edit-profile__item">
	<h4 class="edit-profile__title"><?php _e('Receive weADAPT newsletter?', 'weadapt'); ?></h4>

	<div class="row">
		<div class="col-12">
			<label for="account-newsletter" class="true-false-button" tabindex="0">
				<input type="checkbox" id="account-newsletter" name="newsletter"<?php echo get_field( 'newsletter', "user_$user_ID" ) ? ' checked' : null; ?>>
				<span class="icon"></span>
				<?php _e( 'Receive weADAPT newsletter?', 'weadapt' ); ?>
			</label>

			<?php
				$newsletter_instructions = get_field_object( 'field_6437acceadbf7' );

				if ( ! empty( $newsletter_instructions ) ) : ?>
					<span class="description"><?php echo $newsletter_instructions['instructions']; ?></span>
				<?php endif;
			?>
		</div>
	</div>
</div>

<div class="edit-profile__item">
	<h4 class="edit-profile__title"><?php _e('Privacy Policy and Terms of Use', 'weadapt'); ?></h4>

	<div class="row">
		<div class="col-12">
			<label for="account-agreement" class="true-false-button" tabindex="0">
				<input type="checkbox" id="account-agreement" name="agreement"<?php echo get_field( 'agreement', "user_$user_ID" ) ? ' checked' : null; ?>>
				<span class="icon"></span>
				<?php _e( 'I have read and accepted the Privacy Policy and Terms of Use.', 'weadapt' ); ?>
			</label>

			<?php
				$agreement_instructions = get_field_object( 'field_6437ac123db74' );

				if ( ! empty( $agreement_instructions ) ) : ?>
					<span class="description"><?php echo $agreement_instructions['instructions']; ?></span>
				<?php endif;
			?>
		</div>
	</div>
</div>

<?php endif; ?>