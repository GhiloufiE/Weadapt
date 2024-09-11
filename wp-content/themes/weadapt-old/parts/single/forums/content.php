<?php
/**
 * Single Forum Content
 *
 * @package WeAdapt
 */

$post_ID = get_the_ID();
?>
<section id="tab-latest-panel" role="tabpanel" aria-hidden="false">
	<?php
		$query_args = array(
			'post_status'    => 'publish',
			'post_type'      => get_allowed_post_types( [ 'forum' ] ),
			'orderby'        => 'date',
			'order'          => 'DESC',
			'meta_query'     => [[
				'key'      => 'forum',
				'value'    => $post_ID
			]],
			'ignore_sticky_posts' => true,
			'theme_query'         => true, // multisite fix
			'categories'          => []
		);

		get_part( 'components/cpt-query/index', [
			'query_args'      => $query_args,
			'show_post_types' => false,
			'show_categories' => false
		]);
	?>
</section>

<section id="tab-about-panel" role="tabpanel" aria-hidden="true" hidden>
	<div class="archive-main__entry archive-main__entry--smaller">
		<?php
			if ( empty( get_the_content() ) ) {
				echo sprintf( '<p>%s</p>', __( 'There is no content.', 'weadapt' ) );
			}
			else {
				the_content();
			}

			if ( ! empty( $forums_cta = get_field( 'forums_cta', 'options' ) ) ) {
				echo sprintf( '<div class="forum-quote">%s</div>', apply_filters( 'the_content', $forums_cta ) );
			}
		?>
	</div>
</section>

<section id="tab-members-panel" role="tabpanel" aria-hidden="true" hidden>
	<?php
		if ( ! empty( $followed_users = get_followed_users( $post_ID, 'forums' ) ) ) :
			$query_args = [
				'include' => $followed_users,
				'fields'  => 'ID',
				'number'  => get_option( 'posts_per_page' ),
			];

			get_part('components/cpt-search-query/index', [
				'title'       => __( 'Members', 'weadapt' ),
				'description' => __( 'Connect with peers working on similar issues.', 'weadapt' ),
				'query_type'  => 'user_query',
				'query_args'  => $query_args,
				'show_search' => false
			]);
		?>
	<?php else: ?>
		<p class="cpt-content-heading__text">
			<?php _e( 'There are no members', 'weadapt' ); ?>
		</p>
	<?php endif; ?>
</section>