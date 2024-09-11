<?php

$post_ID = ! empty( $args['post_ID'] ) ? $args['post_ID'] : 0;

if ( ! empty( $post_ID ) ) :
	$date_html  = get_event_formatted_date( $post_ID );
	$doi = get_field( 'doi', $post_ID );
?>

<article class="cpt-list-item blog-list-item blog-list-item--event">
	<?php the_post_thumbnail_html( $post_ID, 'large' ); ?>

	<div class="cpt-list-item__content">
		<?php
			the_post_title_html( $post_ID );
			the_post_excerpt_html( $post_ID );
		?>

		<?php if ( ! empty( $doi ) || ! empty( $date_html ) ) : ?>
			<p>
				<?php if ( ! empty( $doi ) ) :
					echo esc_attr( $doi ); ?>,
				<?php endif;

				if ( ! empty( $date_html ) ) :
					echo $date_html;
				endif; ?>
			</p>
		<?php endif; ?>
	</div>
</article>

<?php endif; ?>
