<?php
/**
 * Single Theme Content
 *
 * @package WeAdapt
 */
$user_ID    = ! empty( $args['user_ID'] ) ? intval( $args['user_ID'] ) : 0;
$is_profile = ! empty( $args['is_profile'] ) ? wp_validate_boolean( $args['is_profile'] ) : false;
?>

<section id="tab-latest-panel" role="tabpanel" aria-hidden="false">
	<?php
		$query_args = array(
			'post_type'           => get_allowed_post_types( [ 'article', 'blog', 'course', 'event', 'case-study' ] ),
			'posts_per_page'      => 5,
			'meta_query'          => [
				'relation' => 'OR',
				[
					'key'         => 'people_contributors',
					'value'       => sprintf( ':"%d";', $user_ID ),
					'compare'     => 'LIKE',
					'compare_key' => 'LIKE'
				],
			],
			'ignore_sticky_posts' => true,
			'theme_query'         => true, // multisite fix
		);

		// Add All posts from editor themes networks
		/*$user_relevant_main_themes_networks = new WP_Query( [
			'post_type'       => get_allowed_post_types( [ 'theme', 'network' ] ),
			'posts_per_page'  => -1,
			'fields'          => 'ids',
			'no_found_rows'   => true,
			'meta_query'      => [ [
				'key'     => 'people_editors',
				'value'   => sprintf( ':"%d";', $user_ID ),
				'compare' => 'LIKE'
			] ],
		] );

		if ( ! empty( $user_relevant_main_themes_networks->posts ) ) {
			foreach ( $user_relevant_main_themes_networks->posts as $post_ID ) {
				$query_args['meta_query'][] = [
					'key'     => 'relevant_main_theme_network',
					'value'   => $post_ID,
					'compare' => '=',
				];
			}
		}*/

		get_part( 'components/cpt-query/index', [
			'query_args' => $query_args
		]);
	?>
</section>

<section id="tab-about-panel" role="tabpanel" aria-hidden="true" hidden>
	<?php get_part('components/user-about/index', $args ); ?>
</section>

<?php if ( $is_profile ) : ?>
	<section id="tab-badges-panel" role="tabpanel" aria-hidden="true" hidden>
		<?php get_part('components/user-badges/index', $args ); ?>
	</section>
	<?php
	/*
	// temp-hide notifications content
	<section id="tab-notifications-panel" role="tabpanel" aria-hidden="true" hidden>

	</section>
	*/ ?>
	<section id="tab-created-content-panel" role="tabpanel" aria-hidden="true" hidden>
		<?php get_part('components/user-created-content/index', $args ); ?>
	</section>

	<section id="tab-bookmarked-panel" role="tabpanel" aria-hidden="true" hidden>
		<?php get_part('components/user-bookmarked/index', $args ); ?>
	</section>

	<?php if ( class_exists( 'Front_End_Pm_Pro' ) ) : ?>
		<section id="tab-messages-panel" role="tabpanel" aria-hidden="true" hidden>
			<?php get_part('components/user-messages/index', $args ); ?>
		</section>
	<?php endif; ?>
<?php endif; ?>
