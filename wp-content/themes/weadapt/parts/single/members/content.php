<?php
/**
 * Single Forum Content
 *
 * @package WeAdapt
 */

$post_ID = get_the_ID();


?>

<section id="tab-latest-panel" role="tabpanel" aria-hidden="false">
	<?php
		$query_args = array(
			'post_status'    => 'publish',
			'post_type'      => get_allowed_post_types( [ 'members' ] ),
			'orderby'        => 'date',
			'order'          => 'DESC',
			'meta_query'     => [[
				'key'      => 'members',
				'value'    => $post_ID
			]],
			'ignore_sticky_posts' => true,
			'theme_query'         => true, // multisite fix

		);

		get_part( 'components/cpt-query/index', [
			'query_args'      => $query_args,
			'show_post_types' => false,
			'show_categories' => false
		]);
	?>
</section>

<section id="tab-about-panel" role="tabpanel" aria-hidden="true" hidden>
	<div class="archive-main__entry archive-main__entry--smaller">
		<?php
			if ( empty( get_the_content() ) ) {
				echo sprintf( '<p>%s</p>', __( 'There is no content.', 'weadapt' ) );
			}
			else {
				the_content();
			}
		?>
	</div>
</section>

