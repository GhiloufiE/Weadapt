<?php
/**
 * Block RichText
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$attr = $block_object->attr( 'background-' . esc_attr( get_field( 'background_color' ) ) );
$name = $block_object->name();

?>

<section <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-heading/', 'core-heading'); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-paragraph/', 'core-paragraph'); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-button/', 'core-button'); ?>

	<div class="container">
		<div class="<?php echo esc_attr( $name ); ?>__content">
			<?php
				echo $block_object->desc( "{$name}__description" );
			?>
		</div>
</section>