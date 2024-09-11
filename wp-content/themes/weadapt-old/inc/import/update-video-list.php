<?php

/*

Users:
http://weadapt/web/?import=update-video-list&key=013b0f890d204a522a7e462d1dfa93e5


mike-dev
85dCXpavMK!zyZAQ

*/

if (
	is_admin() ||
	! isset( $_GET['import'] ) ||
	$_GET['import'] != 'update-video-list' ||
	! isset( $_GET['key'] ) ||
	$_GET['key'] != '013b0f890d204a522a7e462d1dfa93e5'
) return;


add_action( 'init', function() {
	$query = new WP_Query( array(
		'posts_per_page' => -1,
		'post_type'      => 'any',
		'post_status'    => 'any',
		// 'post__in'       => array( 15344 ),
		'meta_query'     => array( array(
			'key' => 'video_list',
			'compare' => 'EXISTS',
		) )
	) );

	if ( ! empty( $query->posts ) ) {
		$i = 1;
		echo '<pre>';

		foreach ( $query->posts as $post ) {
			$post_ID = $post->ID;

			if ( ! empty( $video_list = get_field( 'video_list', $post_ID ) ) ) {
				$has_video = false;

				foreach ( $video_list as $video ) {
					if ( ! empty( $video['url'] ) ) {
						$has_video = true;
					}
				}

				if ( $has_video ) {
					$add_content = '';

					echo sprintf( '%s) [ID: %s] %s | %s', str_pad( $i, 3, '0', STR_PAD_LEFT ), $post_ID, get_edit_post_link( $post_ID ), get_the_title( $post_ID ) ) . PHP_EOL;

					foreach ( $video_list as $video ) {
						if ( ! empty( $video['url'] ) ) {
							echo sprintf( ' - %s', $video['url']);

							if ( ! empty( $video['description'] ) ) {
								echo sprintf( ' | %s', $video['description'] );
							}

							echo PHP_EOL;

							if ( strpos( $video['url'], 'vimeo' ) !== false ) {
								$add_content .= '<!-- wp:embed {"url":"' . $video['url'] . '","type":"video","providerNameSlug":"vimeo","responsive":true,"className":"is-type-video wp-embed-aspect-16-9 wp-has-aspect-ratio"} -->' . PHP_EOL .
								'<figure class="wp-block-embed is-type-video is-provider-vimeo wp-block-embed-vimeo wp-embed-aspect-16-9 wp-has-aspect-ratio"><div class="wp-block-embed__wrapper">' . PHP_EOL .
								$video['url'] . PHP_EOL .
								'</div><figcaption class="wp-element-caption">' . trim( $video['description'] ) . '</figcaption></figure>' . PHP_EOL .
								'<!-- /wp:embed -->' . PHP_EOL . PHP_EOL;
							}
							else {
								$add_content .= '<!-- wp:embed {"url":"' . $video['url'] . '","type":"video","providerNameSlug":"youtube","responsive":true,"className":"wp-embed-aspect-16-9 wp-has-aspect-ratio"} -->' . PHP_EOL .
								'<figure class="wp-block-embed is-type-video is-provider-youtube wp-block-embed-youtube wp-embed-aspect-16-9 wp-has-aspect-ratio"><div class="wp-block-embed__wrapper">' . PHP_EOL .
								$video['url'] . PHP_EOL .
								'</div><figcaption class="wp-element-caption">' . trim( $video['description'] ) . '</figcaption></figure>' . PHP_EOL .
								'<!-- /wp:embed -->' . PHP_EOL . PHP_EOL;
							}
						}
					}

					if ( ! empty( $add_content ) ) {
						wp_update_post( wp_slash( [
							'ID'           => $post_ID,
							'post_content' => $add_content . $post->post_content
						] ) );
					}

					$i++;
				}
			}
		}
		echo '</pre>';
	}

	die();
} );