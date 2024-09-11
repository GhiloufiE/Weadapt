<?php
/**
 * Single Article Content
 *
 * @package WeAdapt
 */
$query_post_types = ! empty( $args['query_post_types'] ) ? $args['query_post_types'] : [ 'blog', 'article', 'course', 'event' ];
$show_post_types  = isset( $args['show_post_types'] ) ? wp_validate_boolean( $args['show_post_types'] ) : true;
$show_filters     = isset( $args['show_filters'] ) ? wp_validate_boolean( $args['show_filters'] ) : true;
$base_args        = [
	'post_status'         => 'publish',
	'post_type'           => get_allowed_post_types( $query_post_types ),
	'orderby'             => 'date',
	'order'               => 'DESC',
	'ignore_sticky_posts' => true,
	'query_post_types'    => $query_post_types,
	'theme_query'         => true, // multisite fix
];

if ( is_user_logged_in() ) :
?>
<section id="tab-bookmarked-panel" role="tabpanel" aria-hidden="true" hidden>
	<?php
		$query_args = $base_args;

		$query_args['post__in'] = get_followed_posts( $base_args['post_type'] );
		$query_args['orderby']  = 'post__in';

		get_part( 'components/cpt-query/index', [
			'query_args'      => $query_args,
			'show_post_types' => $show_post_types,
			'show_filters'    => $show_filters,
		]);
	?>
</section>
<?php endif; ?>

<section id="tab-trending-panel" role="tabpanel" aria-hidden="false">
	<?php
		$query_args = $base_args;

		get_part( 'components/cpt-query/index', [
			'query_args'      => $query_args,
			'show_post_types' => $show_post_types,
			'show_filters'    => $show_filters,
		]);
	?>
</section>