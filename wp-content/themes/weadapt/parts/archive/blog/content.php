<?php
/**
 * Single Blog Content
 *
 * @package WeAdapt
 */
$query_post_types = ! empty( $args['query_post_types'] ) ? $args['query_post_types'] : [ 'theme', 'network', 'blog', 'article', 'course', 'event' ];
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

if ( is_archive() ) {
	global $wp_query;

	$taxonomy = ! empty( $wp_query->query_vars['taxonomy'] ) ? $wp_query->query_vars['taxonomy'] : false;

	if ( $taxonomy && strpos( $taxonomy, 'solution-' ) !== false ) {
		$queried_object = get_queried_object();

		$key = str_replace( '-', '_', $queried_object->taxonomy );

		if ( $key === 'solution_addressed_target' ) {
			$key = 'solution_addressed_targets';
		}

		$base_args['meta_query'] = [
			'key'     => $key,
			'value'   => sprintf( $key === 'solution_benefit' ? "%s" : ':"%s"', $queried_object->term_id ),
			'compare' => 'LIKE'
		];
	}
	else {
		if ( ! empty( $tax_query = $wp_query->tax_query->queries ) ) {
			$base_args['tax_query'] = $tax_query;
		}
	}
}

if ( is_category() ) {
	$base_args['post_type'] = get_allowed_post_types( [ 'blog', 'article', 'course', 'event', 'case-study' ] );
}

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