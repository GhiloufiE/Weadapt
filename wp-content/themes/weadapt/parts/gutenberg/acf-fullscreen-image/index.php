<?php
/**
 * Block Image Text
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */

$block_object = new Block( $block );
$attr = $block_object->attr();
$name = $block_object->name();
?>

<section <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>

	<div class="container">
        <?php echo $block_object->title( "{$name}__title", 'h3' ); ?>
        <?php echo $block_object->image( "{$name}__image" ); ?>
    </div>
</section>