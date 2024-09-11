<?php
/**
 * Tags
 *
 * @package WeAdapt
 */

$title = ! empty( $args['title'] ) ? $args['title'] : '';
$search_query = ! empty( $args['search_query'] ) ? $args['search_query'] : '';

$tags = [];

if ( is_single() ) {
	$tags = wp_get_object_terms( get_the_ID(), 'tags', [
		'orderby'     => 'none',
		'theme_query' => true // multisite fix
	] );
}
else if ( is_search() ) {
	$tags = get_terms( [
		'taxonomy'    => 'tags',
		'search'      => $search_query,
		'number'      => 5,
		'theme_query' => true // multisite fix
	] );
} 
else if ( 'page' !== get_post_type() ) {
	$tags = wp_get_object_terms( get_the_ID(), 'tags', [
		'orderby'     => 'none',
		'theme_query' => true // multisite fix
	] );
}
else {
	$tags = get_terms( [
		'taxonomy'    => 'tags',
		'orderby'     => 'count',
		'order'       => 'DESC',
		'number'      => 8,
		'theme_query' => true // multisite fix
	] );
}

if (
	is_search() ||
	( ! is_search() && ! empty( $tags ) )
) : ?>

<div class="single-tags">
	<?php load_inline_styles( __DIR__, 'single-tags' ); ?>

	<?php if ( ! empty( $title ) ) : ?>
		<h2 class="single-tags__title widget-title">
			<?php echo $title; ?>

			<?php if ( ! empty( $search_query ) ) : ?>
				<b>“<?php echo $search_query; ?>”</b>
			<?php endif; ?>
		</h2>
	<?php endif; ?>

	<?php if ( is_array( $tags ) && ! empty( $tags ) ) : ?>
		<ul class="single-tags__list">
			<?php foreach( $tags as $tag ) : ?>
				<li class="single-tags__tag">
					<a href="<?php echo get_term_link( $tag->term_id, $tag->taxonomy ); ?>" class="single-tags__link">
						<?php echo $tag->name; ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<p><?php _e( 'Nothing found.', 'weadapt' ); ?></p>
	<?php endif; ?>
</div>

<?php endif; ?>