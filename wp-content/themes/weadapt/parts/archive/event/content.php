<?php
/**
 * Single Webinar Content
 *
 * @package WeAdapt
 */
$query_post_types = ! empty( $args['query_post_types'] ) ? $args['query_post_types'] : [ 'event' ];
$show_post_types  = isset( $args['show_post_types'] ) ? wp_validate_boolean( $args['show_post_types'] ) : true;
$show_filters     = isset( $args['show_filters'] ) ? wp_validate_boolean( $args['show_filters'] ) : true;
$base_args        = [
	'post_status'         => 'publish',
	'post_type'           => get_allowed_post_types( $query_post_types ),
	'ignore_sticky_posts' => true,
	'theme_query'         => true, // multisite fix
	'query_post_types'    => $query_post_types,
	'orderby'             => 'meta_value',
	'meta_key'            => 'start_date',
	'meta_type'           => 'DATETIME',
	'order'               => 'DESC',
];
?>

<section>
	<?php
		$query_args = $base_args;

		get_part( 'components/cpt-query/index', [
			'query_args'      => $query_args,
			'show_post_types' => $show_post_types,
			'show_filters'    => $show_filters,
		]);
	?>
</section>