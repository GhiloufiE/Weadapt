<?php
/**
 * Single references
 *
 * @package WeAdapt
 */
?>

<div class="single-references">
	<?php load_inline_styles( __DIR__, 'single-references' ); ?>
	<?php load_blocks_script( 'single-references', 'weadapt/single-references' ); ?>

	<?php if ( ! empty( $references_list = get_field( 'links_list' ) ) ) : ?>
		<ul class="single-references__list">
			<?php foreach ( $references_list as $references_item ) : ?>
				<?php if ( ! empty( $url = $references_item['url'] ) && ! empty( $description = $references_item['description'] ) ) : ?>
					<li class="single-references__item">
						<a href="<?php echo esc_url( $url ); ?>" class="single-references__item-text"><?php echo esc_html( $description ); ?></a>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<div class="single-references__actions">
		<?php
			$post_ID = get_the_ID();

			get_part( 'components/button-share/index', [
				'url'   => get_permalink( $post_ID ),
				'type'  => get_post_type( $post_ID ),
				'class' => 'is-style-secondary has-icon-left',
				'icon'  => 'icon-share',
			] );
		?>
	</div>
</div>