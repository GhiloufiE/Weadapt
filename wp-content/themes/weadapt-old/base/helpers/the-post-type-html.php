<?php

/**
 * Output Post Type HTML
 */

function the_post_type_html( $post_ID = 0 ) {
	?>
		<div class="cpt-list-item__post-type">
			<span><?php echo ucfirst( get_post_type( $post_ID ) ); ?></span>
		</div>
	<?php
}