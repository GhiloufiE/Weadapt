<?php
/**
 * User Messages template.
 *
 * @package    WordPress
 * @subpackage weadapt
 * @since      weadapt 1.0
 */

$user_ID = ! empty( $args['user_ID'] ) ? intval( $args['user_ID'] ) : 0;

add_action( 'popup-content', function() {
	echo get_part( 'components/popup/index', [ 'template' => 'messages' ] );
	echo get_part( 'components/popup/index', [ 'template' => 'messages-new' ] );
} );
?>

<?php load_inline_styles( __DIR__, 'user-messages' ); ?>

<div class="messages">
	<?php
		wp_enqueue_script( 'fep-form-submit' );

		if ( fep_get_option( 'block_other_users', 1 ) ) {
			wp_enqueue_script( 'fep-block-unblock-script' );
		}

		load_blocks_script( 'user-messages', 'weadapt/user-messages' );
	?>

	<?php
		$box_content = Fep_Messages::init()->user_messages();

		require_once( fep_locate_template( 'box-message.php' ) );
	?>
</div>

<?php // echo do_shortcode( '[front-end-pm fepaction="messagebox" fep-filter="show-all"]' ); ?>