<?php

/**
 * Output Post Status HTML
 */
function the_post_status_html( $post_status = '' ) {
	if ( get_query_var( 'theme_is_author_page' ) ) {
		$output        = '';
		$output_mesage = '';

		switch ( $post_status ) {
			case 'pending':
				$output_mesage = 'Submitted for approval';
				break;

			case 'draft':
			case 'future':
			case 'private':
				$output_mesage = ucfirst( $post_status );
				break;
		}

		if ( ! empty( $output_mesage ) ) {
		?>
			<div class="cpt-list-item__status__wrap">
				<div class="cpt-list-item__status">
					<?php echo get_img( 'icon-alert-circle' ); ?>
					<?php echo $output_mesage; ?>
				</div>
			</div>
		<?php
		}
	}
}