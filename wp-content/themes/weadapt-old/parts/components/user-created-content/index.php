<?php
/**
 * User Description template.
 *
 * @package    WordPress
 * @subpackage weadapt
 * @since      weadapt 1.0
 */
$user_ID    = ! empty( $args['user_ID'] ) ? intval( $args['user_ID'] ) : 0;
$content    = get_field( 'created_content', 'options' );
$post_types = get_allowed_post_types( [ 'article', 'blog', 'course', 'event', 'case-study' ] );
?>

<div class="created-content archive-main__entry--smaller">
	<?php load_inline_styles( __DIR__, 'user-created-content' ); ?>
	<?php load_blocks_script( 'user-created-content', 'weadapt/user-created-content' ); ?>

	<?php if ( ! empty( $content['title'] ) ) : ?>
		<h2 class="created-content__title"><?php echo wp_kses_post( $content['title'] ); ?></h2>
	<?php endif; ?>

	<?php if ( ! empty( $content['description'] ) ) : ?>
		<div class="created-content__content"><?php echo wp_kses_post( $content['description'] ); ?></div>
	<?php endif; ?>

	<?php
		get_part( 'components/single-tabs-nav/index', [ 'items' => [
			[
				'id' => 'tab-drafts',
				'controls' => 'tab-drafts-panel',
				'selected' => true,
				'label' => __( 'Drafts', 'weadapt' ),
			],
			[
				'id' => 'tab-published',
				'controls' => 'tab-published-panel',
				'selected' => false,
				'label' => __( 'Published', 'weadapt' ),
			]
		] ] );
	?>

	<section id="tab-drafts-panel" role="tabpanel" aria-hidden="false">
		<?php
			$query_args = array(
				'post_type'           => $post_types,
				'post_status'         => ['draft', 'future', 'private'],
				'posts_per_page'      => 5,
				'meta_query'          => [
					'relation' => 'OR',
					[
						'key'         => 'people_creator',
						'value'       => sprintf( ':"%d";', $user_ID ),
						'compare'     => 'LIKE'
					],
					[
						'key'         => 'people_contributors',
						'value'       => sprintf( ':"%d";', $user_ID ),
						'compare'     => 'LIKE'
					]
				],
				'ignore_sticky_posts'  => true,
				'theme_show_buttons'   => true,
				'theme_is_author_page' => true,
				'theme_query'          => true, // multisite fix
			);

			get_part( 'components/cpt-query/index', [
				'query_args'   => $query_args,
				'show_filters' => false
			]);
		?>
	</section>

	<section id="tab-published-panel" role="tabpanel" aria-hidden="true" hidden>
		<?php
			$query_args = array(
				'post_type'           => $post_types,
				'post_status'         => ['pending', 'publish'],
				'posts_per_page'      => 5,
				'meta_query'          => [
					'relation' => 'OR',
					[
						'key'         => 'people_creator',
						'value'       => sprintf( ':"%d";', $user_ID ),
						'compare'     => 'LIKE'
					],
					[
						'key'         => 'people_contributors',
						'value'       => sprintf( ':"%d";', $user_ID ),
						'compare'     => 'LIKE'
					]
				],
				'ignore_sticky_posts'  => true,
				'theme_show_buttons'   => true,
				'theme_is_author_page' => true,
				'theme_query'          => true, // multisite fix
			);

			get_part( 'components/cpt-query/index', [
				'query_args'   => $query_args,
				'show_filters' => false
			]);
		?>
	</section>
</div>