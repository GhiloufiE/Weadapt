<?php
/**
 * Single Organisation Content
 *
 * @package WeAdapt
 */
?>

<section id="tab-about-panel" role="tabpanel" aria-hidden="false">
	<div class="archive-main__entry archive-main__entry--smaller">
		<?php
			if ( empty( get_the_content() ) ) {
				_e( 'There is no content.', 'weadapt' );
			}
			else {
				the_content();
			}
		?>
	</div>
</section>

<section id="tab-latest-panel" role="tabpanel" aria-hidden="true" hidden>
	<?php
		$query_args = array(
			'post_status'    => 'publish',
			'post_type'      => get_allowed_post_types( [ 'article', 'blog', 'course', 'case-study', 'event' ] ),
			'orderby'        => 'date',
			'order'          => 'DESC',
			'meta_query'     => [ [
				'key'      => 'relevant_organizations',
				'value'    => sprintf( ':"%d";', get_the_ID() ),
				'compare'  => 'LIKE'
			] ],
			'ignore_sticky_posts' => true,
			'theme_query'         => true, // multisite fix
		);

		get_part( 'components/cpt-query/index', [
			'query_args' => $query_args
		]);
	?>
</section>

<section id="tab-members-panel" role="tabpanel" aria-hidden="true" hidden>
	<?php
		$query_args = [
			'meta_query' => [ [
				'key'      => 'organisations',
				'value'    => sprintf( ':"%d";', get_the_ID() ),
				'compare'  => 'LIKE'
			] ],
			'orderby' => 'user_registered',
			'order'   => 'DESC',
			'fields'  => 'ID',
			'number'  => get_option( 'posts_per_page' ),
		];

		get_part('components/cpt-search-query/index', [
			'title'       => __( 'Members', 'weadapt' ),
			'description' => '',
			'show_search' => false,
			'query_type'  => 'user_query',
			'query_args'  => $query_args,
		]);
	?>
</section>

<?php
/*
// temp-hide organisation resources content
<section id="tab-resources-panel" role="tabpanel" aria-hidden="true" hidden>
	<p class="cpt-content-heading__text">
		<?php _e( 'Resources', 'weadapt' ); ?>
	</p>
</section>
*/