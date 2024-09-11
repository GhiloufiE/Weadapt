<div class="share">
<?php
	load_inline_styles( __DIR__, 'button-share' );
	load_blocks_script( 'button-share', 'weadapt/button-share' );

	$title = ! empty( $args['title'] ) ? $args['title'] : __( 'Share', 'weadapt' );
	$style = ! empty( $args['style'] ) ? $args['style'] : '';
	$type  = ! empty( $args['type'] ) ? $args['type'] : __( 'page', 'weadapt' );
	$icon  = ! empty( $args['icon'] ) ? $args['icon'] : '';
	$class = ! empty( $args['class'] ) ? $args['class'] . ' share__button' : 'share__button';

	switch ($type) {
		case 'forum':  $type = 'discussion'; break;
		case 'forums': $type = 'forum'; break;
	}

	$url         = ! empty( $args['url'] ) ? $args['url'] : get_home_url();
	$share_title = ! empty( $args['share_title'] ) ? $args['share_title'] : sprintf( __( 'Share this %s', 'weadapt' ), $type );

	echo get_button([
		'url'   => '',
		'title' => $title,
		''
	], $style, $class, $icon);
?>
	<div class="share__content">
		<button class="share__close"><?php echo get_img( 'icon-close' ); ?></button>
		<h4 class="share__title"><?php echo $share_title; ?></h4>

		<ul class="share__buttons">
			<?php foreach ( [
				'facebook'  => 'https://www.facebook.com/sharer/sharer.php?u=%s',
				'twitter'   => 'https://twitter.com/share?url=%s',
				'linkedin'  => 'https://www.linkedin.com/shareArticle?url=%s'
			] as $item_name => $item_url ) :
			?>
				<li class="<?php echo esc_attr( $item_name ); ?>"><a href="<?php echo sprintf( $item_url, $url ); ?>" target="_blank"><?php echo get_img( 'icon-share-' . $item_name ); ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>