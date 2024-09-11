<?php
/**
 * Columns With Text
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$attr = $block_object->attr();
$name = $block_object->name();

$columns_text = get_field( 'columns_text' );
?>

<section <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>

	<div class="container">
		<?php echo $block_object->title( "{$name}__heading" ); ?>

		<?php if ( ! empty( $columns_text ) ) : ?>
			<div class="row <?php echo esc_attr( $name ) ?>__row">
				<?php foreach ( $columns_text as $column_text ) :
					if ( ! empty( $column_text ) ) : ?>
						<div class="col-12 col-md-6 <?php echo esc_attr( $name ) ?>__col">
							<?php echo $column_text; ?>
						</div>
					<?php endif;
				endforeach; ?>
			</div>
		<?php endif; ?>

		<?php
			$cta_widget = get_field( 'cta_widget' );

			if ( ! empty( $cta_widget ) ) {
				get_part('components/cta-widget/index', [
					'custom_widget' => $cta_widget
				]);
			}
		?>
	</div>
</section>