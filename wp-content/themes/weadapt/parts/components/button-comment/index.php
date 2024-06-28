<?php
load_inline_styles( __DIR__, 'button-comment' );
load_blocks_script( 'button-comment', 'weadapt/button-comment' );
wp_enqueue_script( 'comment-reply' );

$post_ID    = ! empty( $args['post_ID'] ) ? $args['post_ID'] : 0;
$class      = 'wp-block-button wp-block-button--template has-icon-left';
$link_class = 'wp-block-button__link';

add_action( 'popup-content', function() {
	echo get_part( 'components/popup/index', [ 'template' => 'comments' ] );
} );

?>
<div class="<?php echo esc_attr( $class ); ?>">
	<button class="<?php echo esc_attr( $link_class ); ?>" data-popup="comments">
	<?php
		
		$comments_count = wp_count_comments( get_the_ID() );
		$comments_html  = $comments_count->approved > 0 ? sprintf( ' (%s)', number_format_i18n( $comments_count->approved ) ) : '';
		if ( 'forum' === $post_type  ) {
            echo sprintf( '<span>%s%s</span>', esc_html__( 'Reply', 'weadapt' ), esc_attr( $comments_html ) );
        } else {
            echo sprintf( '<span>%s%s</span>', esc_html__( 'Comment', 'weadapt' ), esc_attr( $comments_html ) );
        }
		echo get_img( 'icon-chat-20-light' );
	?>
	</button>
</div>