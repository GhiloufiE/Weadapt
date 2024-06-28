<?php

/**
 * Output Post Title HTML
 */

function the_post_title_html( $post_ID = 0, $contributors = [] ) {
	$title = get_the_title( $post_ID );

	if ( empty( $title ) ) return;

	$title_class = 'cpt-list-item__title';
	$show_icon   = apply_filters( 'list_template_show_title_icon', true ) && count( $contributors ) > 1;

	if ( $show_icon ) {
		$title_class  .= ' cpt-list-item__title--with-icon';
	}

	?>
		<h4 class="<?php echo esc_attr( $title_class ); ?>">
			<a href="<?php
				if ( 'draft' === get_post_status() ) {
					echo get_edit_post_link( $post_ID );
				}
				else {
					the_permalink( $post_ID );
				}
			?>" class="cpt-list-item__link">
				<?php if ( $show_icon ) : ?>
					<span class="cpt-list-item__title-icon">
						<?php echo get_img( 'icon-link-square' ); ?>
					</span>
				<?php endif; ?>

				<?php echo wp_kses_post( $title ); ?>
			</a>
		</h4>
	<?php
}