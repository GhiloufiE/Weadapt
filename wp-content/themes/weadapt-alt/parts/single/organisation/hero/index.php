<?php
/**
 * Single Hero template for Adaptation at Altitude
 *
 * @package    WordPress
 * @subpackage weadapt
 * @since      weadapt 1.0
 */

$post_ID = isset( get_queried_object()->term_id ) ? get_queried_object()->term_id : get_the_ID();
$title   = get_the_title();
$content = get_the_content();
$address = get_field( 'address', "$post_ID" );
$country = $address['country'];

if ( ! empty( $image_ID = get_field( 'image' ) ) ) {
	$thumb_ID      = $image_ID;
	$thumb_caption = apply_filters( 'wp_get_attachment_caption', get_post_field( 'post_excerpt', $image_ID ), $image_ID );
}

if ( empty( $thumb_ID && has_post_thumbnail() ) ) {
	$thumb_ID      = get_post_thumbnail_id();
	$thumb_caption = get_the_post_thumbnail_caption();
}
?>

<section class="single-organisation-hero">
	<?php load_inline_styles( __DIR__, 'hero' ); ?>
	<?php load_inline_styles( '/parts/components/single-hero', 'single-hero' ); ?>
	<?php load_blocks_script( 'single-hero', 'weadapt/single-hero' ); ?>

	<div class="single-hero__container container">
		<div class="single-hero__row row single-hero__row_top">
			<div class="single-hero__left">
				<div class="single-hero__left-inner">
					<h1 class="single-hero__title" id="main-heading">
						<?php echo $title; ?>
					</h1>
					<div class="single-hero__excerpt">
						<?php echo $content; ?>
					</div>
					<?php if( !empty($country) ) : ?>
						<div class="single-hero__country">
							<span class="single-hero__country-address">
								<?php echo esc_html__( 'Address:', 'weadapt' ); ?>
							</span>
							<span class="single-hero__country-country">
								<?php echo $country; ?>
							</span>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<div class="single-hero__right">
				<?php if ( ! empty( $thumb_ID ) ) : ?>
					<figure class="img-caption">
						<?php echo get_img( $thumb_ID ); ?>

						<?php if ( ! empty( $thumb_caption ) ) : ?>
							<figcaption class="img-caption__caption"><?php echo wp_kses_post( $thumb_caption ); ?></figcaption>
						<?php endif; ?>
					</figure>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>