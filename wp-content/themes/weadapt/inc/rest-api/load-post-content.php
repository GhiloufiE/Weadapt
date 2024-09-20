<?php

/**
 * Load Post Content
 */
function load_post_content() {
	$post_type = ! empty( $_POST['post_type'] ) ? esc_attr( $_POST['post_type'], true ) : 'any';
	$post_ID   = ! empty( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
	$part_name = file_exists( get_theme_file_path("/parts/single/$post_type/") ) ? $post_type : 'blog';

 	ob_start();
		global $post;

		$post = get_post( $post_ID );

		// If post type is 'members', get the user by username and fetch display name
		if ( 'members' === $post_type ) {
			$member_username = $post->post_name; // Assuming post slug is the username
			$member_user = get_user_by( 'login', $member_username ); // Get the user by their username

			if ( $member_user ) {
				// Replace the post title (username) with the display name
				$post->post_title = $member_user->display_name;
			}
		}

		setup_postdata( $post );
		?>
		<main id="page-content" class="page-content page-content--single">
			<div id="content" tabindex="-1" class="page-content__wrapper">
				<?php get_part('components/single-hero/index', ['type' => $post_type]); ?>

				<section class="archive-main" aria-labelledby="main-heading">
					<div class="archive-main__container container">
						<?php get_part("single/$part_name/breadcrumbs"); ?>
						<?php get_part("single/$part_name/filter-tabs"); ?>

						<div class="archive-main__row row">
							<div class="archive-main__content">
								<?php get_part("single/$part_name/content"); ?>
							</div>

							<aside class="archive-main__aside">
								<?php get_part("single/$part_name/aside"); ?>
							</aside>
						</div>
					</div>
				</section>
			</div>
		</main>
		<?php

		wp_reset_postdata();
	$output_html = ob_get_clean();

	echo json_encode( [
		'output_html' => $output_html
	] );

	die();
}
add_action( 'wp_ajax_load_post_content', 'load_post_content' );
add_action( 'wp_ajax_nopriv_load_post_content', 'load_post_content' );