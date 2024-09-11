<?php
	$user = ! empty( $args['user'] ) ? $args['user'] : null;

	if ( ! empty( $user ) ) :
		$user_ID = $user->ID;
?>

<div class="edit-profile__item">
	<h4 class="edit-profile__title"><?php _e( 'Personal details', 'weadapt' ); ?></h4>

	<div class="row">
		<div class="col-12 col-md-6">
			<label for="account-twitter-url"><?php _e( 'Twitter url', 'weadapt' ); ?></label>
			<input id="account-twitter-url" type="url" name="twitter_url" value="<?php the_field( 'twitter_url', "user_$user_ID" ); ?>">
		</div>

		<div class="col-12 col-md-6">
			<label for="account-instagram-url"><?php _e( 'Instagram url', 'weadapt' ); ?></label>
			<input id="account-instagram-url" type="url" name="instagram_url" value="<?php the_field( 'instagram_url', "user_$user_ID" ); ?>">
		</div>

		<div class="col-12 col-md-6">
			<label for="account-website-url"><?php _e( 'Website url', 'weadapt' ); ?></label>
			<input id="account-website-url" type="url" name="website_url" value="<?php the_field( 'website_url', "user_$user_ID" ); ?>">
		</div>

		<div class="col-12 col-md-6">
			<label for="account-linkedin-url"><?php _e( 'LinkedIn url', 'weadapt' ); ?></label>
			<input id="account-linkedin-url" type="url" name="linkedin_url" value="<?php the_field( 'linkedin_url', "user_$user_ID" ); ?>">
		</div>
	</div>
</div>

<?php endif; ?>