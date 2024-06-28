<?php
/**
 * Single Hero template.
 *
 * @package    WordPress
 * @subpackage weadapt
 * @since      weadapt 1.0
 */

$type    = isset( $args['type'] ) ? $args['type'] : '';
$title   = get_the_title();
$post_ID = isset( get_queried_object()->term_id ) ? get_queried_object()->term_id : get_the_ID();

$thumb_ID         = 0;
$thumb_caption    = '';

if ( ! empty( $image_ID = get_field( 'image' ) ) ) {
	$thumb_ID      = $image_ID;
	$thumb_caption = apply_filters( 'wp_get_attachment_caption', get_post_field( 'post_excerpt', $image_ID ), $image_ID );
}

if ( empty( $thumb_ID && has_post_thumbnail() ) ) {
	$thumb_ID      = get_post_thumbnail_id();
	$thumb_caption = get_the_post_thumbnail_caption();
}

$date_format = 'jS M Y';

$post_meta_items = [
	[ __( 'Submitted by', 'weadapt-can' ), '' ],
	[ __( 'Published', 'weadapt-can' ), get_the_date( $date_format, $post_ID ) ],
	[ __( 'Last updated', 'weadapt-can' ), get_the_modified_date( $date_format, $post_ID ) ],
];
?>

<section class="single-hero">
	<?php load_inline_styles( __DIR__, 'single-hero' ); ?>

	<div class="single-hero__container container">
		<div class="single-hero__row row <?php echo empty( $thumb_ID ) ? 'single-hero__row_top' : ''; ?>">
			<div class="single-hero__left">
				<div class="single-hero__left-inner">
					<?php if ( $type === 'blog' ) : ?>
						<h2 class="single-hero__subtitle"><?php _e( 'News', 'can-adapt' ); ?> /</h2>
					<?php else: ?>
						<h2 class="single-hero__subtitle"><?php echo ucfirst( get_post_type( $post_ID ) ); ?> /</h2>
					<?php endif; ?>

					<h1 class="single-hero__title" id="main-heading"><?php echo $title; ?></h1>

					<?php if ( ! empty( $post_meta_items ) ) : ?>
						<ul class="post-meta single-hero__meta">
							<?php foreach ( $post_meta_items as $item ) : ?>
								<li class="post-meta__item">
									<span class="text"><?php printf( '%s %s', $item[0], $item[1] )?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			</div>

			<div class="single-hero__right">
				<?php if ( ! empty( $thumb_ID ) ) : ?>
					<figure class="single-hero__image img-caption">
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