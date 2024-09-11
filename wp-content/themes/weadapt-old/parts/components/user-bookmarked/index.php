<?php
/**
 * User Description template.
 *
 * @package    WordPress
 * @subpackage weadapt
 * @since      weadapt 1.0
 */

$user_ID = ! empty( $args['user_ID'] ) ? intval( $args['user_ID'] ) : 0;
$content = get_field( 'bookmarked', 'options' );
?>

<div class="bookmarked archive-main__entry--smaller">
	<?php load_inline_styles( __DIR__, 'user-bookmarked' ); ?>

	<?php if ( ! empty( $content['title'] ) ) : ?>
		<h2 class="bookmarked__title"><?php echo wp_kses_post( $content['title'] ); ?></h2>
	<?php endif; ?>

	<?php if ( ! empty( $content['description'] ) ) : ?>
		<div class="bookmarked__content"><?php echo wp_kses_post( $content['description'] ); ?></div>
	<?php endif; ?>

	<?php
		$post_types = get_allowed_post_types( [ 'article', 'blog', 'course', 'event', 'case-study' ] );
		$query_args = array(
			'post_type'           => $post_types,
			'post_status'         => 'publish',
			'posts_per_page'      => 5,
			'ignore_sticky_posts' => true,
			'post__in'            => get_followed_posts( $post_types ),
			'orderby'             => 'post__in',
			'theme_query'         => true, // multisite fix
		);

		get_part( 'components/cpt-query/index', [
			'query_args'   => $query_args,
			'show_filters' => false
		]);
	?>
</div>