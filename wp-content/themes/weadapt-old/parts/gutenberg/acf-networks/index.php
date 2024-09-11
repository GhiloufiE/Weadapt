<?php
/**
 * Block Image Text
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$primary_bg = get_field('primary_color_bg') ? 'primary-bg' : '';
$attr = $block_object->attr( 'background-' . esc_attr( get_field( 'background_color' ) . ' ' . $primary_bg ) );
$name = $block_object->name();

?>

<section <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>


    <div class="container">
        <?php if( ! empty( $title = get_field('title') ) ) : ?>
            <div class="<?php echo $name;?>__heading" ><?php echo $title; ?></div>
        <?php endif; ?>

        <?php if( ! empty( $networks = get_field('networks') ) ) : ?>
            <div class="row <?php echo $name;?>__content">
                <?php foreach( $networks as $network_id ) : ?>
                    <div class="col-12 col-md-6" >
                        <div class="<?php echo $name;?>__card" >
                            <div class="<?php echo $name;?>__image-container ">
                              <div class="<?php echo $name;?>__image" ><?php echo get_the_post_thumbnail( $network_id, 'full' ); ?></div>
                            </div>

                            <div class="<?php echo $name;?>__card-heading">
                                 <?php echo get_the_title($network_id); ?>
                            </div>

                           <div class="<?php echo $name;?>__description" >
                                <?php echo get_the_excerpt($network_id); ?>
                           </div>

                           <div class="wp-block-button wp-block-button--template <?php echo $name; ?>__button" >
                               <a
                               href="<?php echo get_permalink( $network_id ); ?>"
                               class="wp-block-button__link"
                               > View Community of Practice <?php echo get_img('icon-arrow-right-button'); ?></a>
                           </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>



</section>
