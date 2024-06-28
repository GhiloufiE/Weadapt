<?php

/**
 * Output Post Edit Buttons HTML
 */
function the_post_edit_buttons_html( $post_ID = 0, $post_status = '' ) {
	if ( get_query_var( 'theme_show_buttons' ) ) {
		?><div class="cpt-list-item__buttons"><?php

		?>
			<div class="wp-block-button wp-block-button--template  has-icon-left is-style-outline">
				<button class="wp-block-button__link wp-block-button__duplicate" data-id="<?php echo intval( $post_ID ); ?>" data-nonce="<?php echo wp_create_nonce( 'duplicate_' . $post_ID ); ?>">
					<?php echo __( 'Duplicate', 'weadapt' ); ?>
					<?php echo get_img( 'icon-post-duplicate' ); ?>
					<?php echo get_img( 'loader' ); ?>
				</button>
			</div>
		<?php

		echo get_button( [
			'url'    => get_edit_post_link( $post_ID ),
			'title'  => __( 'Edit article', 'weadapt' ),
			'target' => '_blank',
		], '', 'has-icon-left', 'icon-post-edit' );

		?></div><?php
	}
}