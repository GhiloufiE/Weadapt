<?php
/**
 * Hero Block
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$name         = $block_object->name();

$image_alignment = get_field( 'image_alignment' );
$search_bar      = get_field( 'search_bar' );
$text_size       = get_field( 'title_size');
$image           = $block_object->image( 'hero-read-more__image' );

$content_more = ! empty( get_field('content_more') ) ? get_field('content_more') : '';
$read_more_label = ! empty( get_field('read_more_label') ) ? get_field('read_more_label') : _e( 'Read more', 'weadapt' );

$attr_classes = '';
$attr_classes .= ! empty( $image ) ? ' has-image' : '';
$attr_classes .= ! empty( $search_bar) ? ' has-search-bar' : '';
$attr_classes .= ! empty( $text_size) ? ' title-' . $text_size : ' title-small';

$attr = $block_object->attr( $attr_classes );
?>

<section <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>
	<div class="container <?php echo $name; ?>__container">
		<div class="<?php echo $name; ?>__row row">
			<div class="col-12 <?php echo $name; ?>__col <?php echo ( ! empty( $image ) ) ? 'col-lg-6' : '' ?>">
				<?php echo $block_object->subtitle( 'hero__subtitle', ); ?>
				<?php echo $block_object->title( 'hero__heading', 'h1' ); ?>
				<?php echo $block_object->desc( 'hero__description' ); ?>

				<?php if ( ! empty( $content_more ) ) : ?>
					<div class="wp-block-button <?php echo $name; ?>__more">
						<div class="section-description <?php echo $name; ?>__more-content <?php echo $name; ?>__description">
							<?php echo wp_kses_post( $content_more ); ?>
						</div>
						<button class="<?php echo $name; ?>__more-btn wp-block-button__link">
							<span class="open-label"><?php echo $read_more_label ?></span>
                            <span class="close-label"><?php _e( 'Read Less', 'weadapt' ); ?></span>
							<?php echo get_img( 'icon-chevron-down' ); ?>
						</button>
					</div>
				<?php endif; ?>

				<?php
					if ( ! empty( $search_bar ) ) :
						$args = [
							'placeholder' => __( 'I would like to find out about...', 'weadapt' )
						];

						get_part('components/search-panel/index', $args);
					endif;
				?>
			</div>

			<?php if ( ! empty( $image ) ) : ?>
				<div class="col-12 col-lg-6 <?php echo $name; ?>__col alignment-<?php echo esc_attr( $image_alignment ); ?>">
					<?php echo $image; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
