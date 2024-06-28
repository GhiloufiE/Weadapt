<?php

$post_ID = ! empty( $args['post_ID'] ) ? $args['post_ID'] : 0;

if ( ! empty( $post_ID ) ) :
	$post_meta = [
		['icon-user', get_members_count( $post_ID )]
	];

	$add_class = ( 'draft' === get_post_status( $post_ID ) ) ? ' is-draft' : '';
?>

<article class="cpt-list-item theme-list-item theme-list-item--organisation<?php echo esc_attr( $add_class ); ?>">
	<?php the_post_thumbnail_html( $post_ID, 'medium' ); ?>

	<div class="cpt-list-item__content">
		<?php
			the_post_title_html( $post_ID );
			the_post_excerpt_html( $post_ID );
			the_post_meta_html( $post_meta );
		?>

		<div class="cpt-list-item__actions">
			<?php echo get_button( [
				'url' => get_permalink( $post_ID ),
				'title' => __( 'Find out More', 'weadapt' ),
				'target' => '',
			], 'outline' ); ?>
		</div>
	</div>
</article>

<?php endif; ?>