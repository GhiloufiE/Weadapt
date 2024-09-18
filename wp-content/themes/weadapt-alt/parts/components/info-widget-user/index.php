<?php

/**
 * Info Widget User
 *
 * @package WeAdapt
 */
$user_ID            = !empty($args['user_ID']) ? $args['user_ID'] : 0;
$show_share_button  = isset($args['show_share_button']) ? wp_validate_boolean($args['show_share_button']) : false;
$show_follow_button = isset($args['show_follow_button']) ? wp_validate_boolean($args['show_follow_button']) : true;
$hide_empty_fields  = isset($args['hide_empty_fields']) ? wp_validate_boolean($args['hide_empty_fields']) : true;
$logged_in          = isset($args['logged_in']) ? wp_validate_boolean($args['logged_in']) : is_user_logged_in();

if (!empty($user_ID)) :
	$user_url = get_author_posts_url($user_ID);

	$user_meta = [
		// temp-hide badges count
		// ['icon-asterisk', get_user_badges( $user_ID )],
	];

	if (!empty($members_count = get_members_count($user_ID, 'user', $hide_empty_fields))) {
		$user_meta[] = ['icon-user', $members_count];
	}
?>

	<div class="info-widget-user">
		<?php load_inline_styles(__DIR__, 'info-widget-user'); ?>

		<div class="info-widget-user__avatar">
			<a href="<?php echo $user_url; ?>" class="info-widget-user__avatar__link">
				<?php echo get_avatar($user_ID, 98); ?>
			</a>
		</div>

		<div class="info-widget-user__content">
			<?php if (!empty($user_name = get_user_name($user_ID))) : ?>
				<h3 class="info-widget-user__name">
					<a href="<?php echo $user_url; ?>" class="info-widget-user__name__link"><?php echo $user_name; ?>
					</a>
				</h3>
			<?php endif; ?>
			<?php if (!empty($job_title = get_field('job_title', 'user_' . $user_ID))) : ?>
				<div class="info-widget-user__job">
					<?php echo wp_kses_post($job_title); ?>
				</div>
			<?php endif; ?>
			<?php if (!empty($job_title = get_field('job_title', 'user_' . $user_ID))) : ?>
				<?php
				$current_blog_ID = get_current_blog_id();
				$organisations = get_field('organisations', 'user_' . $user_ID);

				if (is_array($organisations)) {
					// Filter to ensure only published organisations for the current blog are included
					$published_organisations = array_filter($organisations, function ($org_ID) use ($current_blog_ID) {
						$post_status = get_post_status($org_ID);
						$publish_to = get_field('publish_to', $org_ID);
						return $post_status === 'publish' && (empty($publish_to) || in_array($current_blog_ID, $publish_to));
					});

				/* 	if (!empty($published_organisations)) : ?>
						<div class="info-widget-user__job">
							<?php
							foreach ($published_organisations as $org_ID) {
								$organisation_name = get_the_title($org_ID);
								echo wp_kses_post($organisation_name) . '<br>';
							}
							?>
						</div>
				<?php endif; */
				}
				?>
			<?php endif; ?>

			<?php if (!empty($user_description = get_user_excerpt($user_ID, 125))) : ?>
				<div class="info-widget-user__description">
					<?php echo $user_description; ?>
				</div>
			<?php endif; ?>

			<?php if (!empty($user_meta)) : ?>
				<ul class="info-widget-user__meta">
					<?php foreach ($user_meta as $item) : ?>
						<li class="info-widget-user__meta__item">
							<span class="icon" aria-label="<?php echo esc_attr($item[0]); ?>"><?php echo get_img($item[0]); ?></span>
							<span class="text"><?php echo $item[1]; ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<div class="info-widget-user__actions">
				<?php
				if ($show_share_button) {
					get_part('components/button-share/index', [
						'url'   => $user_url,
						'type'  => __('profile', 'weadapt'),
						'title' => __('Share your profile', 'weadapt'),
					]);
				} else {
					if ($show_follow_button) {
						get_part('components/button-join/index', [
							'title'        => __('Follow', 'weadapt'),
							'unjoin_title' => __('Unfollow', 'weadapt'),
							'class'        => 'button-join',
							'style'        => 'outline',
							'join_ID'      => $user_ID,
							'join_type'    => 'user'
						]);
					}

					// temp-hide contact button
					// if ( get_field( 'contact_form', 'user_' . $user_ID ) && $logged_in ) {
					// 	echo get_button([
					// 		'url'    => 'mailto:' . get_userdata($user_ID)->user_email,
					// 		'title'  => __( 'Contact', 'weadapt' ),
					// 		'target' => ''
					// 	]);
					// }
				}
				?>
			</div>
		</div>
	</div>

<?php endif; ?>