<?php

/**
 * Output Post Tag HTML
 */

function the_post_tag_html( $post_ID = 0 ) {
	$term = get_first_post_term( $post_ID );

	if ( ! empty( $term ) ) : ?>
		<div class="cpt-list-item__tag">
			<a href="<?php echo get_term_link( $term->term_id, $term->taxonomy ); ?>" class="cpt-list-item__tag-item">
				<?php echo $term->name; ?>
			</a>
		</div>
	<?php endif;
}