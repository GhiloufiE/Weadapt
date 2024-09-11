<?php
/**
 * Block Image Text
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$attr = $block_object->attr( 'background-' . esc_attr( get_field( 'background_color' ) ) );
$name = $block_object->name();

$image_position = get_field( 'image_position' );
$row_alignment  = get_field( 'row_alignment' );
$hide_button_icon    = get_field( 'button_icon' );
$subtitle = get_field('subtitle');
?>
<style>
.section-subtitle{
		color: #555;
    font-size: 13px;
    margin-right: 10rem;
	margin-top: 2rem;
	text-align: -webkit-center;
}
	.fullwidth-col-1{
	padding: 0 !important;
    max-width: calc(1184px - 50%)!important ;
	align-self: center;
	text-align: -webkit-center;
	}
	
	
</style>
<section <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>

		<div class="row <?php echo esc_attr( $name ); ?>__row alignment-<?php echo esc_attr( $row_alignment ); ?>">
			<div class="col-12 col-md-6 <?php echo esc_attr( $name ); ?>__col <?php echo esc_attr( $name ); ?>__col--image <?php echo esc_attr( $image_position); ?>">
				<?php echo $block_object->image( "{$name}__image" );echo $block_object->subtitle( "{$name}__image" ); ?>
			</div>

			<div class="fullwidth-col-1 col-12 container col-lg-6  <?php echo esc_attr( $name ); ?>__col <?php echo esc_attr( $name ); ?>__col--text">
				<div class=" container <?php echo esc_attr( $name ); ?>__content">
					<?php
						if ( ! empty( $icon_ID = get_field( 'icon' ) ) ) : ?>
							<div class="<?php echo esc_attr( $name ); ?>__icon">
								<?php echo get_img( $icon_ID ); ?>
							</div>
						<?php endif; 

						echo $block_object->title( "{$name}__heading", 'h2' );
						echo $block_object->desc( "{$name}__description" );
						echo $block_object->button( '', "{$name}__button", $hide_button_icon ? '' : 'icon-arrow-right-button' );
					?>
				</div>
			</div>
		</div>
</section>