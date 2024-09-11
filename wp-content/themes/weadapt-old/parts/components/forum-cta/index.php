<?php
/**
 * Forum CTA for Theme/Network
 *
 * @package WeAdapt
 */
if ( ! empty( $forum_id = get_post_forum( get_the_ID() ) ) ) :
	$content = get_field( 'subscribe_to_this_discussion', 'options' );

	$title       = ! empty( $content['title'] ) ? $content['title'] : '';
	$description = ! empty( $content['description'] ) ? $content['description'] : '';
?>
<div class="forum-cta">
	<?php load_inline_styles( __DIR__, 'forum-cta' ); ?>

	<?php if ( $title ) : ?>
		<h2 class="forum-cta__title"><?php echo get_img( 'icon-comment' ) . wp_kses_post( $title ); ?></h2>
	<?php endif; ?>

	<?php if ( $description ) : ?>
		<div class="forum-cta__content"><?php echo wp_kses_post( $description ); ?></div>
	<?php endif; ?>

	<?php echo get_button( [
		'url'    => get_permalink( $forum_id ),
		'title'  => __( 'Join', 'weadapt' ),
	] ); ?>
</div>
<?php
endif;