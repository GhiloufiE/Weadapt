<?php
/**
 * Related content
 *
 * @package WeAdapt
 */

$related_content_IDs = get_field( 'relevant_related_content' ) ? get_field( 'relevant_related_content' ) : [];

if ( ! empty( $related_content_IDs ) ) :
?>

<div class="related-content">
<h2 class="single-resources__title small-title"><?php _e( 'Further resources', 'weadapt' ); ?></h2>

	<?php load_inline_styles( __DIR__, 'related-content' ); ?>

	<?php
		$query_args = [
			'post_status'         => 'publish',
			'post_type'           => get_allowed_post_types( [ 'blog', 'article', 'course', 'event', 'case-study', 'solutions-portal' ] ),
			'post__in'            => $related_content_IDs,
			'orderby'             => 'post__in',
			'posts_per_page'      => 4,
 			'ignore_sticky_posts' => true,
			'theme_query'         => true, // multisite fix
		];

		get_part( 'components/cpt-query/index', [
			'query_args'    => $query_args,
			'show_filters'  => false,
			'hide_no_found' => true
		]);
	?>
</div>

<?php
endif;