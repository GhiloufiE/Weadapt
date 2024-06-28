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

$network_Id = get_field('network');
$hide_button_icon    = get_field( 'button_icon' );
?>

<section <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>

	<div class="container">
		<div class="row <?php echo esc_attr( $name ); ?>__row ">
			<div class="col-12 col-md-8 <?php echo esc_attr( $name ); ?>__col <?php echo esc_attr( $name ); ?>__col--text">
				<div class="<?php echo esc_attr( $name ); ?>__content">
					<?php
						echo $block_object->title( "{$name}__heading", 'h2' );
						echo $block_object->desc( "{$name}__description" );
					?>
				</div>
			</div>
			<div class="col-12 col-md-4 network">
			    <div class="network__image-container"  >
			        <div class="network__image" >
                        <?php echo get_the_post_thumbnail( $network_Id ); ?>
                    </div>
			    </div>
			    <a href="<?php  echo get_permalink( $network_Id ); ?>" class="network__title"><?php echo 'Join the Network ' . get_the_title( $network_Id );; ?></a>
			    <p class="network__caption" >Join to share content with Network Members</p>
			    <p class="network__excerpt" ><?php   echo  get_the_excerpt($network_Id) ; ?></p>
                <div class="wp-block-button wp-block-button--template ">
                    <a href="<?php  echo get_permalink( $network_Id ); ?>" class="wp-block-button__link" >Join now <?php echo get_img('icon-arrow-right-button'); ?></a>
                </div>
            </div>
		</div>
	</div>
</section>
