<?php
/**
 * Single Theme Content
 *
 * @package WeAdapt
 */

$base_args = [
	'post_status'         => 'publish',
	'post_type'           => get_allowed_post_types( [ 'theme' ] ),
	'orderby'             => 'date',
	'order'               => 'DESC',
	'ignore_sticky_posts' => true,
	'theme_query'         => true, // multisite fix
];

if ( is_user_logged_in() ) :
?>
<section id="tab-followed-panel" role="tabpanel" aria-hidden="true" hidden>
	<?php
		$query_args = $base_args;

		$query_args['post__in'] = get_followed_posts( 'theme' );
		$query_args['orderby']  = 'post__in';

		get_part( 'components/cpt-query/index', [
			'query_args' => $query_args
		]);
	?>
</section>
<?php endif; ?>

<section id="tab-trending-panel" role="tabpanel" aria-hidden="false">
	<?php
		$query_args = $base_args;

		get_part( 'components/cpt-query/index', [
			'query_args' => $query_args
		]);
	?>
</section>