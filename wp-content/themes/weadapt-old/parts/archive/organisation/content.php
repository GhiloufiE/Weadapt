<?php
/**
 * Single Organisation Content
 *
 * @package WeAdapt
 */
$post_status = current_user_can( 'administrator' ) ? ['publish', 'draft'] : ['publish'];
$base_args   = [
	'post_status'         => $post_status,
	'post_type'           => get_allowed_post_types( [ 'organisation' ] ),
	'orderby'             => 'date',
	'order'               => 'DESC',
	'ignore_sticky_posts' => true,
	'theme_query'         => true, // multisite fix
];

?>

<section id="tab-trending-panel" role="tabpanel" aria-hidden="false">
	<?php
		$query_args = $base_args;

		get_part( 'components/cpt-query/index', [
			'query_args'      => $query_args,
			'show_post_types' => false,
			'show_search'     => true,
		]);
	?>
</section>