<?php
	load_blocks_script( 'sign-up', 'weadapt/sign-up', [ 'password-strength-meter' ] );

	$template_name = ! empty( $args['template'] ) ? esc_attr( $args['template'] ) : '';
?>

<form class="ajax-form ajax-form--<?php echo esc_attr( $template_name ); ?>" method="POST">
	<?php load_inline_styles_shared( 'forms' ); ?>
	<?php load_inline_styles( __DIR__, 'sign-up' ); ?>

	<?php echo get_part( "components/sign-up/parts/$template_name" ); ?>
</form>