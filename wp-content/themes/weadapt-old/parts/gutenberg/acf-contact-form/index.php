<?php
/**
 * Contact Form Block
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$attr = $block_object->attr();
$name = $block_object->name();
?>

<section <?php echo $attr; ?>>
	<?php echo load_inline_styles_shared( 'forms' ); ?>
	<?php echo load_inline_styles( __DIR__, $name ); ?>

	<div class="container">
		<div class="row <?php echo esc_attr( $name ); ?>__row">
			<div class="<?php echo esc_attr( $name ); ?>__col <?php echo esc_attr( $name ); ?>__col--form">
				<?php if ( ! empty( $shortcode = get_field( 'shortcode' ) ) ) : ?>
					<div class="cf7-form" data-i18n="<?php _e( 'cannot be empty', 'weadapt' ); ?>">
						<?php echo do_shortcode( $shortcode ); ?>
						<?php
                            $google_recaptcha_site_key = get_field( 'google_recaptcha_site_key', 'options' );

                            if ( ! empty( $google_recaptcha_site_key ) ) {
                                echo sprintf( '<div id="g-recaptcha" data-sitekey="%s" data-callback="enableBtn"></div>', esc_attr( $google_recaptcha_site_key ) );

                                ?>
                                    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>

                                    <script type="text/javascript">
                                         window.addEventListener("DOMContentLoaded", () => {
                                         const container = document.querySelector('.chaptcha-container');
                                         const chaptcha = document.querySelector('#g-recaptcha');
                                         container.appendChild( chaptcha );
                                         document.querySelector(".wpcf7-submit").disabled = true;
                                        });

                                        function enableBtn() {
                                             document.querySelector(".wpcf7-submit").disabled = false;
                                         }

                                        var onloadCallback = function() {
                                            grecaptcha.render('g-recaptcha', {
                                                'sitekey' : '<?php echo esc_attr( $google_recaptcha_site_key ); ?>'
                                            });
                                        };
                                    </script>
                                <?php
                            }
                        ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="<?php echo esc_attr( $name ); ?>__col <?php echo esc_attr( $name ); ?>__col--bg">
				<?php echo $block_object->image( "{$name}__image" ); ?>
			</div>
		</div>
	</div>
</section>
