<?php
/**
 * Single Blog Content
 *
 * @package WeAdapt
 */
?>

<div class="archive-main__entry">
	<?php
		if ( empty( get_the_content() ) ) {
			_e( 'There is no content.', 'weadapt' );
		}
		else {
			the_content();
		}
	?>
</div>

<?php

get_part('components/single-references/index');
if ( ! empty( $forums_cta = get_field( 'forums_cta', 'options' ) ) ) {
	echo sprintf( '<div class="forum-quote">%s</div>', apply_filters( 'the_content', $forums_cta ) );
}


// Trending discussions
?><div class="trending-discussions"><?php
	$heading = get_field( 'trending_discussions', 'options' );

	if ( ! empty( $heading['title'] ) ) {
		?><h2 class="trending-discussions__title"><?php echo wp_kses_post( $heading['title'] ); ?></h2><?php
	}
	if ( ! empty( $heading['description'] ) ) {
		?><div class="trending-discussions__description"><?php echo wp_kses_post( $heading['description'] ); ?></div><?php
	}

	$query_args = array(
		'post_status'         => 'publish',
		'post_type'           => get_allowed_post_types( [ 'forum' ] ),
		'fields'              => 'ids',
		'meta_key'            => '_views_count',
		'orderby'             => 'meta_value_num',
		'posts_per_page'      => 5,
		'ignore_sticky_posts' => true,
		'theme_query'         => true, // multisite fix
	);

	get_part( 'components/cpt-query/index', [
		'query_args'   => $query_args,
		'show_filters' => false
	]);
?></div>

