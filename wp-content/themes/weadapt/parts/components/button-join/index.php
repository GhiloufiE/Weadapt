<?php

global $post;

load_blocks_script( 'button-join', 'weadapt/button-join' );

$title        = ! empty( $args['title'] ) ? $args['title'] : __( 'Join', 'weadapt' );
$unjoin_title = ! empty( $args['unjoin_title'] ) ? $args['unjoin_title'] : __( 'Unsubscribe', 'weadapt' );
$join_ID      = ! empty( $args['join_ID'] ) ? $args['join_ID'] : $post->ID;
$join_type    = ! empty( $args['join_type'] ) ? $args['join_type'] : get_post_type( $join_ID );
$class        = 'wp-block-button wp-block-button--template';
$link_class   = 'wp-block-button__link';
$text         = $title;

if ( ! empty( $args['style'] ) ) {
	$class .= sprintf( ' is-style-%s', $args['style'] );
}
if ( ! empty( $args['class'] ) ) {
	$class .= sprintf( ' %s', $args['class'] );
}

if ( is_user_logged_in() ) :
	$link_class .= ' button-join';

	if ( is_user_joined( $join_ID, $join_type ) ) {
		$link_class .= ' is-joined';
		$text       = $unjoin_title;
	}
?>
<div class="<?php echo esc_attr( $class ); ?>">
	<button class="<?php echo esc_attr( $link_class ); ?>"
		data-id="<?php echo esc_attr( $join_ID ); ?>"
		data-type="<?php echo esc_attr( $join_type ); ?>"
		data-join-title="<?php echo esc_attr( $title ); ?>"
		data-unjoin-title="<?php echo esc_attr( $unjoin_title ); ?>">
	<?php
		echo sprintf( '<span>%s</span>', esc_html( $text ) );

		if ( ! empty( $args['icon'] ) ) {
			if ( is_array( $args['icon'] ) ) {
				foreach ( $args['icon'] as $icon ) {
					echo get_img( $icon );
				}
			}
			else {
				echo get_img( $args['icon'] );
			}
		}
	?>
	</button>
</div>
<?php

else:
?>
<div class="<?php echo esc_attr( $class ); ?>">
	<button class="<?php echo esc_attr( $link_class ); ?>" data-popup="sign-in">
	<?php
		echo sprintf( '<span>%s</span>', esc_html( $text ) );

		if ( ! empty( $args['icon'] ) ) {
			if ( is_array( $args['icon'] ) ) {
				foreach ( $args['icon'] as $icon ) {
					echo get_img( $icon );
				}
			}
			else {
				echo get_img( $args['icon'] );
			}
		}
	?>
	</button>
</div>
<?php
endif;