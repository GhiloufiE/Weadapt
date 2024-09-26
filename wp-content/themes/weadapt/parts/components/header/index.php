<?php load_blocks_script( 'header', 'weadapt/header', ['google-translate'] ); ?>

<header class="main-header main-header--mobile" tabindex="-1">
	<?php load_inline_styles_shared( 'forms' ); ?>
	<?php load_inline_styles( __DIR__, 'header' ); ?>

	<?php echo get_part( 'components/header/parts/mobile' ); ?>
</header>

<header class="main-header main-header--desktop">
	<?php echo get_part( 'components/header/parts/top' ); ?>
	<?php echo get_part( 'components/header/parts/main' ); ?>
</header>



<?php
	$popup_text = get_field( 'popup_text', 'options' );

	if ( ! empty( $popup_text ) && empty( $_COOKIE['message-popup'] ) ) : ?>
		<div class="message-popup">
			<div class="container">
				<div class="message-popup__text">
					<?php echo esc_html( $popup_text ); ?>
				</div>

				<button class="message-popup__close">
					<?php echo get_img( 'icon-close' ); ?>
				</button>
			</div>
		</div>
	<?php endif
?>
