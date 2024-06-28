<?php

$post_ID   = ! empty( $args['post_ID'] ) ? $args['post_ID'] : 0;
$post_type = get_post_type();

$date_format    = 'jS M Y';
$published_date = get_the_date( $date_format, $post_ID );
$modified_date  = get_the_modified_date( $date_format, $post_ID );

if ( ! empty( $post_ID ) ) : ?>

<article class="cpt-list-item blog-list-item">
	<?php the_post_thumbnail_html( $post_ID, 'large' ); ?>

	<div class="cpt-list-item__content">
		<?php the_post_title_html( $post_ID ); ?>

		<div class="cpt-list-item__meta">
			<?php if ( $post_type === 'blog' ) : ?>
				<div class="cpt-list-item__post-type cpt-list-item__post-type--news">
					<span><?php _e( 'News', 'weadapt-can' ); ?></span>
				</div>
			<?php else: 
				the_post_type_html( $post_ID );
			endif; ?>
			
			<span class="cpt-list-item__date">
				<?php 
					if ($published_date === $modified_date) {
						printf( '%s %s', __( 'Published', 'weadapt-can' ), $published_date );
					} 
					else {
						printf( '%s %s', __( 'Last updated', 'weadapt-can' ), $modified_date );
					}
				?>
			</span>
		</div>

		<?php the_post_excerpt_html( $post_ID ); ?>

		<?php echo get_button( [
			'url'    => get_permalink( $post_ID ),
			'title'  => $post_type === 'article' ? __( 'Read Article', 'weadapt-can' ) : __( 'Read News', 'weadapt-can' ),
			'target' => '',
		], 'icon-small', '', 'icon-arrow-right-button' ); ?>
	</div>
</article>

<?php endif; ?>