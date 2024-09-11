<?php
/**
 * CTA
 *
 * @package WeAdapt
 */
$name = 'cta';
$link = [
	'url' => '#',
	'title' => 'Contribute Now',
	'target' => '',
];
$image = get_img( 'cta-3', 'cta-thumbnail', '/assets/images/temp/' );
?>
<section class="<?php echo esc_attr( $name ); ?> background-light block-spacing--pt block-spacing--pb">
	<?php echo load_inline_dependencies( '/parts/gutenberg/acf-cta/', $name ); ?>
	<div class="container">
		<div class="row <?php echo esc_attr( $name ); ?>__row alignment-right order-text">
			<div class="col-12 col-md-6 <?php echo esc_attr( $name ); ?>__col <?php echo esc_attr( $name ); ?>__col--image">
				<?php if ( ! empty( $image ) ) : ?>
					<div class="<?php echo esc_attr( $name ); ?>__image">
						<?php echo $image; ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="col-12 col-md-6 <?php echo esc_attr( $name ); ?>__col <?php echo esc_attr( $name ); ?>__col--text">
				<div class="<?php echo esc_attr( $name ); ?>__content">
					<h2 class="section-title cta__heading">Contribute now</h2>
					<div class="section-description cta__description">
						<p>Discover climate adaptation projects on a global map, browsable by theme or network, alongside downscaled climate station data.</p>
					</div>
					<?php echo get_button($link, '', "{$name}__button", 'icon-arrow-right-button'); ?>
				</div>
			</div>
		</div>
	</div>
</section>