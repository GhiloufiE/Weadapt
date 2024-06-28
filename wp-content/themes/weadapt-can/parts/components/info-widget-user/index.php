<?php
/**
 * Info Widget User
 *
 * @package WeAdapt
 */
$user_ID           = ! empty( $args['user_ID'] ) ? $args['user_ID'] : 0;

if ( ! empty( $user_ID ) ) :
	$user_url = get_author_posts_url( $user_ID );

    $user_name = get_user_name( $user_ID );
?>

<div class="info-widget-user">
	<?php load_inline_styles( __DIR__, 'info-widget-user' ); ?>

    <div class="info-widget-user__top">
        <div class="info-widget-user__avatar">
            <a href="<?php echo $user_url; ?>" class="info-widget-user__avatar__link">
                <?php echo get_avatar( $user_ID, 114 ); ?>
            </a>
        </div>

        <div>
            <?php if ( ! empty( $user_name ) ) : ?>
                <h3 class="info-widget-user__name">
                    <a href="<?php echo $user_url; ?>" class="info-widget-user__name__link"><?php echo $user_name; ?>
                    </a>
                </h3>
            <?php endif; ?>

            <?php if ( ! empty( $job_title = get_field( 'job_title', 'user_' . $user_ID ) ) ) : ?>
                <div class="info-widget-user__job">
                    <?php echo wp_kses_post( $job_title ); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>


	<div class="info-widget-user__content">
		<?php if ( ! empty( $user_description = get_user_excerpt( $user_ID, 267 ) ) ) : ?>
			<div class="info-widget-user__description">
				<?php echo $user_description; ?>
			</div>
		<?php endif; ?>

        <?php // temp-hide contact button
        /*
		<div class="info-widget-user__actions">
			<?php
				if ( get_field( 'contact_form', 'user_' . $user_ID ) && is_user_logged_in() ) {
					echo get_button([
						'url'    => 'mailto:' . get_userdata($user_ID)->user_email,
						'title'  => __( 'Contact', 'weadapt' ) . " " . explode(' ', $user_name )[0],
						'target' => ''
					], 'secondary');
				}
			?>
		</div>
        */ ?>
	</div>
</div>

<?php endif; ?>