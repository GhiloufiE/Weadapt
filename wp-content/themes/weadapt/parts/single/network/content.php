<?php
/**
 * Single Network Content
 *
 * @package WeAdapt
 */
?>

<section id="tab-latest-panel" role="tabpanel" aria-hidden="false">
	<?php
		$query_args = array(
			'post_status'    => 'publish',
			'post_type'      => get_allowed_post_types( [ 'article', 'blog', 'course', 'case-study', 'event' ] ),
			'orderby'        => 'date',
			'order'          => 'DESC',
			'meta_query'     => [ [
				'key'   => 'relevant_main_theme_network',
				'value' => get_the_ID(),
			] ],
			'ignore_sticky_posts' => true,
			'theme_query'         => true, // multisite fix
		);

		get_part( 'components/cpt-query/index', [
			'query_args'      => $query_args,
			'show_post_types' => true,
			'show_categories' => true
		]);

		if ( apply_filters( 'show_related_single_network_content', true ) ) {
			get_part('components/related-content/index');
		}

		if ( ! empty( get_allowed_post_types( [ 'forums' ] ) ) ) {
			get_part('components/forum-cta/index');
		}
	?>
</section>

<section id="tab-about-panel" role="tabpanel" aria-hidden="true" hidden>
	<div class="archive-main__entry archive-main__entry--smaller">
		<?php
			if ( empty( get_the_content() ) ) {
				_e( 'There is no content.', 'weadapt' );
			}
			else {
				the_content();
			}
		?>
	</div>
</section>

<section id="tab-editors-panel" role="tabpanel" aria-hidden="true" hidden>
	<?php get_part('components/contact-cols/index'); ?>
</section>

<section id="tab-members-panel" role="tabpanel" aria-hidden="true" hidden>
	<?php
		if ( ! empty( $followed_users = get_followed_users( get_the_ID(), get_post_type() ) ) ) :
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
			]);
		?>
	<?php else: ?>
		<p class="cpt-content-heading__text">
			<?php _e( 'There are no members', 'weadapt' ); ?>
		</p>
	<?php endif; ?>
</section>

<section id="tab-organisations-panel" role="tabpanel" aria-hidden="true" hidden>
	<?php
		$organisation = get_field( 'relevant_organizations' );

		$query_args = ! empty( $organisation ) ? [
			'post_status'         => 'publish',
			'post_type'           => get_allowed_post_types( [ 'organisation' ] ),
			'post__in'            => $organisation,
			'orderby'             => 'post__in',
			'fields'              => 'ids',
			'ignore_sticky_posts' => true,
			'theme_query'         => true, // multisite fix
		] : [];

		get_part('components/cpt-search-query/index', [
			'title'         => __( 'Participating Organisations', 'weadapt' ),
			'description'   => __( 'Connect with organizations working on similar issues.', 'weadapt' ),
			'query_args'    => $query_args,
		]);
	?>
</section>