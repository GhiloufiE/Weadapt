<?php

/**
 * Output Post Meta HTML
 */

function the_post_meta_html( $post_meta ) {
	if ( empty( $post_meta ) ) return;
	?>
		<ul class="post-meta">
			<?php foreach ( $post_meta as $meta ) : ?>
				<?php if ( ! empty( $meta[1] ) ) : ?>
					<li class="post-meta__item">
						<span class="icon" aria-label="<?php echo esc_attr( $meta[0] ); ?>"><?php echo get_img( $meta[0] ); ?></span>
						<span class="text"><?php echo wp_kses_post( $meta[1] ); ?></span>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	<?php
}