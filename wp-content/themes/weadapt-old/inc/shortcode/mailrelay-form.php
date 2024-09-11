<?php

add_shortcode( 'mailrelay_form', function( $atts ) {
	$atts = shortcode_atts( array(
		'url'   => '',
		'type'  => 'footer',
		'title' => __( 'Join the community', 'weadapt' )
	), $atts );

	if ( empty( $atts['url'] ) ) {
		return;
	}
	?>
	<form class="simple_form form form-vertical mailrelay-form" action="<?php echo esc_url( $atts['url'] ); ?>" accept-charset="UTF-8" method="post">
		<?php if ( ! empty( $atts['title'] ) ) : ?>
			<h2><?php echo $atts['title']; ?></h2>
		<?php endif; ?>
		<div class="mailrelay-form__content">
			<?php if ( $atts['type'] === 'footer' ) : ?>
				<div class="form-group email optional subscriber_email">
					<input class="form-control string email optional" placeholder="<?php _e( 'Enter your email', 'weadapt' ); ?>" type="email" name="subscriber[email]" id="subscriber_email" />
				</div>
				<div class="submit-wrapper">
					<input type="submit" name="commit" value="<?php _e( 'Register now', 'weadapt' ); ?>" class="btn btn-primary" data-disable-with="Processing" />
				</div>
			<?php elseif ( $atts['type'] === 'maia-subscribe' ) : ?>
				<input type="text" name="anotheremail" id="anotheremail" style="position: absolute; left: -5000px" tabindex="-1" autocomplete="new-password" />
				<div class="form-group string optional subscriber_name">
					<label class="control-label string optional" for="subscriber_name"><?php _e( 'Name', 'weadapt' ); ?></label>
					<input class="form-control string optional" type="text" name="subscriber[name]" id="subscriber_name" />
				</div>
				<div class="form-group email required subscriber_email">
					<label class="control-label email required" for="subscriber_email"><?php _e( 'Email*', 'weadapt' ); ?></label>
					<input class="form-control string email required" type="email" name="subscriber[email]" id="subscriber_email"  required="required" aria-required="true" />
				</div>
				<div class="form-group string optional subscriber_address">
					<label class="control-label string optional" for="subscriber_address"><?php _e( 'Organization name', 'weadapt' ); ?></label>
					<input class="form-control string optional" type="text" name="subscriber[address]" id="subscriber_address" />
				</div>
				<div class="form-group boolean required subscriber_subscribed_with_acceptance">
					<div class="checkbox">
						<input value="0" autocomplete="off" type="hidden" name="subscriber[subscribed_with_acceptance]" />
						<label class="boolean required" for="subscriber_subscribed_with_acceptance"><input class="boolean required" required="required" aria-required="true" type="checkbox" value="1" name="subscriber[subscribed_with_acceptance]" id="subscriber_subscribed_with_acceptance" /><abbr title="required"></abbr><?php _e( 'I accept the <a href="/privacy-policy/" target="_blank">privacy policy</a>.', 'weadapt' ); ?></label>
					</div>
				</div>
				<div class="custom-text-wrapper">
					<?php _e( 'By subscribing, you´ll receive our newsletter with the latest MAIA insights, news, articles and events. You can change your mind at any time by clicking the unsubscribe link in the footer of any newsletter you receive from us, or by contacting us at <a href="mailto:privacymaia@maia-project.eu">privacymaia@maia-project.eu</a>.', 'weadapt' ); ?>
				</div>
				<div>
					<script src="https://www.recaptcha.net/recaptcha/api.js" async defer ></script>
					<div data-sitekey="6LfxSlQUAAAAAE2wMx0128AjWWFXJoBkiNQn2m8m" class="g-recaptcha "></div>
					<noscript>
						<div>
							<div style="width: 302px; height: 422px; position: relative;">
								<div style="width: 302px; height: 422px; position: absolute;">
									<iframe src="https://www.recaptcha.net/recaptcha/api/fallback?k=6LfxSlQUAAAAAE2wMx0128AjWWFXJoBkiNQn2m8m" name="ReCAPTCHA" style="width: 302px; height: 422px; border-style: none; border: 0; overflow: hidden;"></iframe>
								</div>
							</div>
							<div style="width: 300px; height: 60px; border-style: none;bottom: 12px; left: 25px; margin: 0px; padding: 0px; right: 25px;background: #FFFFFF; border: 0; border-radius: 5px;">
								<textarea id="g-recaptcha-response" name="g-recaptcha-response"class="g-recaptcha-response"style="width: 250px; height: 40px; border: 0; margin: 10px 25px; padding: 0px; resize: none;"></textarea>
							</div>
						</div>
					</noscript>
					<div class="submit-wrapper">
						<input type="submit" name="commit" value="<?php _e( 'Subscribe now', 'weadapt' ); ?>" class="btn btn-primary" data-disable-with="Processing" />
					</div>
				</div>
			<?php endif; ?>
		</div>
	</form>
	<?php
} );