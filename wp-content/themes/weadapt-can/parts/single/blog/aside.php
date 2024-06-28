<?php
/**
 * Single Blog Aside
 *
 * @package WeAdapt
 */

$relevant         = get_field( 'relevant' );
$relevant_post_ID = get_field( 'relevant_main_theme_network' ) ? get_field( 'relevant_main_theme_network' ) : 0;

$people           = get_field( 'people' );
$contributors_IDs = ! empty( $people['contributors'] ) ? $people['contributors'] : [];

if ( ! empty( $contributors_IDs ) ) {
	?><h2 class="featured-resource__title widget-title">Contributors</h2><?php
	foreach ( $contributors_IDs as $contributors_ID ) {
		get_part('components/info-widget-user/index', [
			'user_ID' => $contributors_ID,
		]);
	}
}

get_part('components/tags/index', ['title' => __( 'Tags', 'weadapt' )]);

$related_content_IDs = get_field( 'relevant_related_content' ) ? get_field( 'relevant_related_content' ) : [];

if ( ! empty( $related_content_IDs ) ) : ?>
	<div class="related-blog">
		<h2 class="widget-title"><?php _e( 'CanAdapt Blog', 'can-adapt' ); ?></h2>

		<div class="related-blog__row">
			<?php foreach ( $related_content_IDs as $related_content_ID ) :
				get_part( 'archive/templates/grid/blog', [ 'post_ID' => $related_content_ID ] );
			endforeach; ?>
		</div>
	</div>
<?php endif;