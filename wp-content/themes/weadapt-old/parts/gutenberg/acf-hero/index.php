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
$image           = $block_object->image( 'hero__image' );

$attr_classes = '';
$attr_classes .= ! empty( $image ) ? ' has-image' : '';
$attr_classes .= ! empty( $search_bar) ? ' has-search-bar' : '';
$attr_classes .= ! empty( $text_size) ? ' title-' . $text_size : ' title-small';

$attr = $block_object->attr( $attr_classes );
?>

<section <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-heading/', 'core-heading'); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-paragraph/', 'core-paragraph'); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-button/', 'core-button'); ?>

	<div class="container hero__container">
		<div class="hero__row row">
			<div class="col-12 hero__col <?php echo ( ! empty( $image ) ) ? 'col-lg-6' : '' ?>">
				<?php echo $block_object->subtitle( 'hero__subtitle', ); ?>
				<?php echo $block_object->title( 'hero__heading', 'h1' ); ?>
				<?php echo $block_object->desc( 'hero__description' ); ?>
				<?php
					if ( ! empty( $search_bar ) ) :
						$args = [
							'placeholder' => __( 'I would like to find out about...', 'weadapt' )
						];

						get_part('components/search-panel/index', $args);
					endif;
				?>
				<?php echo $block_object->button(); ?>
			</div>

			<?php if ( ! empty( $image ) ) : ?>
				<div class="col-12 col-lg-6 hero__col alignment-<?php echo esc_attr( $image_alignment ); ?>">
					<?php echo $image; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
