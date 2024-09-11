<?php

$set_class       = isset( $args[ 'set_class'] ) ? $args['set_class'] : '';
$link_target     = isset( $args['link_target'] ) ? $args['link_target'] : '_self';
$link_title      = isset( $args['link_title'] ) ? $args['link_title'] : 'Button';
$link_url        = isset( $args['link_url'] ) ? $args['link_url'] : '#';
$link_attributes = isset( $args['link_attributes'] ) ? $args['link_attributes'] :  [];

$name          = 'button';
$button_class  = 'wp-block-button__link';
$wrapper_class = 'wp-block-button wp-block-button--template';

! empty( $set_class ) && $wrapper_class .= "  $set_class";

( isset( $args['style'] ) && ! empty( $args['style'] ) ) && $wrapper_class .= ' is-style-' . $args['style'];

$attributes = '';

if ( ! empty( $link_attributes ) ) {
	foreach ( $link_attributes as $attribute => $value ) {
		$attributes .= "$attribute='$value' ";
	}
}

load_blocks_script( 'button', 'weadapt/button' );
load_inline_dependencies( '/parts/gutenberg/core-button/', 'core-button');
?>
<div class="<?php echo $wrapper_class; ?>">
	<a class="<?php echo $button_class; ?>" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"<?php echo ! empty( $args['is_popup'] ) ? ' data-popup' : ''; ?> <?php echo $attributes; ?>>
		<?php
			echo $link_title;

			if ( ! empty( $args['icon'] ) ) {
				echo get_img( $args['icon'] );
			}
		?>
	</a>
</div>