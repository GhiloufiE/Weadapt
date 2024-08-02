<?php
/**
 * Single Hero template.
 *
 * @package    WordPress
 * @subpackage weadapt
 * @since      weadapt 1.0
 */

$current_user           = wp_get_current_user();
$user_ID                = $current_user->ID;
$display_name           = sprintf( '%s %s', __( 'Welcome', 'weadapt' ), $current_user->display_name );
$is_current_user_author = false;

if ( is_author() ) {
	$user_ID      = get_the_author_meta( 'ID' );
	$display_name = get_the_author_meta( 'display_name' );

	if ( $user_ID === $current_user->ID ) {
		$is_current_user_author = true;
	}
}

$meta_items   = [];
$avatar       = get_avatar( $user_ID, 608 );
$description  = get_user_meta( $user_ID, 'description', true );
$badges_count = get_user_badges( $user_ID );

if ( ! empty( $badges_count ) ) {
	$meta_items[] = ['icon-asterisk', $badges_count];
}

if ( ! empty( $members_count = get_members_count( $user_ID, 'user', true ) ) ) {
	$meta_items[] = ['icon-user', $members_count];
}
?>

<section class="single-hero single-hero--user">
	<?php load_inline_styles( dirname(__DIR__, 1) . '/single-hero', 'single-hero' ); ?>
	<?php load_inline_styles( __DIR__, 'author-hero' ); ?>

	<div class="single-hero__container container">
		<div class="single-hero__row row <?php echo empty( $avatar ) ? 'single-hero__row_top' : ''; ?>">
			<div class="single-hero__right">
				<?php if ( ! empty( $avatar ) ) : ?>
					<figure class="single-hero__image img-caption">
						<?php echo $avatar; ?>
					</figure>
				<?php endif; ?>
			</div>

			<div class="single-hero__left">
				<div class="single-hero__left-inner">
					<?php if ( ! empty( $display_name ) ) : ?>
						<h1 class="single-hero__title" id="main-heading"><?php echo esc_html( $display_name ); ?></h1>
					<?php endif; ?>

					<?php if ( ! empty( $description = get_user_excerpt( $user_ID, 125 ) ) ) : ?>
						<div class="single-hero__excerpt"><?php echo wp_kses_post( $description ); ?></div>
					<?php endif; ?>

					<?php if ( ! empty( $meta_items ) ) : ?>
						<ul class="post-meta single-hero__meta">
							<?php foreach ( $meta_items as $item ) : ?>
								<li class="post-meta__item">
									<span class="icon" aria-label="<?php echo esc_attr( $item[0] ); ?>"><?php echo get_img( $item[0] ); ?></span>
									<span class="text"><?php echo $item[1]; ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<div class="single-hero__buttons">
						<?php
							if ( is_author() ) {
								if ( ! $is_current_user_author ) {
									get_part( 'components/button-join/index', [
										'title'        => __( 'Follow', 'weadapt' ),
										'unjoin_title' => __( 'Unfollow', 'weadapt' ),
										'class'        => 'button-join',
										'style'        => 'outline',
										'join_ID'      => $user_ID,
										'join_type'    => 'user'
									] );
								}

								get_part( 'components/button-share/index', [
									'url'   => get_author_posts_url( $user_ID ),
									'type'  => __( 'profile', 'weadapt' )
								] );

								// temp-hide contact button
								// if ( get_field( 'contact_form', 'user_' . $user_ID ) && is_user_logged_in() ) {
								// 	echo get_button( [
								// 		'url' => 'mailto:' . get_userdata($user_ID)->user_email,
								// 		'title' => __('Contact', 'weadapt'),
								// 		'target' => '',
								// 	], '', 'has-icon-left', 'icon-mail' );
								// }
							}
							else {
								echo get_button( [
									'url' => get_author_posts_url( $user_ID ),
									'title' => __('View profile', 'weadapt'),
									'target' => '',
								], 'outline' );

								if ( is_page_template( 'page-templates/edit-profile.php' ) ) {
									
								}
								else {
									$edit_profile_ID = get_page_id_by_template( 'edit-profile' );

									echo get_button( [
										'url' => $edit_profile_ID ? get_permalink( $edit_profile_ID ) : '#',
										'title' => __('Edit', 'weadapt'),
										'target' => '',
									] );
								}
							}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<?php

add_action( 'popup-content', function() {
		echo get_part( 'components/popup/index', [ 'template' => 'org-creation' ] );
} );
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
	const urlParams = new URLSearchParams(window.location.search);
	if (urlParams.has('account_created') && urlParams.get('account_created') === 'true') {
		const createAccountButton = document.querySelector('button[data-popup="org-creation"]');
		if (createAccountButton) {
			createAccountButton.click();
		}
	}
});
</script>
