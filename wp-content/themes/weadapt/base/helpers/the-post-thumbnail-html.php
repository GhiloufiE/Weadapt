<?php

/**
 * Output Post Thumbnail HTML
 */

function the_post_thumbnail_html( $post_ID = 0, $size = 'cpt-list-item' ) {
	if ( has_post_thumbnail( $post_ID ) ) : ?>
		<div class="cpt-list-item__image">
			<a href="<?php
				if ( 'draft' === get_post_status() ) {
					echo get_edit_post_link( $post_ID );
				}
				else {
					the_permalink( $post_ID );
				}
			?>" class="cpt-list-item__image-link">
				<?php
					$low_quality = false;
					if(isset( $_COOKIE['weadapt-low-quality-images'] ) && wp_validate_boolean( $_COOKIE['weadapt-low-quality-images'] )) {
						$low_quality = $_COOKIE['weadapt-low-quality-images'];
					}
					if($low_quality) {
						echo get_the_post_thumbnail( $post_ID, 'thumbnail' );
					} else {
						the_post_thumbnail( $post_ID, $size );
					}
				?>
			</a>
		</div>
	<?php endif;
}
