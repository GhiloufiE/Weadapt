<?php
/**
 * Info Widget User
 *
 * @package WeAdapt
 */
$user_ID           = ! empty( $args['user_ID'] ) ? $args['user_ID'] : 0;
$show_share_button = ! empty( $args['show_share_button'] ) ? $args['show_share_button'] : false;

if ( ! empty( $user_ID ) ) :
	$user_url = get_author_posts_url( $user_ID );

	$user_meta = [
		// temp-hide badges count
		// ['icon-asterisk', get_user_badges( $user_ID )],
		['icon-user', get_members_count( $user_ID, 'user' )]
	];
?>

<div class="info-widget-user">
	<?php load_inline_styles( __DIR__, 'info-widget-user' ); ?>

	<div class="info-widget-user__avatar">
		<a href="<?php echo $user_url; ?>" class="info-widget-user__avatar__link">
			<?php echo get_avatar( $user_ID, 98 ); ?>
		</a>

        <div class="info-widget-user__user" >
            <?php if ( ! empty( $user_name = get_user_name( $user_ID ) ) ) : ?>
                <h3 class="info-widget-user__name">
                    <a href="<?php echo $user_url; ?>" class="info-widget-user__name__link"><?php echo $user_name; ?></a>
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

		<?php if ( ! empty( $user_description = get_user_excerpt( $user_ID, 125 ) ) ) : ?>
			<div class="info-widget-user__description">
				<?php echo $user_description; ?>
			</div>
		<?php endif; ?>

		<div class="info-widget-user__actions">
			<?php
				if ( $show_share_button ) {
					get_part( 'components/button-share/index', [
						'url'   => $user_url,
						'type'  => __( 'profile', 'weadapt' ),
						'title' => __( 'Share your profile', 'weadapt' ),
					] );
				}
				else {

					if ( get_field( 'contact_form', 'user_' . $user_ID ) && is_user_logged_in() ) {
						echo get_button([
							'url'    => 'mailto:' . get_userdata($user_ID)->user_email,
							'title'  => __( 'Contact ' . strtok( $user_name, ' ') , 'weadapt' ),
							'target' => ''
						]);
					}
				}
			?>
		</div>
	</div>
</div>

<?php endif; ?>
