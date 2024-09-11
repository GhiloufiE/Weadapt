<?php
/**
 * Newsletter Card Block
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

    <?php 
        echo $block_object->image( "{$name}__image" );
        echo $block_object->title( "{$name}__heading", 'h2' );
        echo $block_object->desc( "{$name}__description" );
        echo $block_object->button( '', "{$name}__button", 'icon-arrow-right-button', true );

        

        add_action( 'popup-content', function() {
            echo get_part( 'components/popup/index', [ 'template' => 'newsletter' ] );
        } );
    ?>
</section>