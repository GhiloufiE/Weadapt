<?php
/**
 * Single tabs nav
 *
 * @package WeAdapt
 */

$items = ! empty( $args['items'] ) ? $args['items'] : [];

if ( ! empty( $items ) ) :
?>

<nav class="single-tabs-nav" aria-label="Page Content Tabs" role="tablist">
	<?php
		load_blocks_script( 'single-tabs-nav', 'weadapt/single-tabs-nav', ['swiper'] );
		load_inline_styles_plugin( 'swiper-bundle.min' );
		load_inline_styles( __DIR__, 'single-tabs-nav' );
	?>

	<div class="swiper">
		<ul class="swiper-wrapper">
			<?php foreach ( $items as $item ) : ?>
				<li class="swiper-slide" role="presentation">
					<button class="single-tabs-nav__btn" role="tab" aria-selected="<?php echo $item['selected'] ? 'true' : 'false'; ?>" id="<?php echo $item['id']; ?>" aria-controls="<?php echo $item['controls']; ?>"<?php
						if ( ! empty( $item['attributes'] ) ) {
							foreach ($item['attributes'] as $attr_key => $attr_value) {
								echo sprintf( '%s="%s"', esc_attr( $attr_key ), esc_attr( $attr_value ) );
							}
						}
					?>><?php echo $item['label']; ?></button>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</nav>

<?php endif; ?>