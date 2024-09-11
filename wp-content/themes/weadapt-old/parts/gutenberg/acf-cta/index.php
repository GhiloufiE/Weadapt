<?php
/**
 * Block CTA
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$attr         = $block_object->attr( 'background-' . get_field( 'background_color' ) );
$name         = $block_object->name();

$image_position      = get_field( 'image_position' );
$text_alignment      = get_field( 'text_alignment' );
$mobile_column_order = get_field( 'mobile_column_order' );
$form_data           = get_field( 'form_popup' );
$has_popup           = wp_validate_boolean( get_field( 'has_popup' ) );
?>

<section <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>

	<div class="container">
		<div class="row <?php echo esc_attr( $name ); ?>__row alignment-<?php echo $text_alignment; ?> order-<?php echo $mobile_column_order; ?>">
			<?php if ( ! empty( $image = get_field( 'section_image' ) ) ) : ?>
				<div class="col-12 col-md-6 <?php echo esc_attr( $name ); ?>__col <?php echo esc_attr( $name ); ?>__col--image position-<?php echo esc_attr( $image_position ); ?>">
					<div class="<?php echo esc_attr( $name ); ?>__image">
						<?php echo get_img( $image, 'large' ); ?>
					</div>
				</div>
			<?php endif; ?>

			<div class="col-12 <?php echo $image ? 'col-md-6' : 'col-md-8'; ?> <?php echo esc_attr( $name ); ?>__col <?php echo esc_attr( $name ); ?>__col--text">
				<div class="<?php echo esc_attr( $name ); ?>__content">
					<?php
						echo $block_object->subtitle( "{$name}__subtitle" );
						echo $block_object->title( "{$name}__heading", 'h2' );
						echo $block_object->desc( "{$name}__description" );
						echo $block_object->button( '', "{$name}__button", 'icon-arrow-right-button', $has_popup );

						if ( $has_popup ) {
							add_action( 'popup-content', function() use ( $form_data ) {
								echo get_part( 'components/popup/index', [ 'template' => 'form-popup', 'form_data' => $form_data ] );
							} );
						}
					?>
				</div>
			</div>
		</div>
	</div>
</section>