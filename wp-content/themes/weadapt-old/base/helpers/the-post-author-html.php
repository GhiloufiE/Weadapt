<?php

/**
 * Output Post Author HTML
 */
function the_post_author_html( $authors = [], $author_class = '', $post_type = '', $add_html = '' ) {
	$add_text = '';

	if ( ! empty( $authors ) ) :
	?>
		<div class="<?php echo $author_class; ?>">
			<div class="cpt-list-item__author-image">
				<?php echo $add_text; ?>
				<?php foreach( $authors as $i => $contributor ) : ?>
					<a style="z-index:<?php echo count( $authors ) - $i; ?>;" href="<?php echo get_author_posts_url( $contributor ); ?>">
						<?php echo get_avatar( $contributor, 80 ); ?>
					</a>
				<?php endforeach; ?>
				<span class="sep"></span>
				<div class="cpt-list-item__author-info">
					<?php if ( count( $authors ) > 1 ) : ?>
						<span><?php _e( 'Multiple Authors', 'weadapt' ); ?></span>
					<?php else: ?>
						<a href="<?php echo get_author_posts_url( $authors[0] ); ?>" class="cpt-list-item__link">
							<?php
								if ( get_query_var( 'theme_is_author_page' ) ) {
									if ( $authors[0] == get_current_user_id() ) {
										echo __( 'You', 'weadapt' );
									}
									else {
										echo get_user_name( $authors[0] );
									}
								}
								else {
									echo get_user_name( $authors[0] );
								}
							?>
						</a>

						<?php
							if ( ! empty( $post_type ) ) {
								switch( get_post_status() ) {
									case 'pending':
										_e( 'Submitted an', 'weadapt' );
										break;

									case 'draft':
									case 'future':
									case 'private':
										_e( 'Saved an', 'weadapt' );
										break;

									default:
										_e( 'Published in', 'weadapt' );
										break;
								}

								$output = ' ' . '<span>'. ucfirst( $post_type ) . '</span>';

								if ( 'forum' === $post_type ) {
									$post_forum = get_field( 'forum' );

									if ( ! empty( $post_forum ) ) {
										$output = sprintf( ' <a href="%s" class="cpt-list-item__link">%s</a>',
											get_permalink( $post_forum ),
											get_the_title( $post_forum )
										);
									}
								}
								else {
									$template_ID = get_page_id_by_template( $post_type );

									if ( ! empty( $template_ID ) ) {
										$output = sprintf( ' <a href="%s" class="cpt-list-item__link">%s</a>',
											get_permalink( $template_ID ),
											ucfirst( $post_type )
										);
									}
								}

								echo $output;
							}
						?>
					<?php endif; ?>
					<?php
						if ( ! empty( $add_html ) ) {
							echo wp_kses_post( $add_html );
						}
					?>
				</div>
			</div>
		</div>
	<?php
	endif;
}
