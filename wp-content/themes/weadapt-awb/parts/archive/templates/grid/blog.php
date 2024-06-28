<?php

$post_ID = ! empty( $args['post_ID'] ) ? $args['post_ID'] : 0;

if ( ! empty( $post_ID ) ) : ?>

<article class="cpt-list-item blog-list-item">
	<?php the_post_thumbnail_html( $post_ID, 'large' ); ?>

	<div class="cpt-list-item__content">
		<?php
			the_post_title_html( $post_ID );
			the_post_excerpt_html( $post_ID );
		?>
	</div>

    <div class="wp-block-button">
        <a class="wp-block-button__link" href="<?php echo the_permalink($post_ID); ?>">
    	    <span>View Article</span>
    	    <?php echo get_img( 'icon-arrow-right-button' ); ?>
    	</a>
    </div>


</article>

<?php endif; ?>

