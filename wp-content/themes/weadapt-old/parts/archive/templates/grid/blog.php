<?php

$post_ID   = ! empty( $args['post_ID'] ) ? $args['post_ID'] : 0;
$post_meta = apply_filters( 'grid_template_post_meta', [], $post_ID );

if ( ! empty( $post_ID ) ) : ?>

<article class="cpt-list-item blog-list-item">
	<?php the_post_thumbnail_html( $post_ID, 'large' ); ?>

	<div class="cpt-list-item__content">
		<?php
			the_post_title_html( $post_ID );
			the_post_excerpt_html( $post_ID );

			if ( ! empty( $post_meta ) ) {
				the_post_meta_html( $post_meta );
			}
		?>
	</div>
</article>

<?php endif; ?>