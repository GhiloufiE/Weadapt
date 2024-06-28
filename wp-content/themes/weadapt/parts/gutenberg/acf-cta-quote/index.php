<?php
/**
 * Block Quote
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$attr = $block_object->attr( 'background-' . get_field( 'background' ) . ' title-' . get_field( 'title_size' ) );
$name = $block_object->name();

?>

<div <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>

	<?php if ( ! empty( $title = get_field( 'section_title' ) ) ) : ?>
		<blockquote class="cta-quote__blockquote">
			<?php echo esc_html( $title); ?>
		</blockquote>
	<?php endif; ?>

	<?php echo $block_object->button('', 'has-icon-left', 'icon-share'); ?>
</div>
